<x-admin-layout>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        {{-- ═══════════════════════════════════════
             HEADER
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

            <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Importar Usuarios</h1>
                        <p class="text-xs text-lime-600 dark:text-lime-400/70 tracking-widest uppercase mt-1">Administración · Estudiantes</p>
                    </div>
                </div>

                <a href="{{ route('administracion.administrativa.estudiantes.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full
                           border border-slate-200 dark:border-white/10
                           bg-slate-100 dark:bg-white/5
                           text-slate-600 dark:text-slate-300 text-xs font-medium tracking-wide
                           hover:bg-slate-200 dark:hover:bg-white/10
                           hover:text-slate-800 dark:hover:text-white
                           hover:-translate-x-0.5 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>

            </div>
        </div>

        @livewire('administration.import-users')

    </div>

</x-admin-layout>
