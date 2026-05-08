<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches))
                document.documentElement.classList.add('dark');
            else
                document.documentElement.classList.remove('dark');
        })();
    </script> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ config('app.url', 'ISTCumandá') }}">
    {{-- <title>{{ config('app.name', 'ISTCumandá') }} — Portal Docente</title>
    <link rel="shortcut icon" href="{{ asset('../imagenes/icono.webp') }}"> --}}

    @php
        $nombreCorto = \App\Services\SettingService::get('instituto.nombre_corto') ?: config('app.name', 'ISTCumandá');
        $faviconPath = \App\Services\SettingService::get('instituto.favicon_path');
    @endphp
    <title>{{ $nombreCorto }} — Portal Docente</title>
    @if ($faviconPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($faviconPath))
        <link rel="shortcut icon" href="{{ Storage::disk('public')->url($faviconPath) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('../imagenes/icono.webp') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif;
        }

        .page-bg {
            background-color: #f1f5f9;
            background-image:
                radial-gradient(circle at 20% 0%, rgba(16, 185, 129, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 100%, rgba(20, 184, 166, 0.06) 0%, transparent 50%);
        }

        .page-header {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }
    </style>
</head>

<body class="font-sans antialiased page-bg min-h-screen">

    <x-banner />

    <div class="min-h-screen flex flex-col">

        {{-- NAVBAR --}}
        @livewire('docente.menu-docente')

        {{-- PAGE HEADER --}}
        @if (isset($header))
            <div class="page-header">
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 text-emerald-200/60 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span>Portal Docente</span>
                                <span>/</span>
                            </div>
                            <div class="text-white font-semibold text-sm">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="flex-1">
            {{ $slot }}
        </main>

        {{-- FOOTER MINIMALISTA --}}
        <footer class="border-t border-slate-200 bg-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p class="text-xs text-slate-400">
                        © {{ date('Y') }} ISTC — Portal Docente
                    </p>
                    <p class="text-xs text-slate-300">
                        Sistema de Gestión Académica
                    </p>
                </div>
            </div>
        </footer>

    </div>

    @stack('modals')

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });

        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                Toast.fire({ icon: 'success', title: @json(session('success')) });
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function () {
                Toast.fire({ icon: 'error', title: @json(session('error')) });
            });
        @endif

        @if (session('swal'))
            document.addEventListener('DOMContentLoaded', function () {
                const data = @json(session('swal'));
                Swal.fire({
                    icon: data.icon ?? 'info',
                    title: data.title ?? '',
                    text: data.text ?? '',
                    confirmButtonText: data.confirmButtonText ?? 'Entendido',
                    confirmButtonColor: '#10b981',
                });
            });
        @endif

        document.addEventListener('livewire:init', function () {
            Livewire.on('swal', function (params) {
                const data = Array.isArray(params) ? (params[0] ?? params) : params;

                if (data.toast === true) {
                    Toast.fire({
                        icon:     data.icon  ?? 'info',
                        title:    data.title ?? '',
                        text:     data.text  ?? '',
                        position: data.position ?? 'top-end',
                        timer:    data.timer ?? 3000,
                    });
                } else {
                    Swal.fire({
                        icon:  data.icon  ?? 'info',
                        title: data.title ?? '',
                        ...(data.html ? { html: data.html } : { text: data.text ?? '' }),
                        timer:             data.timer ?? undefined,
                        timerProgressBar:  data.timer ? true : false,
                        showConfirmButton: data.showConfirmButton !== undefined
                            ? data.showConfirmButton
                            : (data.timer ? false : true),
                        confirmButtonText:  data.confirmButtonText ?? 'Entendido',
                        confirmButtonColor: '#10b981',
                    });
                }
            });
        });
    </script>

</body>

</html>
