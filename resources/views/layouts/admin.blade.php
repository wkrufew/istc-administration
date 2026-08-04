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
        x-data
        :class="{ 'md:ml-64': $store.sidebar.open, 'md:ml-14': !$store.sidebar.open }"
        class="flex flex-col justify-between ml-0 transition-all duration-300 ease-in-out pt-14 px-2 md:px-5 pb-4">

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
        const moon = document.querySelector(".moon")
        const sun = document.querySelector(".sun")

        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                open: localStorage.getItem('sidebar') !== 'closed',
                mobile: window.innerWidth < 768,
                mobileOpen: false,
                init() {
                    const self = this;
                    window.addEventListener('resize', () => {
                        self.mobile = window.innerWidth < 768;
                        if (!self.mobile) self.mobileOpen = false;
                    });
                },
                toggle() {
                    if (this.mobile) {
                        this.mobileOpen = !this.mobileOpen;
                    } else {
                        this.open = !this.open;
                        localStorage.setItem('sidebar', this.open ? 'open' : 'closed');
                    }
                }
            });
        });

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
            // Sincronizar iconos de tema con el estado real al cargar
            if (document.documentElement.classList.contains('dark')) {
                moon.classList.add("hidden");
                sun.classList.remove("hidden");
            } else {
                sun.classList.add("hidden");
                moon.classList.remove("hidden");
            }

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
            ...swalTheme(),
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
        // Sin DOMContentLoaded: con Livewire Navigate los scripts inline se
        // re-ejecutan en cada navegación SPA, por lo que la llamada directa
        // funciona tanto en carga inicial como en navegación entre páginas.
        @if (session('swal'))
            Swal.fire({
                icon:               @json(session('swal')['icon']              ?? 'info'),
                title:              @json(session('swal')['title']             ?? ''),
                text:               @json(session('swal')['text']              ?? ''),
                confirmButtonText:  @json(session('swal')['confirmButtonText'] ?? 'Aceptar'),
                confirmButtonColor: '#65a30d',
                ...swalTheme(),
            });
        @endif

        // ── Reaplicar dark mode tras navegación SPA (morphdom resetea <html>) ──
        document.addEventListener('livewire:navigated', function () {
            const t = localStorage.getItem('theme');
            const dark = t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.classList.toggle('light', !dark);
        });

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
                        ...(data.html ? { html: data.html } : { text: data.text ?? '' }),
                        timer: data.timer ?? undefined,
                        showConfirmButton: data.showConfirmButton !== undefined
                            ? data.showConfirmButton
                            : (data.timer ? false : true),
                        confirmButtonText: data.confirmButtonText ?? 'Entendido',
                        confirmButtonColor: '#6366f1',
                        customClass: {
                            confirmButton: 'swal-btn-confirm',
                            popup: 'swal-popup-custom',
                        },
                        ...swalTheme(),
                    });
                }
            });
        });
    </script>
</body>

</html>
