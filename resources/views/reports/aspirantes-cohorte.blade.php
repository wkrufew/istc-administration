<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Aspirantes — {{ $cohorte->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .header { border-bottom: 2px solid #16a34a; padding-bottom: 10px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-left h1 { font-size: 15px; font-weight: 700; color: #15803d; }
        .header-left p  { font-size: 10px; color: #64748b; margin-top: 2px; }
        .header-right   { text-align: right; font-size: 10px; color: #64748b; }
        .header-right strong { font-size: 12px; color: #1e293b; display: block; }

        .meta { display: flex; gap: 24px; margin-bottom: 12px; }
        .meta-item { }
        .meta-item span { font-size: 9px; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; display: block; }
        .meta-item strong { font-size: 11px; color: #1e293b; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f1f5f9; }
        th { padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .05em; color: #475569; border-bottom: 1px solid #cbd5e1; white-space: nowrap; }
        th.center, td.center { text-align: center; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: #f8fafc; }

        .num { color: #64748b; font-size: 9px; }
        .name { font-weight: 600; }
        .cedula { color: #64748b; font-size: 10px; }
        .carrera { font-size: 10px; color: #475569; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 20px; font-size: 9px; font-weight: 600; }
        .b-pendiente    { background: #f1f5f9; color: #64748b; }
        .b-proceso      { background: #dbeafe; color: #1d4ed8; }
        .b-verificacion { background: #fef3c7; color: #b45309; }
        .b-aprobado     { background: #dcfce7; color: #15803d; }
        .b-rechazado    { background: #fee2e2; color: #dc2626; }
        .b-matriculado  { background: #ede9fe; color: #7c3aed; }

        .doc-ok   { color: #16a34a; font-weight: 700; }
        .doc-pend { color: #d97706; }
        .doc-no   { color: #dc2626; }
        .doc-na   { color: #cbd5e1; }

        .tipo-regular { font-size: 9px; color: #475569; }
        .tipo-valid   { font-size: 9px; color: #d97706; font-weight: 600; }

        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }

        @media print {
            @page { size: A4 landscape; margin: 10mm 12mm; }
            body { font-size: 10px; }
            .no-print { display: none !important; }
        }

        .print-btn { position: fixed; bottom: 20px; right: 20px; background: #16a34a; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
        .print-btn:hover { background: #15803d; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <h1>{{ $instituto['nombre_largo'] }}</h1>
            <p>Reporte de aspirantes inscritos</p>
        </div>
        <div class="header-right">
            <strong>{{ $cohorte->nombre }}</strong>
            Generado: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="meta">
        <div class="meta-item">
            <span>Cohorte</span>
            <strong>{{ $cohorte->nombre }}</strong>
        </div>
        @if($cohorte->fecha_inicio_matriculacion)
        <div class="meta-item">
            <span>Inicio matrícula</span>
            <strong>{{ $cohorte->fecha_inicio_matriculacion->format('d/m/Y') }}</strong>
        </div>
        @endif
        @if($cohorte->fecha_inicio_clases)
        <div class="meta-item">
            <span>Inicio clases</span>
            <strong>{{ $cohorte->fecha_inicio_clases->format('d/m/Y') }}</strong>
        </div>
        @endif
        <div class="meta-item">
            <span>Total aspirantes</span>
            <strong>{{ $aspirantes->count() }}</strong>
        </div>
        <div class="meta-item">
            <span>Estado cohorte</span>
            <strong>{{ ucfirst($cohorte->estado) }}</strong>
        </div>
    </div>

    @if($aspirantes->isEmpty())
    <p style="text-align:center;padding:40px;color:#94a3b8;">No hay aspirantes registrados en esta cohorte.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombres y Apellidos</th>
                <th>Cédula</th>
                <th>Carrera</th>
                <th>Tipo</th>
                <th class="center">Estado</th>
                <th class="center">CI</th>
                <th class="center">Bach/Hab</th>
                <th class="center">Pago</th>
                <th class="center">H. Vida</th>
                <th class="center">Cert. Lab.</th>
                <th class="center">Cert. Cur.</th>
            </tr>
        </thead>
        @php
            $docIcon = function(?string $path, ?string $estado): string {
                if (! $path) return '<span class="doc-na">—</span>';
                return match($estado) {
                    'aprobado', 'verificado' => '<span class="doc-ok">✓</span>',
                    'rechazado'              => '<span class="doc-no">✗</span>',
                    default                  => '<span class="doc-pend">○</span>',
                };
            };
        @endphp
        <tbody>
            @foreach($aspirantes as $i => $asp)
            @php
                $esValidacion = $asp->tipo_proceso === 'validacion_conocimientos';

                $estadoClase = match($asp->estado) {
                    'pendiente'    => 'b-pendiente',
                    'proceso'      => 'b-proceso',
                    'verificacion' => 'b-verificacion',
                    'aprobado'     => 'b-aprobado',
                    'rechazado'    => 'b-rechazado',
                    'matriculado'  => 'b-matriculado',
                    default        => 'b-pendiente',
                };
                $estadoLabel = \App\Models\Aspirante::ESTADOS[$asp->estado] ?? $asp->estado;
            @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>
                    <span class="name">{{ $asp->user->name }}</span>
                </td>
                <td class="cedula">{{ $asp->user->cedula }}</td>
                <td class="carrera">{{ $asp->carrera?->name ?? '—' }}</td>
                <td>
                    @if($esValidacion)
                    <span class="tipo-valid">Validación</span>
                    @else
                    <span class="tipo-regular">Regular</span>
                    @endif
                </td>
                <td class="center"><span class="badge {{ $estadoClase }}">{{ $estadoLabel }}</span></td>
                <td class="center">{!! $docIcon($asp->cedula_path, $asp->cedula_estado) !!}</td>
                <td class="center">
                    @if($asp->habilitante_path)
                        {!! $docIcon($asp->habilitante_path, $asp->habilitante_estado) !!}
                    @else
                        {!! $docIcon($asp->bachiller_path, $asp->bachiller_estado) !!}
                    @endif
                </td>
                <td class="center">{!! $docIcon($asp->pago_comprobante_path, $asp->pago_estado) !!}</td>
                <td class="center">
                    @if($esValidacion)
                        {!! $docIcon($asp->hoja_vida_path, $asp->hoja_vida_estado) !!}
                    @else
                        <span class="doc-na">—</span>
                    @endif
                </td>
                <td class="center">
                    @if($esValidacion)
                        {!! $docIcon($asp->cert_laborales_path, $asp->cert_laborales_estado) !!}
                    @else
                        <span class="doc-na">—</span>
                    @endif
                </td>
                <td class="center">
                    @if($esValidacion)
                        {!! $docIcon($asp->cert_cursos_path, $asp->cert_cursos_estado) !!}
                    @else
                        <span class="doc-na">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <span>✓ Aprobado &nbsp; ○ Pendiente &nbsp; ✗ Rechazado/No subido &nbsp; — No aplica</span>
        <span>{{ $instituto['nombre_corto'] }} · {{ now()->format('d/m/Y') }}</span>
    </div>

    <button class="print-btn no-print" onclick="window.print()">Imprimir / Guardar PDF</button>

</body>
</html>
