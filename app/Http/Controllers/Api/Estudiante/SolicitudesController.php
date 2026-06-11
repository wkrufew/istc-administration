<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SolicitudesController extends Controller
{
    public function tipos()
    {
        $tipos = TipoSolicitud::where('is_active', true)
            ->orderBy('nombre')
            ->get()
            ->map(fn($t) => [
                'id'                 => $t->id,
                'nombre'             => $t->nombre,
                'descripcion'        => $t->descripcion,
                'precio'             => $t->precio,
                'requiere_documento' => (bool) $t->requiere_documento,
            ]);

        return response()->json(['success' => true, 'data' => $tipos]);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $solicitudes = Solicitud::with(['tipoSolicitud', 'obligacion'])
            ->where('estudiante_id', $userId)
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $solicitudes->map(fn($s) => [
                'id'               => $s->id,
                'tipo'             => $s->tipoSolicitud?->nombre,
                'descripcion'      => $s->descripcion,
                'estado'           => $s->estado,
                'precio_aplicado'  => $s->precio_aplicado,
                'tiene_obligacion' => $s->obligacion !== null,
                'created_at'       => $s->created_at->format('d/m/Y'),
            ]),
            'meta' => [
                'current_page' => $solicitudes->currentPage(),
                'last_page'    => $solicitudes->lastPage(),
                'total'        => $solicitudes->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $tipo = TipoSolicitud::find($request->tipo_solicitud_id);

        $rules = [
            'tipo_solicitud_id' => 'required|exists:tipos_solicitudes,id',
            'descripcion'       => 'required|string|min:10|max:1000',
        ];

        if ($tipo?->requiere_documento) {
            $rules['documento'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $request->validate($rules, [
            'tipo_solicitud_id.required' => 'Seleccione el tipo de solicitud.',
            'descripcion.required'       => 'Describa el motivo de su solicitud.',
            'descripcion.min'            => 'La descripción debe tener al menos 10 caracteres.',
            'documento.required'         => 'Este tipo de solicitud requiere adjuntar un documento.',
            'documento.mimes'            => 'El documento debe ser PDF, JPG o PNG.',
            'documento.max'              => 'El documento no puede superar los 5 MB.',
        ]);

        $user = $request->user();

        $documentoPath = null;
        if ($tipo?->requiere_documento && $request->hasFile('documento')) {
            $slug     = Str::slug($user->name);
            $ext      = $request->file('documento')->getClientOriginalExtension();
            $filename = "{$slug}-solicitud-" . time() . ".{$ext}";
            $documentoPath = $request->file('documento')->storeAs(
                'solicitudes-documentos/' . $user->id,
                $filename,
                'public'
            );
        }

        $solicitud = Solicitud::create([
            'estudiante_id'     => $user->id,
            'tipo_solicitud_id' => $tipo->id,
            'descripcion'       => trim($request->descripcion),
            'documento_path'    => $documentoPath,
            'estado'            => 'pendiente',
            'precio_aplicado'   => $tipo->precio,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada correctamente. La secretaría la revisará pronto.',
            'data'    => [
                'id'     => $solicitud->id,
                'estado' => $solicitud->estado,
            ],
        ], 201);
    }
}
