@use('App\Support\FichaPersona', 'F')
@php
    $input = 'w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/70 px-3.5 py-2.5 text-sm
              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600
              focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 transition-all';
    $label = 'block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5';
    $requeridos = F::camposRequeridos($user);

    // [campo, tipo, opciones, ancho completo]
    $secciones = [
        'personales' => [
            'Identidad' => [
                ['first_name', 'text'], ['last_name', 'text'], ['cedula', 'text'], ['email', 'email'],
                ['phone', 'text'], ['fecha_nacimiento', 'date'], ['matricula_numero', 'text'],
            ],
            'Datos personales' => [
                ['sexo', 'select', F::SEXO], ['genero', 'select', F::GENERO], ['estado_civil', 'select', F::ESTADO_CIVIL],
                ['tipo_sangre', 'select', F::TIPO_SANGRE], ['etnia', 'select', F::ETNIA], ['pueblo_nacionalidad', 'text'],
                ['nacionalidad', 'select', F::PAISES],
            ],
            'Discapacidad' => [
                ['tiene_discapacidad', 'bool'], ['tipo_discapacidad', 'select', F::DISCAPACIDAD],
                ['porcentaje_discapacidad', 'number'], ['nro_conadis', 'text'], ['observaciones_medicas', 'textarea', null, true],
            ],
            'Nacimiento y residencia' => [
                ['provincia_nacimiento', 'text'], ['canton_nacimiento', 'text'], ['pais_residencia', 'select', F::PAISES],
                ['provincia_residencia', 'text'], ['canton_residencia', 'text'], ['address', 'text', null, true],
            ],
        ],
        'socioeconomicos' => [
            'Educación y trabajo' => [
                ['tipo_colegio', 'select', F::TIPO_COLEGIO], ['nombre_colegio', 'text'], ['ocupacion', 'text'], ['empleo_ingresos', 'text'],
            ],
            'Hogar' => [
                ['bono_dh', 'select', ['SI' => 'Sí', 'NO' => 'No']], ['ingresos_hogar', 'text'], ['miembros_hogar', 'number'],
            ],
        ],
        'familia' => [
            'Padres y tutor' => [
                ['padre', 'text'], ['formacion_padre', 'select', F::FORMACION], ['madre', 'text'],
                ['formacion_madre', 'select', F::FORMACION], ['tutor', 'text'],
            ],
            'Contacto de emergencia' => [
                ['contacto_emergencia', 'text'], ['telefono_emergencia', 'text'], ['parentesco_emergencia', 'select', F::PARENTESCO],
            ],
        ],
        'facturacion' => [
            'Facturación' => [
                ['is_facturador', 'bool'], ['fact_nombre', 'text'], ['fact_documento', 'text'], ['fact_correo', 'email'],
                ['fact_direccion', 'text'], ['fact_telefono', 'text'],
            ],
        ],
    ];

    // Campos que se ocultan cuando no aplican
    $oculto = fn($campo) => match (true) {
        $campo === 'pueblo_nacionalidad' => ($f['etnia'] ?? null) !== 'Indígena',
        in_array($campo, ['tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis'], true) => ! ($f['tiene_discapacidad'] ?? false),
        str_starts_with($campo, 'fact_') => (bool) ($f['is_facturador'] ?? false),
        default => false,
    };
    $etiquetaBool = ['tiene_discapacidad' => '¿Tiene alguna discapacidad?', 'is_facturador' => 'La factura sale a su propio nombre'];

    $estadoColor = [
        'incompleta'        => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
        'sin_revisar'       => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'en_revision'       => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
        'con_observaciones' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'aprobada'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
    ][$estadoFicha];
    $moodleActivo = (int) env('MOODLE_MODE', 0) === 1;
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-5">

    {{-- ══ CABECERA ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm px-6 py-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4 min-w-0">
                @if ($profile_photo_actual)
                    <img src="{{ Storage::url($profile_photo_actual) }}" alt="" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-slate-200 dark:ring-slate-600 flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        {{ mb_strtoupper(mb_substr($user->first_name ?: $user->name, 0, 1) . mb_substr($user->last_name ?? '', 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 leading-tight truncate">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        C.I. {{ $user->cedula ?? '—' }} · {{ $user->email }}
                        @if ($carrera) · {{ $carrera }} @endif
                        @if ($user->estado_academico) · {{ $user->estado_academico }} @endif
                    </p>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoColor }}">Ficha: {{ F::ESTADOS_FICHA[$estadoFicha] }}</span>
                        @if ($faltantes)
                            <span class="text-xs text-amber-700 dark:text-amber-400" title="{{ implode(', ', $faltantes) }}">Faltan {{ count($faltantes) }} dato(s) obligatorio(s)</span>
                        @endif
                        @if ($user->ficha_revisada_at && $user->ficha_estado)
                            <span class="text-[11px] text-slate-400">Revisada {{ $user->ficha_revisada_at->format('d/m/Y') }}@if ($revisadaPor) por {{ $revisadaPor }}@endif</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('administracion.administrativa.estudiantes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>

        @if ($user->ficha_estado === 'con_observaciones' && $user->ficha_observacion)
            <p class="mt-3 text-xs rounded-lg px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300">Observación: {{ $user->ficha_observacion }}</p>
        @endif

        {{-- Revisión y accesos --}}
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-wrap items-center gap-2">
            @if ($estadoFicha !== 'aprobada')
                <button wire:click="aprobarFicha" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700">Aprobar ficha</button>
                <button wire:click="$set('showObservar', true)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 hover:bg-red-100">Marcar con observaciones</button>
            @else
                <button wire:click="reabrirFicha" wire:confirm="¿Reabrir la ficha? Volverá a quedar sin revisar."
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700">Reabrir ficha</button>
            @endif
            <span class="flex-1"></span>
            <x-boton-ficha-pdf :url="route('administracion.administrativa.estudiantes.ficha-pdf', $user)" :financiero="true" />
            @if ($this->aplicaReenvioCredenciales())
                @can('reenviar_credenciales')
                <button type="button" x-data
                    @click="Swal.fire({ title: '¿Reenviar credenciales?', html: 'La contraseña será restablecida al número de cédula y se enviará un correo con las nuevas credenciales.', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, reenviar', cancelButtonText: 'Cancelar' }).then(r => r.isConfirmed && $wire.reenviarCredenciales())"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 hover:bg-amber-100">
                    Reenviar credenciales
                </button>
                @endcan
            @endif
            @can('moodle_gestion')
                @if ($moodleActivo)
                <a href="{{ route('administracion.administrativa.estudiantes.moodle', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100">Moodle</a>
                @endif
            @endcan
            @can('gestionar_usuarios')
            <a href="{{ route('administracion.administrativa.users.edit', $user) }}" title="Rol, correo de acceso y estado de la cuenta"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700">Cuenta</a>
            @endcan
        </div>
    </div>

    {{-- ══ PESTAÑAS ══════════════════════════════════════════════════════════ --}}
    <div class="flex gap-1 overflow-x-auto pb-1 -mx-1 px-1">
        @foreach (\App\Livewire\Administration\Estudiantes\FichaEstudiante::TABS as $clave => $nombre)
            @php $conFaltantes = isset($secciones[$clave]) && collect($secciones[$clave])->flatten(1)->pluck(0)->intersect($requeridos)->contains(fn($c) => blank($user->$c)); @endphp
            <button wire:click="cambiarTab('{{ $clave }}')"
                    class="relative whitespace-nowrap px-4 py-2 rounded-xl text-sm font-medium transition-colors
                           {{ $tab === $clave ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                {{ $nombre }}
                @if ($conFaltantes) <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-500" title="Faltan datos obligatorios"></span> @endif
            </button>
        @endforeach
    </div>

    {{-- ══ PESTAÑAS DE DATOS ═════════════════════════════════════════════════ --}}
    @if (isset($secciones[$tab]))
        <form wire:submit.prevent="guardar" autocomplete="off" novalidate class="space-y-5">
            @if ($tab === 'personales')
                @include('livewire.administration.partials.foto-perfil')
            @endif

            @foreach ($secciones[$tab] as $titulo => $campos)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3">
                        <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">{{ $titulo }}</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($campos as $def)
                            @php [$campo, $tipo] = $def; $opciones = $def[2] ?? []; $ancho = $def[3] ?? false; @endphp
                            @continue($oculto($campo))
                            <div class="{{ $ancho || $tipo === 'bool' ? 'sm:col-span-2' : '' }}" wire:key="campo-{{ $campo }}">
                                @if ($tipo === 'bool')
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model.live="f.{{ $campo }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $etiquetaBool[$campo] ?? F::ETIQUETAS[$campo] }}</span>
                                    </label>
                                @else
                                    <label class="{{ $label }}">
                                        {{ F::ETIQUETAS[$campo] ?? $campo }}
                                        @if (in_array($campo, $requeridos, true) || in_array($campo, ['first_name', 'last_name', 'cedula', 'email'], true))
                                            <span class="{{ blank($user->$campo) ? 'text-amber-500' : 'text-slate-300 dark:text-slate-600' }}">*</span>
                                        @endif
                                    </label>
                                    @if ($tipo === 'select')
                                        @php
                                            $lista = array_is_list($opciones) ? array_combine($opciones, $opciones) : $opciones;
                                            $actual = $f[$campo] ?? null;
                                        @endphp
                                        <select wire:model{{ $campo === 'etnia' ? '.live' : '' }}="f.{{ $campo }}" class="{{ $input }}">
                                            <option value="">— Selecciona —</option>
                                            {{-- Un valor guardado que no está en la lista se conserva --}}
                                            @if ($actual !== null && $actual !== '' && ! array_key_exists($actual, $lista))
                                                <option value="{{ $actual }}">{{ $actual }}</option>
                                            @endif
                                            @foreach ($lista as $valor => $texto)
                                                <option value="{{ $valor }}">{{ $texto }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($tipo === 'textarea')
                                        <textarea wire:model="f.{{ $campo }}" rows="2" class="{{ $input }}"></textarea>
                                    @else
                                        <input type="{{ $tipo }}" wire:model="f.{{ $campo }}" class="{{ $input }}" @if ($tipo === 'number') min="0" @endif>
                                    @endif
                                    @error("f.{$campo}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if ($tab === 'personales')
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3">
                        <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Acceso</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Nueva contraseña <span class="font-normal text-slate-400">(opcional)</span></label>
                            <input type="password" wire:model="password" autocomplete="new-password" placeholder="Déjala vacía para no cambiarla" class="{{ $input }}">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Confirmar contraseña</label>
                            <input type="password" wire:model="password_confirmation" autocomplete="new-password" class="{{ $input }}">
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer sm:col-span-2">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700 dark:text-slate-200">Cuenta activa</span>
                        </label>
                    </div>
                </div>
            @endif

            @can('editar_usuarios')
            <div class="sticky bottom-4 flex justify-end">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-lg disabled:opacity-60">
                    <span wire:loading.remove wire:target="guardar">Guardar ficha</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </button>
            </div>
            @endcan
        </form>
    @endif

    {{-- ══ ACADÉMICO ═════════════════════════════════════════════════════════ --}}
    @if ($tab === 'academico')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Matrículas</h3>
                <span class="text-xs text-slate-500">Estado académico: <strong>{{ $user->estado_academico ?? '—' }}</strong></span>
            </div>
            @if ($matriculas->isEmpty())
                <p class="px-6 py-8 text-sm text-center text-slate-400">Sin matrículas registradas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-slate-500 dark:text-slate-400 bg-slate-50/60 dark:bg-slate-900/30">
                            <tr><th class="px-6 py-2 text-left">Código</th><th class="px-6 py-2 text-left">Período</th><th class="px-6 py-2 text-left">Carrera</th><th class="px-6 py-2 text-left">Tipo</th><th class="px-6 py-2 text-left">Estado</th><th class="px-6 py-2 text-left">Fecha</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($matriculas as $m)
                                <tr>
                                    <td class="px-6 py-2.5 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $m->code }}</td>
                                    <td class="px-6 py-2.5 text-slate-700 dark:text-slate-200">{{ $m->periodo?->code ?? '—' }}</td>
                                    <td class="px-6 py-2.5 text-slate-700 dark:text-slate-200">{{ $m->carrera?->name ?? '—' }}</td>
                                    <td class="px-6 py-2.5 text-slate-500">{{ $m->tipo }}</td>
                                    <td class="px-6 py-2.5 text-slate-500">{{ $m->estado }}</td>
                                    <td class="px-6 py-2.5 text-slate-500 whitespace-nowrap">{{ $m->fecha_matricula ? \Carbon\Carbon::parse($m->fecha_matricula)->format('d/m/Y') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ══ DOCUMENTOS ════════════════════════════════════════════════════════ --}}
    @if ($tab === 'documentos')
        @livewire('expediente.panel-documentos', ['user' => $user, 'perfil' => 'estudiante'], key('panel-docs-' . $user->id))
    @endif

    {{-- ══ HISTORIAL ═════════════════════════════════════════════════════════ --}}
    @if ($tab === 'historial')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3">
                <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Historial de cambios</h3>
            </div>
            @forelse ($historial as $h)
                @php
                    $cambios = collect(($h->valores_despues ?? []))->except(['user_id', 'documento']);
                    $esDoc = $h->auditable_type === \App\Models\ExpedienteDocumento::class;
                @endphp
                <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 text-sm">
                    <p class="text-xs text-slate-400">
                        {{ $h->created_at?->format('d/m/Y H:i') }} · {{ $h->usuario?->name ?? 'Sistema' }}
                        @if ($esDoc) · <span class="font-semibold text-slate-500 dark:text-slate-300">{{ $h->valores_despues['documento'] ?? 'Documento' }}</span> @endif
                        @if ($h->evento === 'deleted') · <span class="text-red-500 font-semibold">eliminado definitivamente</span> @endif
                    </p>
                    <ul class="mt-1 space-y-0.5">
                        @foreach ($cambios as $campo => $nuevo)
                            @php $anterior = $h->valores_antes[$campo] ?? null; @endphp
                            <li class="text-slate-700 dark:text-slate-200">
                                <span class="text-slate-500 dark:text-slate-400">{{ F::ETIQUETAS[$campo] ?? ucfirst(str_replace('_', ' ', $campo)) }}:</span>
                                @if ($anterior !== null && $anterior !== '') <span class="line-through text-slate-400">{{ \Illuminate\Support\Str::limit((string) $anterior, 60) }}</span> → @endif
                                {{ \Illuminate\Support\Str::limit((string) ($nuevo ?? '—'), 80) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="px-6 py-8 text-sm text-center text-slate-400">Sin cambios registrados.</p>
            @endforelse
        </div>
    @endif

    {{-- ══ MODAL: OBSERVAR FICHA ═════════════════════════════════════════════ --}}
    @if ($showObservar)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">Marcar ficha con observaciones</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Indica qué debe corregir o completar el estudiante.</p>
                </div>
                <div class="px-6 py-5">
                    <textarea wire:model="observacionFicha" rows="3" maxlength="1000" placeholder="Ej.: El ingreso del hogar no coincide con el documento entregado."
                              class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-800 dark:text-slate-100"></textarea>
                    @error('observacionFicha') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                    <button wire:click="$set('showObservar', false)" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancelar</button>
                    <button wire:click="observarFicha" wire:loading.attr="disabled" class="px-5 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700">Guardar observación</button>
                </div>
            </div>
        </div>
    @endif
</div>
