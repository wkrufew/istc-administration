<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ISTC') }} — Portal Estudiantil</title>
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
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif;
        }

        /* Fondo con patrón sutil */
        .page-bg {
            background-color: #f1f5f9;
            background-image:
                radial-gradient(circle at 20% 0%, rgba(99, 102, 241, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 100%, rgba(59, 130, 246, 0.06) 0%, transparent 50%);
        }

        /* Header con gradiente sutil */
        .page-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 50%, #1d4ed8 100%);
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
        @livewire('estudiante.menu-estudiante')

        {{-- PAGE HEADER --}}
        @if (isset($header))
            <div class="page-header">
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            {{-- Breadcrumb decorativo --}}
                            <div class="flex items-center gap-2 text-blue-200/60 text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Portal Estudiantil</span>
                                <span>/</span>
                            </div>
                            <div class="text-white font-semibold text-sm">
                                {{ $header }}
                            </div>
                        </div>
                        {{-- Número de matrícula si existe --}}
                        @if (Auth::user()->matricula_numero)
                            <span
                                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                         bg-white/10 backdrop-blur-sm border border-white/15
                                         text-xs font-semibold text-blue-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ Auth::user()->matricula_numero }}
                            </span>
                        @endif
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
                        © {{ date('Y') }} ISTC — Portal Estudiantil
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
    @stack('js')

    <script>
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            });
        @endif
    </script>
</body>

</html>
{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('estudiante.menu-estudiante')


        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            console.log("{{ session('success') }}");
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                });
            });
        @endif
    </script>
</body>

</html> --}}
