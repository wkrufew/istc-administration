<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('administracion.docencia.dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('administracion.docencia.dashboard') }}" :active="request()->routeIs('administracion.docencia.dashboard')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('administracion.docencia.calificaciones.index') }}" :active="request()->routeIs('administracion.docencia.calificaciones.index')">
                        {{ __('Calificaciones') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('administracion.docencia.asistencias.index') }}" :active="request()->routeIs('administracion.docencia.asistencias.index')">
                        {{ __('Asistencias') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- Campana de avisos (docente) --}}
                @php
                    use App\Models\Aviso;
                    use App\Models\Periodo;
                    use App\Models\AsignacionDocente;
                    $_periodoDoc = Periodo::periodoActivoGlobal();
                    $_avisosDoc  = ($_periodoDoc && auth()->check())
                        ? Aviso::whereHas('asignacionDocente', fn($q) =>
                              $q->where('docente_id', auth()->id())
                                ->where('periodo_id', $_periodoDoc->id)
                          )->where('fecha_aviso', '>=', now()->startOfDay())->count()
                        : 0;
                @endphp
                <a href="{{ route('administracion.docencia.avisos') }}"
                   class="relative inline-flex items-center justify-center w-9 h-9 rounded-lg
                          text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200
                          {{ request()->routeIs('administracion.docencia.avisos') ? 'text-indigo-600 bg-indigo-50' : '' }}"
                   title="Mis avisos">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($_avisosDoc > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] px-0.5
                                 flex items-center justify-center rounded-full
                                 bg-red-500 text-white text-[10px] font-bold leading-none">
                        {{ $_avisosDoc > 9 ? '9+' : $_avisosDoc }}
                    </span>
                    @endif
                </a>

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('administracion.docencia.docente-profile') }}">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('administracion.docencia.dashboard') }}" :active="request()->routeIs('administracion.docencia.dashboard')">
                {{ __('Inicio') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('administracion.docencia.calificaciones.index') }}" :active="request()->routeIs('administracion.docencia.calificaciones.*')">
                {{ __('Calificaciones') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('administracion.docencia.asistencias.index') }}" :active="request()->routeIs('administracion.docencia.asistencias.*')">
                {{ __('Asistencias') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('administracion.docencia.avisos') }}" :active="request()->routeIs('administracion.docencia.avisos')">
                {{ __('Avisos') }}
                @if($_avisosDoc > 0)
                <span class="ml-2 inline-flex items-center justify-center min-w-[1.2rem] h-5 px-1
                             rounded-full bg-red-500 text-white text-xs font-bold">
                    {{ $_avisosDoc > 9 ? '9+' : $_avisosDoc }}
                </span>
                @endif
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
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
                <!-- Account Management -->
                {{-- <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link> --}}
                <x-responsive-nav-link href="{{ route('administracion.estudiantil.estudiante-profile') }}"
                    :active="request()->routeIs('administracion.estudiantil.estudiante-profile')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
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
