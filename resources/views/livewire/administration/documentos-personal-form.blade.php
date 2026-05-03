<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ── HEADER ────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('administracion.administrativa.docentes.index') }}"
                    class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z"
                            clip-rule="evenodd" />
                    </svg>
                    Docentes
                </a>
                <span class="text-gray-300 dark:text-gray-700">/</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Documentos</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $esEdicion ? 'Actualizar' : 'Cargar' }} Documentos
            </h1>
        </div>

        {{-- Estado del expediente --}}
        @php
            $total = collect(['file_curriculum', 'file_senescyt', 'file_contrato', 'file_otro'])
                ->filter(fn($f) => $document?->$f)
                ->count();
        @endphp
        <div @class([
            'inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border',
            'bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-800 text-green-700 dark:text-green-300' =>
                $total === 4,
            'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300' =>
                $total > 0 && $total < 4,
            'bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400' =>
                $total === 0,
        ])>
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75-6.75a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z"
                    clip-rule="evenodd" />
                <path
                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
            </svg>
            Expediente: {{ $total }} / 4 documentos
        </div>
    </div>

    {{-- ── INFO DEL DOCENTE ───────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 shrink-0 rounded-xl bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                <span class="text-blue-700 dark:text-blue-300 font-bold text-lg">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </span>
            </div>
            <div class="min-w-0">
                <p class="text-base font-bold text-gray-900 dark:text-gray-100 truncate">
                    {{ $user->name }}
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-0.5">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                    @if ($user->cedula)
                        <span class="text-xs text-gray-400 dark:text-gray-500">CI: {{ $user->cedula }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── ERRORES GLOBALES ───────────────────────────────────────────────────── --}}
    @if ($errors->has('files'))
        <div
            class="flex items-start gap-3 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 px-4 py-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-semibold text-red-700 dark:text-red-300">{{ $errors->first('files') }}</p>
        </div>
    @endif

    {{-- ── SLOTS DE DOCUMENTOS ────────────────────────────────────────────────── --}}
    @php
        $slots = [
            [
                'prop' => 'fileCurriculum',
                'column' => 'file_curriculum',
                'label' => 'Hoja de Vida (Curriculum)',
                'desc' => 'Curriculum vitae actualizado del docente.',
                'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM1.5 20.25a6.75 6.75 0 0 1 13.5 0H1.5Z',
            ],
            [
                'prop' => 'fileSenescyt',
                'column' => 'file_senescyt',
                'label' => 'Título Senescyt',
                'desc' => 'Registro de título en la Senescyt.',
                'icon' =>
                    'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
            ],
            [
                'prop' => 'fileContrato',
                'column' => 'file_contrato',
                'label' => 'Contrato',
                'desc' => 'Contrato laboral vigente.',
                'icon' =>
                    'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z',
            ],
            [
                'prop' => 'fileOtro',
                'column' => 'file_otro',
                'label' => 'Cédula de Identidad',
                'desc' => 'Copia de cédula de identidad del docente.',
                'icon' =>
                    'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($slots as $slot)
            @php
                $prop = $slot['prop'];
                $column = $slot['column'];
                $tieneActual = (bool) $document?->$column;
                $hayNuevo = (bool) $this->$prop;
            @endphp

            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <div class="flex items-center gap-3">
                        <div @class([
                            'h-9 w-9 rounded-xl flex items-center justify-center shrink-0',
                            'bg-green-100 dark:bg-green-950' => $tieneActual,
                            'bg-gray-100 dark:bg-gray-800' => !$tieneActual,
                        ])>
                            <svg @class([
                                'w-4 h-4',
                                'text-green-600 dark:text-green-400' => $tieneActual,
                                'text-gray-400 dark:text-gray-600' => !$tieneActual,
                            ]) xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $slot['icon'] }}" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                {{ $slot['label'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">
                                {{ $slot['desc'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Estado badge --}}
                    @if ($tieneActual && !$hayNuevo)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-300 shrink-0">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.333a1 1 0 0 1-1.425.006L3.29 9.704A1 1 0 1 1 4.704 8.29l3.01 3.01 6.54-6.61a1 1 0 0 1 1.45.6Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Cargado
                        </span>
                    @elseif ($hayNuevo)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 shrink-0">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M9.25 13.25a.75.75 0 0 0 1.5 0V4.636l2.955 3.129a.75.75 0 0 0 1.09-1.03l-4.25-4.5a.75.75 0 0 0-1.09 0l-4.25 4.5a.75.75 0 1 0 1.09 1.03L9.25 4.636v8.614Z" />
                                <path
                                    d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                            </svg>
                            Listo para guardar
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500 shrink-0">
                            Sin archivo
                        </span>
                    @endif
                </div>

                {{-- Archivo actual --}}
                @if ($tieneActual && !$hayNuevo)
                    <div
                        class="mx-5 mb-3 flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800/50 px-3 py-2">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z"
                                clip-rule="evenodd" />
                            <path
                                d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                        </svg>
                        <a href="{{ asset('storage/' . $document->$column) }}" target="_blank"
                            class="text-xs font-semibold text-green-700 dark:text-green-300 hover:underline truncate">
                            Ver archivo actual
                        </a>
                        <svg class="w-3 h-3 text-green-500 shrink-0 ml-auto" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.06l1.224-1.224a4 4 0 0 0-5.656-5.656l-3 3a4 4 0 0 0 .225 5.865.75.75 0 0 0 .977-1.138 2.5 2.5 0 0 1-.142-3.667l3-3Z" />
                            <path
                                d="M11.603 7.963a.75.75 0 0 0-.977 1.138 2.5 2.5 0 0 1 .142 3.667l-3 3a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.06l-1.224 1.224a4 4 0 1 0 5.656 5.656l3-3a4 4 0 0 0-.225-5.865Z" />
                        </svg>
                    </div>
                @elseif ($hayNuevo)
                    <div
                        class="mx-5 mb-3 flex items-center gap-2 rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50 px-3 py-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z"
                                clip-rule="evenodd" />
                            <path
                                d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                        </svg>
                        <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 truncate">
                            {{ $this->$prop?->getClientOriginalName() }}
                        </span>
                    </div>
                @endif

                {{-- Input de archivo --}}
                <div class="px-5 pb-5">
                    <div class="relative">
                        <input wire:model="{{ $prop }}" type="file" accept="application/pdf"
                            class="block w-full text-sm text-gray-600 dark:text-gray-400
                                   file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-xs file:font-bold file:cursor-pointer
                                   file:bg-blue-600 file:text-white hover:file:bg-blue-700
                                   dark:file:bg-blue-600 dark:hover:file:bg-blue-700
                                   cursor-pointer
                                   rounded-xl border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-950
                                   py-2 pr-3
                                   focus:outline-none transition" />
                    </div>

                    {{-- Progress bar --}}
                    <div wire:loading wire:target="{{ $prop }}" class="mt-2">
                        <div class="h-1.5 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full animate-pulse w-3/4"></div>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Cargando archivo…</p>
                    </div>

                    @error($prop)
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                    @enderror

                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-1.5">Solo PDF — máximo 10 MB</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── ACCIONES ────────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('administracion.administrativa.docentes.index') }}"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700
                   bg-white dark:bg-gray-950 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200
                   hover:bg-gray-50 dark:hover:bg-gray-800 transition">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z"
                    clip-rule="evenodd" />
            </svg>
            Volver a Docentes
        </a>

        <button wire:click="save" wire:loading.attr="disabled"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700
                   disabled:opacity-60 disabled:cursor-not-allowed
                   px-6 py-2.5 text-sm font-bold text-white shadow-sm transition">
            <span wire:loading.remove wire:target="save">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2H5Zm7-14v4h4V6h-4Z" />
                </svg>
            </span>
            <span wire:loading wire:target="save">
                <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z">
                    </path>
                </svg>
            </span>
            <span wire:loading.remove wire:target="save">Guardar Documentos</span>
            <span wire:loading wire:target="save">Guardando…</span>
        </button>
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('toast', ({
                    message,
                    type
                }) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        didOpen: (t) => {
                            t.onmouseenter = Swal.stopTimer;
                            t.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: type,
                        title: message
                    });
                });
            });
        </script>
    @endpush
</div>
