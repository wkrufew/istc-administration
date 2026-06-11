<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\Periodo;
use Illuminate\Http\Request;

class ObligacionesController extends Controller
{
    public function resumen(Request $request)
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        $ultimaMatricula  = $user->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();
        $periodoId        = $periodoDeCarrera?->id;

        $periodos = Periodo::whereHas('matriculas', fn($q) => $q->where('user_id', $userId))
            ->orderByDesc('fecha_inicio')
            ->get()
            ->map(fn($p) => ['id' => $p->id, 'descripcion' => $p->description]);

        $deudaActual = 0;
        $totalPagado = 0;

        if ($periodoId) {
            $obs = ObligacionesFinanciera::where('user_id', $userId)
                ->where('periodo_id', $periodoId)
                ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
                ->get();

            $deudaActual = $obs->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
                ->sum(fn($ob) => max(0, $ob->monto_final - ($ob->pagado ?? 0)));
            $totalPagado = $obs->sum(fn($ob) => $ob->pagado ?? 0);
        }

        $saldoCarrera = 0;
        if ($ultimaMatricula?->carrera) {
            $totalHistorico = ObligacionesFinanciera::where('user_id', $userId)
                ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
                ->get()
                ->sum(fn($ob) => $ob->pagado ?? 0);

            $saldoCarrera = max(0, $ultimaMatricula->carrera->costo_carrera - $totalHistorico);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'periodo_actual_id' => $periodoId,
                'periodos'          => $periodos,
                'deuda_actual'      => round((float) $deudaActual, 2),
                'total_pagado'      => round((float) $totalPagado, 2),
                'saldo_carrera'     => round((float) $saldoCarrera, 2),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $paginado = ObligacionesFinanciera::with(['periodo', 'matricula.carrera', 'pagos'])
            ->where('user_id', $userId)
            ->when($request->tipo,       fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->estado,     fn($q) => $q->where('estado', $request->estado))
            ->when($request->periodo_id, fn($q) => $q->where('periodo_id', $request->periodo_id))
            ->orderByRaw("FIELD(estado, 'Pendiente', 'Parcial', 'Vencido', 'Pagado')")
            ->orderBy('fecha_vencimiento')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $paginado->map(fn($ob) => $this->formatObligacion($ob)),
            'meta'    => [
                'current_page' => $paginado->currentPage(),
                'last_page'    => $paginado->lastPage(),
                'total'        => $paginado->total(),
            ],
        ]);
    }

    public function pagar(Request $request, int $id)
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        $obligacion = ObligacionesFinanciera::with(['pagos', 'matricula'])
            ->where('user_id', $userId)
            ->findOrFail($id);

        if ($obligacion->estado === 'Pagado') {
            return response()->json([
                'success' => false,
                'message' => 'Esta obligación ya está pagada.',
            ], 422);
        }

        $request->validate([
            'monto'       => 'required|numeric|min:0.01|max:' . ($obligacion->saldo ?? 0),
            'metodo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia,Deposito,Payphone',
            'referencia'  => 'nullable|string|max:100',
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'comprobante.required' => 'Debe adjuntar el comprobante de pago.',
            'monto.max'            => 'El monto no puede superar el saldo pendiente.',
        ]);

        $matricula     = $obligacion->matricula;
        $nombreLimpio  = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $user->name));
        $identificador = $matricula?->code ?? $user->cedula;

        $nombreArchivo = implode('_', [
            $obligacion->tipo,
            $identificador,
            $nombreLimpio,
            now()->format('Ymd_His'),
        ]) . '.' . $request->file('comprobante')->getClientOriginalExtension();

        $comprobantePath = $request->file('comprobante')->storeAs(
            'pagos/comprobantes',
            $nombreArchivo,
            'public'
        );

        $numeroCuota = Pago::where('obligacion_id', $obligacion->id)
            ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE])
            ->count() + 1;

        $pago = Pago::create([
            'numero_comprobante' => 'TEMP',
            'codigo_referencia'  => $request->referencia ?: null,
            'obligacion_id'      => $obligacion->id,
            'monto'              => $request->monto,
            'metodo_pago'        => $request->metodo_pago,
            'estado'             => Pago::ESTADO_PENDIENTE,
            'numero_cuota'       => $numeroCuota,
            'fecha_pago'         => now(),
            'descripcion'        => $request->descripcion
                ?: 'Cuota ' . $numeroCuota . ' — ' . $obligacion->tipo,
            'comprobante_path'   => $comprobantePath,
        ]);

        $pago->update([
            'numero_comprobante' => 'ISTC-CP-' . $obligacion->tipo . '-' . now()->year . '-' . str_pad($pago->id, 5, '0', STR_PAD_LEFT),
        ]);

        if ($obligacion->estado === 'Pendiente') {
            $obligacion->update(['estado' => 'Parcial']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado. La secretaría verificará tu comprobante en breve.',
            'data'    => [
                'numero_comprobante' => $pago->numero_comprobante,
                'monto'              => $pago->monto,
                'estado'             => $pago->estado,
            ],
        ], 201);
    }

    public function historial(Request $request, int $id)
    {
        $obligacion = ObligacionesFinanciera::with([
            'periodo',
            'matricula',
            'pagos' => fn($q) => $q->orderByDesc('numero_cuota')->orderByDesc('fecha_pago'),
        ])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'obligacion' => $this->formatObligacion($obligacion),
                'pagos'      => $obligacion->pagos->map(fn($p) => [
                    'id'                 => $p->id,
                    'numero_comprobante' => $p->numero_comprobante,
                    'monto'              => $p->monto,
                    'metodo_pago'        => $p->metodo_pago,
                    'estado'             => $p->estado,
                    'numero_cuota'       => $p->numero_cuota,
                    'fecha_pago'         => $p->fecha_pago?->format('d/m/Y H:i'),
                    'descripcion'        => $p->descripcion,
                ]),
            ],
        ]);
    }

    private function formatObligacion(ObligacionesFinanciera $ob): array
    {
        return [
            'id'                => $ob->id,
            'tipo'              => $ob->tipo,
            'descripcion'       => $ob->descripcion,
            'monto_original'    => $ob->monto_original,
            'descuento'         => $ob->descuento,
            'monto_final'       => $ob->monto_final,
            'saldo'             => $ob->saldo,
            'total_pagado'      => $ob->total_pagado,
            'estado'            => $ob->estado,
            'fecha_vencimiento' => $ob->fecha_vencimiento?->format('d/m/Y'),
            'periodo'           => $ob->periodo
                ? ['id' => $ob->periodo->id, 'descripcion' => $ob->periodo->description]
                : null,
        ];
    }
}
