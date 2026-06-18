<div>
    <nav x-data="{ open: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })" :class="scrolled ? 'shadow-md shadow-slate-900/8' : ''"
        class="relative bg-white/95 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40 transition-all duration-300">

        {{-- Línea de acento superior --}}
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-400"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- LOGO + LINKS --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('administracion.estudiantil.dashboard') }}"
                        class="flex items-center gap-2.5 group shrink-0">
                        {{-- <div
                            class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700
                                    flex items-center justify-center shadow-md shadow-blue-500/30
                                    group-hover:scale-105 group-hover:shadow-blue-500/40 transition-all duration-200">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-bold text-slate-800 leading-none tracking-tight">ISTC</p>
                            <p class="text-xs text-slate-400 leading-none mt-0.5">Portal Estudiantil</p>
                        </div> --}}
                        @php
        $logoPath = \App\Services\SettingService::get('instituto.logo_path');
        $logoSrc  = ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath)
            : asset('../imagenes/icono.webp');
    @endphp
    
    @if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
        <img class="object-cover overflow-hidden size-12" src="{{ $logoSrc }}" alt="">
    @else
        <img class="object-cover overflow-hidden size-12" src="{{ asset('../imagenes/icono.webp') }}">
    @endif
                    </a>

                    {{-- Nav links desktop --}}
                    @php
                        $navLinks = [
                            [
                                'route' => 'administracion.estudiantil.dashboard',
                                'label' => 'Inicio',
                                'icon' =>
                                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                            ],
                            [
                                'route' => 'administracion.estudiantil.horarios',
                                'label' => 'Horarios',
                                'icon' =>
                                    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            ],
                            [
                                'route' => 'administracion.estudiantil.calificaciones',
                                'label' => 'Calificaciones',
                                'icon' =>
                                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                            ],
                            [
                                'route' => 'administracion.estudiantil.obligaciones-financieras',
                                'label' => 'Pagos',
                                'icon' =>
                                    'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                            ],
                            [
                                'route' => 'administracion.estudiantil.acta-calificaciones.index',
                                'label' => 'Acta de Calificaciones',
                                'icon' =>
                                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                            ],
                            [
                                'route' => 'administracion.estudiantil.solicitudes',
                                'label' => 'Solicitudes',
                                'icon' =>
                                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                            ],
                            [
                                'route' => 'administracion.estudiantil.calendario-moodle',
                                'label' => 'Actividades Moodle',
                                'icon'  => 'M13 10V3L4 14h7v7l9-11h-7z',
                            ],
                        ];
                    @endphp

                    <div class="hidden md:flex items-center gap-1">
                        @foreach ($navLinks as $link)
                            @php $isActive = request()->routeIs($link['route']); @endphp
                            <a href="{{ route($link['route']) }}"
                                class="relative flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-200 group
                                      {{ $isActive ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 transition-colors duration-200
                                            {{ $isActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="{{ $link['icon'] }}" />
                                </svg>
                                {{ $link['label'] }}
                                @if ($isActive)
                                    <span
                                        class="absolute bottom-1 left-1/2 -translate-x-1/2
                                                 w-4 h-0.5 rounded-full bg-blue-600"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- DERECHA: campanita + usuario dropdown --}}
                <div class="hidden md:flex items-center gap-2">

                    {{-- Campana avisos --}}
                    <a href="{{ route('administracion.estudiantil.avisos') }}"
                       class="relative inline-flex items-center justify-center w-9 h-9 rounded-xl
                              text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200
                              {{ request()->routeIs('administracion.estudiantil.avisos') ? 'text-blue-600 bg-blue-50' : '' }}"
                       title="Mis avisos">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($this->avisosNoLeidos > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] px-0.5
                                     flex items-center justify-center rounded-full
                                     bg-red-500 text-white text-[10px] font-bold leading-none
                                     ring-2 ring-white">
                            {{ $this->avisosNoLeidos > 9 ? '9+' : $this->avisosNoLeidos }}
                        </span>
                        @endif
                    </a>
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-xl
                                           border border-transparent hover:border-slate-200
                                           hover:bg-slate-50 transition-all duration-200 group">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="w-8 h-8 rounded-xl object-cover ring-2 ring-white shadow-sm"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                @else
                                    <div
                                        class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                                flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-left hidden lg:block">
                                    <p class="text-xs font-semibold text-slate-700 leading-tight max-w-32 truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-xs text-slate-400 leading-none mt-0.5">Estudiante</p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition hidden lg:block"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Mi cuenta</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1 truncate">{{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <x-dropdown-link href="{{ route('administracion.estudiantil.estudiante-profile') }}"
                                    class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Mi Perfil
                                </x-dropdown-link>
                            </div>

                            @canany(['acceso_administrativo', 'acceso_docencia'])
                            <div class="border-t border-slate-100 py-1">
                                <p class="px-4 pt-2 pb-1 text-xs text-slate-400 font-medium uppercase tracking-wider">Mis portales</p>
                                @can('acceso_administrativo')
                                <x-dropdown-link href="{{ route('administracion.administrativa.dashboard') }}"
                                    class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Panel Administrativo
                                </x-dropdown-link>
                                @endcan
                                @can('acceso_docencia')
                                <x-dropdown-link href="{{ route('administracion.docencia.dashboard') }}"
                                    class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Portal Docente
                                </x-dropdown-link>
                                @endcan
                            </div>
                            @endcanany

                            <div class="border-t border-slate-100 py-1">
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                        class="flex items-center gap-2.5 text-sm text-red-500 hover:text-red-700 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Cerrar Sesión
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Hamburger móvil --}}
                <div class="flex items-center md:hidden">
                    <button @click="open = !open"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500
                               hover:bg-slate-100 hover:text-slate-700 transition-all duration-200">
                        <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- MENÚ MÓVIL --}}
        <div :class="{ 'block': open, 'hidden': !open }"
            class="hidden md:hidden border-t border-slate-100 bg-white/98 backdrop-blur-md">
            <div class="p-3 space-y-1">
                @foreach ($navLinks as $link)
                    @php $isActive = request()->routeIs($link['route']); @endphp
                    <a href="{{ route($link['route']) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                              {{ $isActive ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <svg class="w-4 h-4 {{ $isActive ? 'text-blue-600' : 'text-slate-400' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="{{ $link['icon'] }}" />
                        </svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach

                {{-- Avisos (móvil) --}}
                @php $isAvisos = request()->routeIs('administracion.estudiantil.avisos'); @endphp
                <a href="{{ route('administracion.estudiantil.avisos') }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all
                           {{ $isAvisos ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ $isAvisos ? 'text-blue-600' : 'text-slate-400' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Avisos
                    </div>
                    @if($this->avisosNoLeidos > 0)
                    <span class="inline-flex items-center justify-center min-w-[1.3rem] h-5 px-1.5
                                 rounded-full bg-red-500 text-white text-xs font-bold">
                        {{ $this->avisosNoLeidos > 9 ? '9+' : $this->avisosNoLeidos }}
                    </span>
                    @endif
                </a>
            </div>
            <div class="border-t border-slate-100 p-4 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-10 h-10 rounded-xl object-cover shadow-sm"
                            src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    @else
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                                    flex items-center justify-center text-white font-bold shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('administracion.estudiantil.estudiante-profile') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Mi Perfil
                    </a>

                    @canany(['acceso_administrativo', 'acceso_docencia'])
                    <p class="px-3 pt-3 pb-0.5 text-xs text-slate-400 font-medium uppercase tracking-wider">Mis portales</p>
                    @can('acceso_administrativo')
                    <a href="{{ route('administracion.administrativa.dashboard') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Panel Administrativo
                    </a>
                    @endcan
                    @can('acceso_docencia')
                    <a href="{{ route('administracion.docencia.dashboard') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Portal Docente
                    </a>
                    @endcan
                    @endcanany

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <button @click.prevent="$root.submit();"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-red-500 hover:bg-red-50 hover:text-red-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</div>
{{-- <div>
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('administracion.estudiantil.dashboard') }}">
                            <x-application-mark class="block h-9 w-auto" />
                        </a>
                    </div>

            
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('administracion.estudiantil.dashboard') }}" :active="request()->routeIs('administracion.estudiantil.dashboard')">
                            {{ __('Inicio') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('administracion.estudiantil.horarios') }}" :active="request()->routeIs('administracion.estudiantil.horarios')">
                            {{ __('Horarios') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('administracion.estudiantil.calificaciones') }}" :active="request()->routeIs('administracion.estudiantil.calificaciones')">
                            {{ __('Calificaciones') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('administracion.estudiantil.obligaciones-financieras') }}"
                            :active="request()->routeIs('administracion.estudiantil.obligaciones-financieras')">
                            {{ __('Obligaciones Financieras') }}
                        </x-nav-link>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">


                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="size-8 rounded-full object-cover"
                                            src="{{ Auth::user()->profile_photo_url }}"
                                            alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-dropdown-link href="{{ route('administracion.estudiantil.estudiante-profile') }}">
                                    {{ __('Perfil') }}
                                </x-dropdown-link>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                        {{ __('API Tokens') }}
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-200"></div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('administracion.estudiantil.dashboard') }}" :active="request()->routeIs('administracion.estudiantil.dashboard')">
                    {{ __('Inicio') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('administracion.estudiantil.horarios') }}" :active="request()->routeIs('administracion.estudiantil.horarios')">
                    {{ __('Horarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('administracion.estudiantil.calificaciones') }}"
                    :active="request()->routeIs('administracion.estudiantil.calificaciones')">
                    {{ __('Calificaciones') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('administracion.estudiantil.obligaciones-financieras') }}"
                    :active="request()->routeIs('administracion.estudiantil.obligaciones-financieras')">
                    {{ __('Obligaciones Financieras') }}
                </x-responsive-nav-link>
            </div>

            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('administracion.estudiantil.estudiante-profile') }}"
                        :active="request()->routeIs('administracion.estudiantil.estudiante-profile')">
                        {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                            {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>

</div> --}}
