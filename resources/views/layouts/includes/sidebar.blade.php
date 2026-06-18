<div
    x-data
    class="fixed w-full z-30 flex
    bg-white/70 dark:bg-slate-900/70
    backdrop-blur-md
    border-b border-slate-200 dark:border-slate-800
    p-2 items-center h-16 px-4 md:px-6">

    {{-- Mobile hamburger --}}
    <button @click="$store.sidebar.toggle()"
        class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg mr-2 flex-shrink-0
               text-slate-600 dark:text-slate-300
               hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
        aria-label="Abrir menú">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div
        :class="{ 'md:ml-64': $store.sidebar.open, 'md:ml-14': !$store.sidebar.open }"
        class="logo text-slate-900 dark:text-white ml-0 transition-all duration-300
    flex-none h-full flex items-center justify-center font-semibold tracking-wide">
        {{-- @php
            $nombreCorto =
                \App\Services\SettingService::get('instituto.nombre_corto') ?: config('app.name', 'ISTCumandá');
        @endphp
        {{ $nombreCorto }}  --}}
         Zona Administrativa
    </div>
    <!-- SPACER -->
    <div class = "grow h-full flex items-center justify-center"></div>
    <div class = "flex-none h-full text-center flex items-center justify-center">

        <div class = "flex md:space-x-3 items-center md:px-3">

            <div class = "flex-none flex md:justify-center">
                <div class="relative hidden md:block">
                    <x-dropdown align="left" width="48">
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

                            <x-dropdown-link href="{{ route('administracion.administrativa.users.profile') }}">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            @canany(['acceso_docencia', 'acceso_estudiantil'])
                            <div class="border-t border-gray-100 pt-1">
                                <div class="block px-4 py-1.5 text-xs text-gray-400 font-medium uppercase tracking-wide">Mis portales</div>
                                @can('acceso_docencia')
                                <x-dropdown-link href="{{ route('administracion.docencia.dashboard') }}" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Portal Docente
                                </x-dropdown-link>
                                @endcan
                                @can('acceso_estudiantil')
                                <x-dropdown-link href="{{ route('administracion.estudiantil.dashboard') }}" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                    Portal Estudiantil
                                </x-dropdown-link>
                                @endcan
                            </div>
                            @endcanany

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                <div class="relative md:hidden">
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

                            <x-dropdown-link href="{{ route('administracion.administrativa.users.profile') }}">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            @canany(['acceso_docencia', 'acceso_estudiantil'])
                            <div class="border-t border-gray-100 pt-1">
                                <div class="block px-4 py-1.5 text-xs text-gray-400 font-medium uppercase tracking-wide">Mis portales</div>
                                @can('acceso_docencia')
                                <x-dropdown-link href="{{ route('administracion.docencia.dashboard') }}" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Portal Docente
                                </x-dropdown-link>
                                @endcan
                                @can('acceso_estudiantil')
                                <x-dropdown-link href="{{ route('administracion.estudiantil.dashboard') }}" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                    Portal Estudiantil
                                </x-dropdown-link>
                                @endcan
                            </div>
                            @endcanany

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- <div class = "hidden md:block text-sm md:text-md text-black dark:text-white">{{ Auth::user()->name }}</div> --}}
            <div class="hidden md:block text-sm md:text-md text-slate-700 dark:text-slate-200 font-medium">
                {{ Auth::user()->name }}
            </div>
        </div>

    </div>
</div>
