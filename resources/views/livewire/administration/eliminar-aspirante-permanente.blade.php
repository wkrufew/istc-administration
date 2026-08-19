<div>
    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('administracion.administrativa.aspirantes.papelera') }}"
           class="p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Eliminación permanente</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Esta acción no se puede deshacer</p>
        </div>
    </div>

    <div class="max-w-lg mx-auto p-6">
        @if($eliminado)
        {{-- ══ ÉXITO ═══════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 text-center">
            <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aspirante eliminado permanentemente</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">El registro fue borrado de forma definitiva del sistema.</p>
            <a href="{{ route('administracion.administrativa.aspirantes.papelera') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-sm font-medium hover:bg-slate-700 dark:hover:bg-slate-600 transition-colors">
                Volver a la papelera
            </a>
        </div>
        @else
        {{-- ══ FORMULARIO ══════════════════════════════════════════════════ --}}

        {{-- Datos del aspirante --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 mb-4">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Aspirante a eliminar</p>
            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $aspirante->user->name }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $aspirante->user->email }}</p>
            <p class="text-sm text-slate-500">{{ $aspirante->user->cedula }}</p>
            @if($aspirante->cohorte)
            <p class="text-xs text-slate-400 mt-2">
                {{ $aspirante->cohorte->nombre }} · {{ $aspirante->cohorte->carrera->name ?? '—' }}
            </p>
            @endif
        </div>

        {{-- Advertencia --}}
        <div class="flex gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 mb-5">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <div class="text-sm text-red-700 dark:text-red-300">
                <p class="font-semibold mb-1">Esta acción es irreversible</p>
                <p class="leading-relaxed">Se eliminarán permanentemente el aspirante y su cuenta de usuario. No podrás recuperarlos.</p>
            </div>
        </div>

        {{-- Código de seguridad --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Para confirmar, escribe el siguiente código en el campo de abajo:
            </p>

            <div class="flex justify-center mb-5">
                <span class="text-5xl font-black tracking-[0.25em] text-red-600 dark:text-red-400 select-none font-mono">
                    {{ $codigoSeguridad }}
                </span>
            </div>

            <div class="mb-4">
                <input wire:model="codigoIngresado"
                       type="text"
                       inputmode="numeric"
                       maxlength="2"
                       placeholder="_ _"
                       autocomplete="off"
                       class="w-full text-center text-2xl font-bold tracking-widest rounded-xl border py-3
                              focus:outline-none focus:ring-2 focus:ring-red-500/40 transition-colors font-mono
                              {{ $errors->has('codigoIngresado') ? 'border-red-400 bg-red-50 dark:bg-red-900/20 text-red-700' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                @error('codigoIngresado')
                    <p class="text-xs text-red-500 text-center mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('administracion.administrativa.aspirantes.papelera') }}"
                   class="flex-1 text-center px-4 py-2.5 rounded-xl text-sm font-medium
                          text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-600
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </a>
                <button wire:click="confirmar" wire:loading.attr="disabled"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                               bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white text-sm font-medium
                               transition-colors shadow-sm">
                    <span wire:loading.remove wire:target="confirmar">Eliminar definitivamente</span>
                    <span wire:loading wire:target="confirmar">Eliminando…</span>
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
