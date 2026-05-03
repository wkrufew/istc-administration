<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script>
        // Aplicar modo oscuro desde el inicio para evitar FOUC
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}

    <link rel="canonical" href="{{ config('app.url', 'ISTCumandá') }}">
    @php
        $nombreCorto = \App\Services\SettingService::get('instituto.nombre_corto') ?: config('app.name', 'ISTCumandá');
        $faviconPath = \App\Services\SettingService::get('instituto.favicon_path');
    @endphp
    <title>{{ $nombreCorto }}</title>
    @if ($faviconPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($faviconPath))
        <link rel="shortcut icon" href="{{ Storage::disk('public')->url($faviconPath) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('../imagenes/icono.webp') }}">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=abel:400|ubuntu:300,400,500,700" rel="stylesheet" />


    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-slate-950">
    @include('layouts.includes.sidebar')

    @include('layouts.includes.aside')

    <div
        class = "content flex flex-col justify-between ml-12 transform ease-in-out duration-500 pt-20 px-2 md:px-5 pb-4">

        {{--  @include('layouts.includes.navigation') --}}

        <div class="flex flex-wrap my-4 {{-- bg-gray-200 dark:bg-gray-800 rounded-lg shadow-md --}}">
            <main class="w-full {{-- h-auto --}} h-[calc(88vh)]">
                {{ $slot }}
            </main>
        </div>

    </div>

    @stack('modals')

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    @stack('js')
    <script>
        const sidebar = document.querySelector("aside");
        const maxSidebar = document.querySelector(".max")
        const miniSidebar = document.querySelector(".mini")
        const roundout = document.querySelector(".roundout")
        const maxToolbar = document.querySelector(".max-toolbar")
        const logo = document.querySelector('.logo')
        const content = document.querySelector('.content')
        const moon = document.querySelector(".moon")
        const sun = document.querySelector(".sun")

        function openNav() {
            if (sidebar.classList.contains('-translate-x-48')) {
                // max sidebar 
                sidebar.classList.remove("-translate-x-48")
                sidebar.classList.add("translate-x-none")
                maxSidebar.classList.remove("hidden")
                maxSidebar.classList.add("flex")
                miniSidebar.classList.remove("flex")
                miniSidebar.classList.add("hidden")
                maxToolbar.classList.add("translate-x-0")
                maxToolbar.classList.remove("translate-x-24", "scale-x-0")
                logo.classList.remove("ml-12")
                content.classList.remove("ml-12")
                content.classList.add("ml-12", "md:ml-60")
            } else {
                // mini sidebar
                sidebar.classList.add("-translate-x-48")
                sidebar.classList.remove("translate-x-none")
                maxSidebar.classList.add("hidden")
                maxSidebar.classList.remove("flex")
                miniSidebar.classList.add("flex")
                miniSidebar.classList.remove("hidden")
                maxToolbar.classList.add("translate-x-24", "scale-x-0")
                maxToolbar.classList.remove("translate-x-0")
                logo.classList.add('ml-12')
                content.classList.remove("ml-12", "md:ml-60")
                content.classList.add("ml-12")

            }

        }

        function toggleFullScreen() {
            if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document
                    .webkitIsFullScreen)) {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullscreen) {
                    document.documentElement.mozRequestFullscreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        }

        $(document).ready(function() {
            $("#full-screen").click(function() {
                toggleFullScreen()
                $("#full-icon").toggleClass("hidden");
                $("#collapse").toggleClass("hidden");
            });
        });

        function setDark(val) {
            if (val === "dark") {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                localStorage.theme = 'dark';

                moon.classList.add("hidden");
                sun.classList.remove("hidden");
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';

                sun.classList.add("hidden");
                moon.classList.remove("hidden");
            }
        }

        // ── Helpers SweetAlert reutilizables ─────────────────────────────────
        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
        }

        function swalTheme() {
            return isDarkMode() ? {
                background: '#111827',
                color: '#f9fafb'
            } : {
                background: '#ffffff',
                color: '#111827'
            };
        }

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

        // ── session('success') → toast verde ─────────────────────────────
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            });
        @endif

        // ── session('error') → toast rojo (mensajes simples de error) ────
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                Toast.fire({
                    icon: 'error',
                    title: @json(session('error'))
                });
            });
        @endif

        // ── session('swal') → modal centrado (acceso denegado, etc.) ─────
        @if (session('swal'))
            document.addEventListener('DOMContentLoaded', function() {
                const data = @json(session('swal'));
                Swal.fire({
                    icon: data.icon ?? 'info',
                    title: data.title ?? '',
                    text: data.text ?? '',
                    confirmButtonText: data.confirmButtonText ?? 'Aceptar',
                    confirmButtonColor: '#65a30d',
                    ...swalTheme(),
                });
            });
        @endif

        // ── Livewire → $this->dispatch('swal', [...]) ─────────────────────
        document.addEventListener('livewire:init', function() {
            Livewire.on('swal', function(params) {
                // Livewire 3 pasa los args como array; el payload es params[0]
                const data = Array.isArray(params) ? (params[0] ?? params) : params;

                const isToast = data.toast === true;

                if (isToast) {
                    Toast.fire({
                        icon: data.icon ?? 'info',
                        title: data.title ?? '',
                        text: data.text ?? '',
                    });
                } else {
                    Swal.fire({
                        icon: data.icon ?? 'info',
                        title: data.title ?? '',
                        text: data.text ?? '',
                        timer: data.timer ?? undefined,
                        showConfirmButton: data.timer ? false : true,
                        confirmButtonText: data.confirmButtonText ?? 'Aceptar',
                        confirmButtonColor: '#65a30d',
                        ...swalTheme(),
                    });
                }
            });
        });
    </script>
</body>

</html>
