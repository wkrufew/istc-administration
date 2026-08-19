<div class="space-y-4">

    {{-- ═══════════════════════════════════════
         UPLOAD CARD
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-900/[0.04] dark:ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

        <div class="px-6 py-6 sm:px-8">

            {{-- Section header + download button --}}
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-[0.65rem] font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-400 whitespace-nowrap">
                        Carga masiva de usuarios
                    </span>
                    <div class="h-px w-12 bg-slate-200 dark:bg-white/[0.05]"></div>
                </div>

                <button wire:click="downloadTemplate"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-medium tracking-wide text-white/90
                           bg-gradient-to-r from-emerald-800/70 to-teal-800/60 border border-emerald-500/25
                           hover:from-emerald-700/80 hover:to-teal-700/70
                           hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-900/30
                           active:translate-y-0 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3" />
                    </svg>
                    Descargar Plantilla .xlsx
                </button>
            </div>

            {{-- Instructions strip --}}
            <div class="mb-5 flex flex-wrap gap-x-5 gap-y-1.5 px-4 py-3 rounded-xl bg-sky-500/[0.05] border border-sky-500/15">
                <p class="text-[0.72rem] text-sky-700 dark:text-sky-400/80 flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-sky-500 flex-shrink-0"></span>
                    Campos obligatorios: <strong class="font-semibold">first_name, last_name, cedula</strong>
                </p>
                <p class="text-[0.72rem] text-sky-700 dark:text-sky-400/80 flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-sky-500 flex-shrink-0"></span>
                    Booleanos: escribe <strong class="font-semibold">si</strong> o <strong class="font-semibold">no</strong>
                </p>
                <p class="text-[0.72rem] text-sky-700 dark:text-sky-400/80 flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-sky-500 flex-shrink-0"></span>
                    Fechas en formato <strong class="font-semibold">AAAA-MM-DD</strong>
                </p>
                <p class="text-[0.72rem] text-sky-700 dark:text-sky-400/80 flex items-center gap-1.5">
                    <span class="w-1 h-1 rounded-full bg-sky-500 flex-shrink-0"></span>
                    Email y contraseña se auto-generan si están vacíos
                </p>
            </div>

            {{-- Success message --}}
            @if ($successMessage)
                <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/[0.08] border border-emerald-500/20">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ $successMessage }}</p>
                </div>
            @endif

            {{-- Error list --}}
            @if (count($importErrors) > 0)
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-500/[0.06] border border-red-500/20">
                    <div class="flex items-center gap-2 mb-2.5">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                            {{ count($importErrors) }} error(es) encontrado(s)
                        </span>
                    </div>
                    <ul class="space-y-1 pl-1 max-h-40 overflow-y-auto">
                        @foreach ($importErrors as $error)
                            <li class="text-xs text-red-600 dark:text-red-400/80 flex items-start gap-1.5">
                                <span class="mt-1.5 w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Upload form --}}
            <form wire:submit.prevent="import">

                <div
                    x-data="{
                        dragging: false,
                        hasFile: {{ $file ? 'true' : 'false' }},
                        fileName: @js($file ? $file->getClientOriginalName() : null),
                        handleDrop(e) {
                            this.dragging = false;
                            const file = e.dataTransfer.files[0];
                            if (!file) return;
                            const input = this.$refs.fileInput;
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            input.files = dt.files;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                            this.hasFile = true;
                            this.fileName = file.name;
                        }
                    }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    {{-- Drop zone --}}
                    <label for="fileUploadInput"
                        :class="dragging
                            ? 'border-lime-500/60 bg-lime-500/[0.04] scale-[1.01]'
                            : 'border-slate-200 dark:border-white/[0.08] bg-slate-50 dark:bg-slate-800/40 hover:border-lime-500/40 hover:bg-lime-500/[0.02]'"
                        class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed rounded-xl cursor-pointer transition-all duration-200 group">

                        <div class="flex flex-col items-center justify-center py-8 px-4 text-center pointer-events-none">
                            <div :class="dragging ? 'text-lime-500' : 'text-slate-400 dark:text-slate-500 group-hover:text-lime-500/70'"
                                class="mb-3 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>

                            <template x-if="!hasFile">
                                <div>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                                        <span class="text-lime-600 dark:text-lime-400">Haz clic para seleccionar</span>
                                        o arrastra tu archivo aquí
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">
                                        Excel (.xlsx, .xls) · Máx. 10 MB
                                    </p>
                                </div>
                            </template>

                            <template x-if="hasFile">
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-lime-500/[0.08] border border-lime-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-lime-500 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm text-lime-600 dark:text-lime-400 font-medium" x-text="fileName"></span>
                                </div>
                            </template>

                            <div wire:loading wire:target="file" class="flex items-center gap-2 mt-3">
                                <svg class="animate-spin w-4 h-4 text-lime-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span class="text-xs text-slate-500 dark:text-slate-400">Cargando archivo...</span>
                            </div>
                        </div>

                        <input
                            x-ref="fileInput"
                            type="file"
                            id="fileUploadInput"
                            wire:model="file"
                            accept=".xlsx,.xls,.csv"
                            class="hidden"
                            @change="hasFile = $event.target.files.length > 0; fileName = $event.target.files[0]?.name || null"
                        />
                    </label>

                    @error('file')
                        <p class="mt-1.5 text-xs text-red-500 dark:text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                            </svg>
                            <strong>{{ $message }}</strong>
                        </p>
                    @enderror

                    {{-- Importing overlay --}}
                    <div wire:loading wire:target="import"
                        class="mt-4 flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-sky-500/[0.06] border border-sky-500/15">
                        <svg class="animate-spin w-4 h-4 text-sky-500 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span class="text-sm text-sky-600 dark:text-sky-400 font-medium">
                            Procesando importación, por favor espera…
                        </span>
                    </div>

                    {{-- Footer actions --}}
                    <div class="mt-5 flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-white/[0.05]">

                        <a href="{{ route('administracion.administrativa.estudiantes.index') }}"
                            class="px-5 py-2 rounded-full text-xs font-medium tracking-wide
                                   text-slate-500 dark:text-slate-400
                                   border border-slate-200 dark:border-white/[0.08]
                                   hover:bg-slate-100 dark:hover:bg-white/5
                                   hover:text-slate-700 dark:hover:text-slate-200
                                   transition-all duration-200">
                            Cancelar
                        </a>

                        <button type="submit"
                            :disabled="!hasFile"
                            wire:loading.attr="disabled"
                            wire:target="import"
                            class="inline-flex items-center gap-2 px-6 py-2 rounded-full text-xs font-medium tracking-widest uppercase text-white/90
                                   bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                                   border border-lime-500/25
                                   hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                                   hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                                   active:translate-y-0 transition-all duration-200
                                   disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                            <span wire:loading.remove wire:target="import" class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Importar Usuarios
                            </span>
                            <span wire:loading wire:target="import" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Importando…
                            </span>
                        </button>

                    </div>

                </div>
            </form>

        </div>

        <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>
    </div>

    {{-- ═══════════════════════════════════════
         IMPORT SUMMARY STATS
    ═══════════════════════════════════════ --}}
    @if ($importedCount > 0 || count($importErrors) > 0)
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/[0.06] p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 dark:text-slate-400">Importados</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $importedCount }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-white/[0.06] p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 dark:text-slate-400">Errores</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ count($importErrors) }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════
         FIELD REFERENCE TABLE
    ═══════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-900/[0.04] dark:ring-white/[0.04]">

        <div class="h-px bg-gradient-to-r from-transparent via-sky-500/30 to-transparent"></div>

        <div class="px-6 py-5 sm:px-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-[0.65rem] font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-400 whitespace-nowrap">
                    Referencia de campos
                </span>
                <div class="flex-1 h-px bg-slate-200 dark:bg-white/[0.05]"></div>
                <span class="px-2.5 py-0.5 rounded-full text-[0.62rem] tracking-wide bg-sky-500/10 border border-sky-500/20 text-sky-600 dark:text-sky-400">
                    26 columnas
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-white/[0.05]">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-white/[0.05]">
                            <th class="px-3 py-2.5 text-left text-[0.65rem] font-medium tracking-wider text-slate-500 dark:text-slate-400 uppercase w-8">#</th>
                            <th class="px-3 py-2.5 text-left text-[0.65rem] font-medium tracking-wider text-slate-500 dark:text-slate-400 uppercase">Campo</th>
                            <th class="px-3 py-2.5 text-left text-[0.65rem] font-medium tracking-wider text-slate-500 dark:text-slate-400 uppercase">Estado</th>
                            <th class="px-3 py-2.5 text-left text-[0.65rem] font-medium tracking-wider text-slate-500 dark:text-slate-400 uppercase">Ejemplo / Tipo</th>
                            <th class="px-3 py-2.5 text-left text-[0.65rem] font-medium tracking-wider text-slate-500 dark:text-slate-400 uppercase">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/[0.03]">
                        @php
                        $fields = [
                            [1,  'first_name',               true,  'Juan',                   'Nombre(s) del usuario'],
                            [2,  'last_name',                true,  'Pérez',                  'Apellido(s) del usuario'],
                            [3,  'cedula',                   true,  '0601234567',             'Cédula de identidad · debe ser único'],
                            [4,  'rol',                      false, 'Estudiante',             'Rol del sistema · default: Estudiante'],
                            [5,  'email',                    false, 'juan@email.com',         'Correo · se genera automáticamente si está vacío'],
                            [6,  'password',                 false, '12345678',               'Contraseña · default: 12345678'],
                            [7,  'phone',                    false, '0999123456',             'Teléfono celular'],
                            [8,  'address',                  false, 'Av. Principal 123',      'Dirección domiciliaria'],
                            [9,  'fecha_nacimiento',         false, '1990-01-15',             'Fecha de nacimiento · formato AAAA-MM-DD'],
                            [10, 'matricula_numero',         false, 'MAT-2024-001',           'Número de matrícula'],
                            [11, 'padre',                    false, 'Pedro Pérez',            'Nombre del padre'],
                            [12, 'madre',                    false, 'María López',            'Nombre de la madre'],
                            [13, 'tutor',                    false, '(vacío)',                'Nombre del tutor legal'],
                            [14, 'nacionalidad',             false, 'Ecuatoriano',            'Nacionalidad'],
                            [15, 'genero',                   false, 'Masculino',              'Masculino · Femenino · Otro'],
                            [16, 'estado_civil',             false, 'Soltero',                'Estado civil'],
                            [17, 'telefono_emergencia',      false, '0988123456',             'Teléfono del contacto de emergencia'],
                            [18, 'contacto_emergencia',      false, 'María López',            'Nombre del contacto de emergencia'],
                            [19, 'tipo_sangre',              false, 'O+',                     'Grupo sanguíneo'],
                            [20, 'observaciones_medicas',    false, '(vacío)',                'Notas médicas relevantes'],
                            [21, 'is_facturador',            false, 'no',                     'Tiene datos de facturación · si / no'],
                            [22, 'fact_nombre',              false, '(vacío)',                'Nombre o razón social para factura'],
                            [23, 'fact_documento',           false, '(vacío)',                'RUC o cédula para factura'],
                            [24, 'fact_correo',              false, '(vacío)',                'Correo electrónico para factura'],
                            [25, 'fact_direccion',           false, '(vacío)',                'Dirección para factura'],
                            [26, 'fact_telefono',            false, '(vacío)',                'Teléfono para factura'],
                        ];
                        @endphp
                        @foreach ($fields as [$num, $field, $required, $example, $desc])
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/[0.015] transition-colors">
                                <td class="px-3 py-2 text-slate-400 dark:text-slate-600 font-mono tabular-nums">
                                    {{ str_pad($num, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-3 py-2 font-mono text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    {{ $field }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if ($required)
                                        <span class="px-2 py-0.5 rounded-full text-[0.6rem] font-medium bg-red-500/[0.08] border border-red-500/20 text-red-600 dark:text-red-400">
                                            Obligatorio
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[0.6rem] font-medium bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-white/[0.05] text-slate-500 dark:text-slate-400">
                                            Opcional
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap">
                                    {{ $example }}
                                </td>
                                <td class="px-3 py-2 text-slate-600 dark:text-slate-400">
                                    {{ $desc }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>
    </div>

</div>
