<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #333;
        }

        /* HEADER */
        .header {
            width: 100%;
            border-bottom: 3px solid #32620e;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .logo {
            width: 60px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            color: #32620e;
        }

        .subtitle {
            font-size: 10px;
            color: #666;
        }

        /* CARDS */
        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to right, #32620e, #7ea41e);
            color: white;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
        }

        .card-body {
            padding: 8px;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            border-radius: 4px;
            color: white;
        }

        .badge-verde {
            background: #7ea41e;
        }

        .badge-naranja {
            background: #e59e20;
        }

        .badge-morado {
            background: #84219f;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #32620e;
            color: white;
            font-size: 9px;
            padding: 5px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 4px;
            font-size: 9px;
        }

        /* STATS */
        .stats {
            width: 100%;
            margin-top: 5px;
        }

        .stat-box {
            text-align: center;
            border-right: 1px solid #eee;
        }

        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #84219f;
        }

        .stat-label {
            font-size: 9px;
            color: #777;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="70">
                    <img src="{{ public_path('imagenes/icono.webp') }}" class="logo">
                </td>
                <td>
                    <div class="title">INSTITUTO SUPERIOR TECNOLÓGICO CUMANDA</div>
                    <div class="subtitle">Sistema Académico · Reportes Institucionales</div>
                    <div class="subtitle">Generado: {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- CONTENT --}}
    @yield('content')

    {{-- FOOTER --}}
    <div class="footer">
        Página {PAGE_NUM} de {PAGE_COUNT}
    </div>

</body>

</html>
