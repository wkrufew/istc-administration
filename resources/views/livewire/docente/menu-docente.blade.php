<div>
    <nav x-data="{ open: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
         :class="scrolled ? 'shadow-md shadow-slate-900/8' : ''"
         class="relative bg-white/95 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40 transition-all duration-300">

        {{-- Línea de acento superior --}}
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- LOGO + LINKS --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('administracion.docencia.dashboard') }}"
                       class="flex items-center gap-2.5 group shrink-0">

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
                                'route' => 'administracion.docencia.dashboard',
                                'label' => 'Inicio',
                                'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'active_pattern' => 'administracion.docencia.dashboard',
                            ],
                            [
                                'route' => 'administracion.docencia.calificaciones.index',
                                'label' => 'Calificaciones',
                                'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                                'active_pattern' => 'administracion.docencia.calificaciones.*',
                            ],
                            [
                                'route' => 'administracion.docencia.asistencias.index',
                                'label' => 'Asistencias',
                                'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6m-6 4h6',
                                'active_pattern' => 'administracion.docencia.asistencias.*',
                            ],
                            [
                                'route' => 'administracion.docencia.avisos',
                                'label' => 'Avisos',
                                'icon'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                                'active_pattern' => 'administracion.docencia.avisos',
                            ],
                        ];
                    @endphp

                    <div class="hidden md:flex items-center gap-1">
                        @foreach ($navLinks as $link)
                            @php $isActive = request()->routeIs($link['active_pattern']); @endphp
                            <a href="{{ route($link['route']) }}"
                               class="relative flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-200 group
                                      {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4 transition-colors duration-200
                                            {{ $isActive ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="{{ $link['icon'] }}" />
                                </svg>
                                {{ $link['label'] }}
                                @if ($isActive)
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2
                                                  w-4 h-0.5 rounded-full bg-emerald-600"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- DERECHA: usuario dropdown --}}
                <div class="hidden md:flex items-center gap-2">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-xl
                                           border border-transparent hover:border-slate-200
                                           hover:bg-slate-50 transition-all duration-200 group">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="w-8 h-8 rounded-xl object-cover ring-2 ring-white shadow-sm"
                                         src="{{ Auth::user()->profile_photo_url }}"
                                         alt="{{ Auth::user()->name }}" />
                                @else
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600
                                                flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-left hidden lg:block">
                                    <p class="text-xs font-semibold text-slate-700 leading-tight max-w-32 truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-xs text-slate-400 leading-none mt-0.5">Docente</p>
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
                                <p class="text-sm font-semibold text-slate-800 mt-1 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <x-dropdown-link href="{{ route('administracion.docencia.docente-profile') }}"
                                    class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Mi Perfil
                                </x-dropdown-link>
                            </div>

                            @canany(['acceso_administrativo', 'acceso_estudiantil'])
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
                                @can('acceso_estudiantil')
                                <x-dropdown-link href="{{ route('administracion.estudiantil.dashboard') }}"
                                    class="flex items-center gap-2.5 text-sm text-slate-600 hover:text-slate-900">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                    Portal Estudiantil
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
                    @php $isActive = request()->routeIs($link['active_pattern']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                              {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <svg class="w-4 h-4 {{ $isActive ? 'text-emerald-600' : 'text-slate-400' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="{{ $link['icon'] }}" />
                        </svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="border-t border-slate-100 p-4 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-3">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-10 h-10 rounded-xl object-cover shadow-sm"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}" />
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600
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
                    <a href="{{ route('administracion.docencia.docente-profile') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Mi Perfil
                    </a>

                    @canany(['acceso_administrativo', 'acceso_estudiantil'])
                    <p class="px-3 pt-2 pb-0.5 text-xs text-slate-400 font-medium uppercase tracking-wider">Mis portales</p>
                    @can('acceso_administrativo')
                    <a href="{{ route('administracion.administrativa.dashboard') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Panel Administrativo
                    </a>
                    @endcan
                    @can('acceso_estudiantil')
                    <a href="{{ route('administracion.estudiantil.dashboard') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-white hover:text-slate-800 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Portal Estudiantil
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
