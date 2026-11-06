<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $ficha['titulo'] }} · {{ $ficha['nombre'] }}</title>
    <style>
        /* Sin reinicio global (*): en dompdf anula los márgenes de la página */
        @page { margin: 14mm 14mm 18mm 14mm; }
        div, table, td, th, p { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.6px; color: #1e293b; background: #fff; }

        /* Cabecera institucional (misma de los certificados) */
        .inst-header { background-color: #1e3a8a; padding: 9px 12px; border-radius: 6px 6px 0 0; }
        .inst-header table { width: 100%; border-collapse: collapse; }
        .inst-logo { height: 44px; width: auto; }
        .inst-name { font-size: 12px; font-weight: bold; color: #fff; letter-spacing: 0.04em; text-transform: uppercase; }
        .inst-detail { font-size: 7.5px; color: #c7d2fe; margin-top: 2px; }
        .inst-badge { text-align: right; }
        .badge-label { font-size: 7px; color: #c7d2fe; text-transform: uppercase; letter-spacing: 0.08em; }
        .badge-value { font-size: 11px; color: #fff; font-weight: bold; margin-top: 1px; text-transform: uppercase; letter-spacing: 0.03em; }
        .stripe { height: 3px; background: #2563eb; margin-bottom: 10px; }

        /* Tarjeta de la persona */
        .persona { width: 100%; border: 1px solid #dbe3ef; border-radius: 6px; margin-bottom: 10px; }
        .persona td { vertical-align: middle; padding: 8px 10px; }
        .foto { width: 64px; height: 64px; border-radius: 6px; }
        table.iniciales { width: 64px; height: 64px; border-collapse: collapse; }
        table.iniciales td { width: 64px; height: 64px; padding: 0; border-radius: 6px; background: #1d4ed8; color: #fff; font-size: 22px; font-weight: bold; text-align: center; vertical-align: middle; }
        .p-nombre { font-size: 14px; font-weight: bold; color: #0f172a; }
        .p-datos { margin-top: 3px; color: #475569; font-size: 8.4px; }
        .p-datos span.sep { color: #cbd5e1; padding: 0 4px; }
        .chips { margin-top: 5px; }
        .chip { display: inline-block; padding: 2px 7px; border-radius: 9px; font-size: 7.6px; font-weight: bold; margin-right: 4px; border: 1px solid; }
        .chip-ok     { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .chip-mal    { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .chip-aviso  { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .chip-info   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .chip-neutro { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .observacion { margin-top: 6px; padding: 5px 8px; border-radius: 4px; background: #fef2f2; color: #b91c1c; font-size: 8px; }

        /* Bloques */
        .bloque { margin-bottom: 9px; page-break-inside: avoid; }
        .bloque-largo { page-break-inside: auto; }
        .b-titulo { font-size: 8.4px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.07em;
                    border-bottom: 1.5px solid #2563eb; padding-bottom: 2px; margin-bottom: 4px; }
        .financiero .b-titulo { color: #065f46; border-bottom-color: #059669; }

        table.campos { width: 100%; border-collapse: collapse; }
        table.campos td { padding: 3px 5px; border-bottom: 1px solid #eef2f7; vertical-align: top; }
        table.campos td.et { width: 17%; color: #64748b; font-size: 7.8px; }
        table.campos td.va { width: 33%; color: #0f172a; }
        .vacio { color: #94a3b8; font-style: italic; }

        table.tabla { width: 100%; border-collapse: collapse; }
        table.tabla th { background: #eff4fb; color: #1e3a8a; font-size: 7.4px; text-transform: uppercase; letter-spacing: 0.04em;
                         text-align: left; padding: 4px 5px; border-bottom: 1px solid #c7d6ee; }
        .financiero table.tabla th { background: #ecfdf5; color: #065f46; border-bottom-color: #a7f3d0; }
        table.tabla td { padding: 3px 5px; border-bottom: 1px solid #eef2f7; vertical-align: top; }
        table.tabla tr:nth-child(even) td { background: #fafbfd; }
        .sin-filas { color: #94a3b8; font-style: italic; padding: 4px 5px; }

        .nota { padding: 5px 8px; border-radius: 4px; font-size: 8px; margin-bottom: 9px; }
        .nota-aviso { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .nota-info  { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .nota strong { display: block; margin-bottom: 1px; }

        .separador-fin { margin: 12px 0 8px; padding: 5px 8px; background: #059669; color: #fff; font-weight: bold; font-size: 8.6px;
                         text-transform: uppercase; letter-spacing: 0.08em; border-radius: 4px; }

        /* Pie fijo en cada página */
        .pie { position: fixed; bottom: -12mm; left: 0; right: 0; height: 10mm; border-top: 1px solid #dbe3ef; padding-top: 3px; font-size: 7px; color: #64748b; }
        .pie table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>

    <div class="pie">
        <table><tr>
            <td>Generado el {{ $ficha['generado'] }} por {{ $ficha['generadoPor'] }} · {{ $instituto['nombre_corto'] }}</td>
            <td style="text-align:right;width:90px">{{-- "Página X de Y" lo escribe FichaPdfService::generar --}}</td>
        </tr><tr>
            <td colspan="2" style="font-size:6.6px;color:#94a3b8;padding-top:1px">
                Documento confidencial: contiene datos personales protegidos por la Ley Orgánica de Protección de Datos Personales. Uso exclusivo institucional.
            </td>
        </tr></table>
    </div>

    {{-- Cabecera --}}
    <div class="inst-header">
        <table><tr>
            <td style="width:52px">
                @if (file_exists($instituto['logo_file']))
                    <img src="{{ $instituto['logo_file'] }}" class="inst-logo" alt="">
                @endif
            </td>
            <td>
                <div class="inst-name">{{ $instituto['nombre_largo'] }}</div>
                <div class="inst-detail">
                    @if ($instituto['ruc']) RUC {{ $instituto['ruc'] }} · @endif
                    {{ $instituto['direccion'] }}
                </div>
                <div class="inst-detail">
                    {{ collect([$instituto['telefono'], $instituto['email'], $instituto['web']])->filter()->implode(' · ') }}
                </div>
            </td>
            <td class="inst-badge">
                <div class="badge-label">Documento</div>
                <div class="badge-value">{{ $ficha['titulo'] }}</div>
            </td>
        </tr></table>
    </div>
    <div class="stripe"></div>

    {{-- Persona --}}
    <table class="persona"><tr>
        <td style="width:84px">
            @if ($ficha['foto'])
                <img src="{{ $ficha['foto'] }}" class="foto" alt="">
            @else
                <table class="iniciales"><tr><td>{{ $ficha['iniciales'] }}</td></tr></table>
            @endif
        </td>
        <td>
            <div class="p-nombre">{{ $ficha['nombre'] }}</div>
            <div class="p-datos">
                @foreach ($ficha['datos'] as [$etiqueta, $valor])
                    @if (! $loop->first)<span class="sep">|</span>@endif{{ trim($etiqueta . ' ' . $valor) }}
                @endforeach
            </div>
            <div class="chips">
                @foreach ($ficha['chips'] as [$texto, $tono])
                    <span class="chip chip-{{ $tono }}">{{ $texto }}</span>
                @endforeach
            </div>
            @if ($ficha['observacion'])
                <div class="observacion">Observación de secretaría: {{ $ficha['observacion'] }}</div>
            @endif
        </td>
    </tr></table>

    {{-- Bloques --}}
    @php $separadorFinanciero = false; @endphp
    @foreach ($ficha['bloques'] as $bloque)
        @if ($bloque['grupo'] === 'financiero' && ! $separadorFinanciero)
            @php $separadorFinanciero = true; @endphp
            <div class="separador-fin">Resumen financiero</div>
        @endif

        @if ($bloque['tipo'] === 'nota')
            <div class="nota nota-{{ $bloque['tono'] }}"><strong>{{ $bloque['titulo'] }}</strong>{{ $bloque['texto'] }}</div>

        @elseif ($bloque['tipo'] === 'campos')
            @php
                // Filas de dos pares etiqueta/valor; los campos "ancho" ocupan su propia fila
                $filas = []; $par = [];
                foreach ($bloque['campos'] as $c) {
                    if ($c['ancho']) { if ($par) { $filas[] = $par; $par = []; } $filas[] = [$c]; continue; }
                    $par[] = $c;
                    if (count($par) === 2) { $filas[] = $par; $par = []; }
                }
                if ($par) $filas[] = $par;
            @endphp
            <div class="bloque {{ $bloque['grupo'] }}">
                <div class="b-titulo">{{ $bloque['titulo'] }}</div>
                <table class="campos">
                    @foreach ($filas as $fila)
                        <tr>
                            @if (count($fila) === 1 && $fila[0]['ancho'])
                                <td class="et">{{ $fila[0]['etiqueta'] }}</td>
                                <td colspan="3">@if ($fila[0]['valor'] !== null){{ $fila[0]['valor'] }}@else<span class="vacio">Sin registrar</span>@endif</td>
                            @else
                                @foreach ($fila as $c)
                                    <td class="et">{{ $c['etiqueta'] }}</td>
                                    <td class="va">@if ($c['valor'] !== null){{ $c['valor'] }}@else<span class="vacio">Sin registrar</span>@endif</td>
                                @endforeach
                                @if (count($fila) === 1)<td class="et"></td><td class="va"></td>@endif
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>

        @elseif ($bloque['tipo'] === 'tabla')
            <div class="bloque {{ count($bloque['filas']) > 12 ? 'bloque-largo' : '' }} {{ $bloque['grupo'] }}">
                <div class="b-titulo">{{ $bloque['titulo'] }}</div>
                @if ($bloque['filas'])
                    <table class="tabla">
                        <thead><tr>@foreach ($bloque['columnas'] as $col)<th>{{ $col }}</th>@endforeach</tr></thead>
                        <tbody>
                            @foreach ($bloque['filas'] as $fila)
                                <tr>@foreach ($fila as $i => $celda)<td @if (in_array($i, $bloque['sinPartir'] ?? [], true)) style="white-space:nowrap" @endif>@if (filled($celda)){{ $celda }}@else<span class="vacio">—</span>@endif</td>@endforeach</tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="sin-filas">{{ $bloque['vacio'] }}</div>
                @endif
            </div>
        @endif
    @endforeach

</body>
</html>
