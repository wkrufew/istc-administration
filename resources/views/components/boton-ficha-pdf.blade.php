@props(['url', 'financiero' => false])
{{--
    Botón "PDF de la ficha". Abre el PDF en una pestaña nueva (desde ahí se imprime o descarga).
    Con financiero=true y permiso ver_obligaciones_financieras ofrece la casilla
    "Incluir resumen financiero", desmarcada por defecto. El servidor vuelve a verificar el permiso.
--}}
@php $ofrecerFinanciero = $financiero && auth()->user()?->can('ver_obligaciones_financieras'); @endphp

@if ($ofrecerFinanciero)
    <div x-data="{ abierto: false, financiero: false }" class="relative inline-block" @keydown.escape.window="abierto = false">
        <button type="button" @click="abierto = ! abierto" :aria-expanded="abierto"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/40">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M6 20h12a2 2 0 002-2V8l-6-6H6a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            PDF de la ficha
        </button>
        <div x-show="abierto" x-cloak x-transition.opacity @click.outside="abierto = false"
             class="absolute right-0 z-30 mt-2 w-64 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg p-3 space-y-3">
            <label class="flex items-start gap-2 text-xs text-slate-700 dark:text-slate-200 cursor-pointer">
                <input type="checkbox" x-model="financiero" class="mt-0.5 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                <span>Incluir resumen financiero<span class="block text-[11px] text-slate-400">Saldos, cuotas vencidas, becas y convenios.</span></span>
            </label>
            <a :href="'{{ $url }}' + (financiero ? '?financiero=1' : '')" target="_blank" rel="noopener" @click="abierto = false"
               class="flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700">
                Generar PDF
            </a>
        </div>
    </div>
@else
    <a href="{{ $url }}" target="_blank" rel="noopener"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/40">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M6 20h12a2 2 0 002-2V8l-6-6H6a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        PDF de la ficha
    </a>
@endif
