<div>
    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800 dark:text-white">Aspirantes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Gestiona el proceso de admisión de aspirantes</p>
        </div>
        @can('gestionar_aspirantes')
        <div class="flex items-center gap-2">
            <button wire:click="$set('showIndicadores', true)"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                </svg>
                Indicadores
            </button>
            <a href="{{ route('administracion.administrativa.aspirantes.papelera') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                      text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
                Papelera
            </a>
            <a href="{{ route('administracion.administrativa.aspirantes.registrar') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 text-white text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Registrar aspirante
            </a>
        </div>
        @endcan
    </div>

    {{-- ══ ALERTA VERIFICACIÓN PENDIENTE ══════════════════════════════════ --}}
    @if($verificacionCount > 0)
    <button wire:click="$set('filtroEstado','verificacion')"
            class="w-full flex items-center gap-3 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-left hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors mb-5">
        <span class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-400/20 dark:bg-amber-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                {{ $verificacionCount }} aspirante{{ $verificacionCount !== 1 ? 's' : '' }} pendiente{{ $verificacionCount !== 1 ? 's' : '' }} de verificación
            </p>
            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Haz clic para filtrar y revisar su documentación</p>
        </div>
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
    </button>
    @endif

    {{-- ══ FILTROS ═════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 mb-5
                flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input wire:model.live.debounce.350ms="buscar" type="search"
                   placeholder="Buscar por nombre, cédula o correo…"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                          text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
        </div>
        <select wire:model.live="filtroCohorte"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                       text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
            <option value="">Todas las cohortes</option>
            @foreach($cohortes as $cohorte)
                <option value="{{ $cohorte->id }}">{{ $cohorte->nombre }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroEstado"
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900
                       text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40">
            <option value="">Todos los estados</option>
            @foreach(\App\Models\Aspirante::ESTADOS as $key => $label)
                @if($key !== 'matriculado')
                <option value="{{ $key }}">{{ $label }}</option>
                @endif
            @endforeach
        </select>
    </div>

    {{-- ══ TABLA ═══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($aspirantes->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-600">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="currentColor" viewBox="0 0 640 512">
                    <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128z"/>
                </svg>
                <p class="text-sm font-medium">No se encontraron aspirantes</p>
                <p class="text-xs mt-1">Prueba ajustando los filtros de búsqueda</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Aspirante</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Cohorte</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Documentos</th>
                            <th class="text-center px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Estado</th>
                            <th class="text-left px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Registro</th>
                            <th class="text-right px-4 py-3 font-medium text-slate-600 dark:text-slate-400">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($aspirantes as $asp)
                        @php
                            $estadoClases = [
                                'pendiente'    => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400',
                                'proceso'      => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                                'verificacion' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
                                'aprobado'     => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
                                'rechazado'    => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
                                'matriculado'  => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
                            ][$asp->estado] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ $asp->user->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $asp->user->cedula }} · {{ $asp->user->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                <p>{{ $asp->carrera?->name ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $asp->cohorte?->nombre ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @php
                                        $docs = [
                                            'CI'   => $asp->cedula_path      ? $asp->cedula_estado      : null,
                                            'Bach' => $asp->bachiller_path   ? $asp->bachiller_estado   : null,
                                            'Hab'  => $asp->habilitante_path ? $asp->habilitante_estado : null,
                                            'Pago' => $asp->pago_comprobante_path ? $asp->pago_estado   : null,
                                        ];
                                        $docColors = ['aprobado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
                                                      'rechazado' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                                      'pendiente' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                                                      'verificado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400'];
                                    @endphp
                                    @foreach($docs as $label => $docEstado)
                                        @if($docEstado !== null)
                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $docColors[$docEstado] ?? 'bg-slate-100 text-slate-500' }}">
                                                {{ $label }}
                                            </span>
                                        @else
                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-400">
                                                {{ $label }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $estadoClases }}">
                                    {{ \App\Models\Aspirante::ESTADOS[$asp->estado] ?? $asp->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                {{ $asp->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="verDetalle({{ $asp->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                                   bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300
                                                   hover:bg-indigo-100 dark:hover:bg-indigo-800/40 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Ver
                                    </button>
                                    @can('gestionar_aspirantes')
                                    <button
                                        x-on:click="Swal.fire({
                                            title: '¿Eliminar aspirante?',
                                            text: 'Se moverá a la papelera. Podrás restaurarlo o eliminarlo definitivamente desde allí.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Sí, eliminar',
                                            cancelButtonText: 'Cancelar',
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#64748b',
                                        }).then(r => { if (r.isConfirmed) $wire.eliminar({{ $asp->id }}) })"
                                        class="inline-flex items-center p-1.5 rounded-lg text-xs font-medium
                                               text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600
                                               transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $aspirantes->links() }}
            </div>
        @endif
    </div>

    {{-- ══ MODAL DETALLE ════════════════════════════════════════════════════ --}}
    @if($showDetalle && $aspiranteDetalle)
    @php
        $asp = $aspiranteDetalle;
        $u   = $asp->user;
        $estadoLabels = \App\Models\Aspirante::ESTADOS;
        $estadoColors = [
            'pendiente'    => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
            'proceso'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'verificacion' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'aprobado'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            'rechazado'    => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'matriculado'  => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
        ];
        $transiciones = [
            'pendiente'    => ['proceso' => 'Poner en proceso', 'rechazado' => 'Rechazar'],
            'proceso'      => [/* 'verificacion' => 'Marcar verificando', */ 'rechazado' => 'Rechazar'],
            'verificacion' => ['aprobado' => 'Aprobar', 'rechazado' => 'Rechazar'],
            'aprobado'     => [],
        ];
        $transicionesPosibles = $transiciones[$asp->estado] ?? [];

        $docsInfo = [
            ['label' => 'Cédula de identidad y papeleta de votación', 'path' => $asp->cedula_path,          'estado' => $asp->cedula_estado,         'obs' => $asp->cedula_observacion,         'campo' => 'cedula',      'req' => true,  'esPago' => false],
            ['label' => 'Título de bachiller',                        'path' => $asp->bachiller_path,        'estado' => $asp->bachiller_estado,      'obs' => $asp->bachiller_observacion,      'campo' => 'bachiller',   'req' => true,  'esPago' => false],
            ['label' => 'Documento habilitante',                      'path' => $asp->habilitante_path,      'estado' => $asp->habilitante_estado,    'obs' => $asp->habilitante_observacion,    'campo' => 'habilitante', 'req' => false, 'esPago' => false],
            ['label' => 'Comprobante de pago de matrícula',          'path' => $asp->pago_comprobante_path, 'estado' => $asp->pago_estado,           'obs' => $asp->pago_observacion,           'campo' => 'pago',        'req' => true,  'esPago' => true,  'monto' => $asp->pago_monto],
        ];
        if ($asp->esValidacionConocimientos()) {
            $docsInfo[] = ['label' => 'Hoja de vida',                    'path' => $asp->hoja_vida_path,      'estado' => $asp->hoja_vida_estado,      'obs' => $asp->hoja_vida_observacion,      'campo' => 'hoja_vida',      'req' => true,  'esPago' => false];
            $docsInfo[] = ['label' => 'Certificados laborales (PDF)',     'path' => $asp->cert_laborales_path, 'estado' => $asp->cert_laborales_estado, 'obs' => $asp->cert_laborales_observacion, 'campo' => 'cert_laborales', 'req' => true,  'esPago' => false];
            $docsInfo[] = ['label' => 'Certificados de cursos (PDF)',     'path' => $asp->cert_cursos_path,    'estado' => $asp->cert_cursos_estado,    'obs' => $asp->cert_cursos_observacion,    'campo' => 'cert_cursos',    'req' => true,  'esPago' => false];
            $docsInfo[] = ['label' => 'Mecanizado IESS',                  'path' => $asp->mecanizado_iess_path,'estado' => 'pendiente',                 'obs' => null,                             'campo' => null,             'req' => false, 'esPago' => false];
        }

        $docEstadoBadge = [
            'pendiente'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'aprobado'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'rechazado'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'verificado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        ];

        $docsConProblema = collect($docsInfo)->filter(fn($d) => $d['estado'] === 'rechazado')->count();

        // Campos obligatorios de la ficha — espejo de portal-aspirante.blade.php
        $camposReqFicha = [
            'phone','sexo','genero','estado_civil','tipo_sangre','etnia',
            'fecha_nacimiento','nacionalidad',
            'address','pais_residencia','provincia_residencia','canton_residencia',
            'provincia_nacimiento','canton_nacimiento',
            'tipo_colegio','nombre_colegio','ocupacion','empleo_ingresos',
            'bono_dh','ingresos_hogar','miembros_hogar',
            'padre','formacion_padre','madre','formacion_madre',
            'contacto_emergencia','telefono_emergencia','parentesco_emergencia',
        ];
        if ($u->etnia === 'Indígena')
            $camposReqFicha[] = 'pueblo_nacionalidad';
        if ($u->tiene_discapacidad)
            array_push($camposReqFicha, 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis');
        if (! $u->is_facturador)
            array_push($camposReqFicha, 'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono');

        $fMiss = fn(string $campo) => in_array($campo, $camposReqFicha) && empty($u->$campo);
        $fichaFaltantes = collect($camposReqFicha)->filter(fn($c) => empty($u->$c))->count();

        // ── Condición para habilitar el botón Aprobar global ─────────────────
        // Docs requeridos deben estar aprobados/verificados
        $docsReqAprobados =
            $asp->cedula_estado === 'aprobado' &&
            ($asp->bachiller_estado === 'aprobado' || $asp->habilitante_estado === 'aprobado') &&
            $asp->pago_estado === 'verificado';
        if ($asp->esValidacionConocimientos()) {
            $docsReqAprobados = $docsReqAprobados
                && $asp->hoja_vida_estado      === 'aprobado'
                && $asp->cert_laborales_estado === 'aprobado'
                && $asp->cert_cursos_estado    === 'aprobado';
        }
        // Ningún documento (subido) puede estar rechazado
        $hayDocRechazado = collect($docsInfo)
            ->filter(fn($d) => $d['path'] && $d['estado'] === 'rechazado')
            ->isNotEmpty();

        $puedeAprobar = $fichaFaltantes === 0 && $docsReqAprobados && !$hayDocRechazado;

        // Razones para mostrar al admin cuando está deshabilitado
        $razonesNoPuede = [];
        if ($fichaFaltantes > 0)
            $razonesNoPuede[] = "Ficha incompleta ({$fichaFaltantes} campo" . ($fichaFaltantes > 1 ? 's' : '') . ')';
        if ($hayDocRechazado)
            $razonesNoPuede[] = 'Hay documentos rechazados sin corregir';
        elseif (!$docsReqAprobados)
            $razonesNoPuede[] = 'Documentos requeridos aún no están todos aprobados';
    @endphp

    <div class="fixed inset-0 z-50 flex items-start justify-center pt-6 pb-4 px-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
         x-data="{
            tab: '{{ $asp->estado === 'verificacion' ? 'documentos' : 'ficha' }}',
            rechazando: null,
            obs: ''
         }">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl mb-4"
             @click.outside.stop>

            {{-- ── Header ── --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="min-w-0">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">Aspirante</p>
                        <h2 class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $asp->user->name }}</h2>
                    </div>
                    <span class="flex-shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoColors[$asp->estado] ?? '' }}">
                        {{ $estadoLabels[$asp->estado] ?? $asp->estado }}
                    </span>
                    @if($asp->esValidacionConocimientos())
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        Validación de conocimientos
                    </span>
                    @endif
                </div>
                <button wire:click="cerrarDetalle"
                        class="flex-shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors ml-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ── Tabs ── --}}
            <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                <button @click="tab='ficha'"
                        :class="tab==='ficha' ? 'border-b-2 border-blue-600 text-blue-700 dark:text-blue-400 bg-white dark:bg-slate-800' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                        class="flex-1 py-3 text-xs font-semibold text-center transition-colors relative">
                    Ficha personal
                    @if($fichaFaltantes > 0)
                    <span class="absolute top-2 right-6 w-4 h-4 rounded-full bg-amber-500 text-white text-[9px] font-bold flex items-center justify-center">
                        {{ $fichaFaltantes }}
                    </span>
                    @endif
                </button>
                <button @click="tab='documentos'"
                        :class="tab==='documentos' ? 'border-b-2 border-blue-600 text-blue-700 dark:text-blue-400 bg-white dark:bg-slate-800' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                        class="flex-1 py-3 text-xs font-semibold text-center transition-colors relative">
                    Documentos
                    @if($docsConProblema > 0)
                    <span class="absolute top-2 right-6 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">
                        {{ $docsConProblema }}
                    </span>
                    @endif
                </button>
            </div>

            {{-- ══ TAB FICHA PERSONAL ══════════════════════════════════════════ --}}
            <div x-show="tab==='ficha'" x-cloak class="overflow-y-auto max-h-[62vh]">
                <div class="px-6 py-5 space-y-5 text-sm">

                    {{-- Resumen ficha --}}
                    @if($fichaFaltantes > 0)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-400">
                            Faltan <strong>{{ $fichaFaltantes }}</strong> campo{{ $fichaFaltantes !== 1 ? 's' : '' }} obligatorio{{ $fichaFaltantes !== 1 ? 's' : '' }} por completar — el aspirante debe llenarlos antes de enviar.
                        </p>
                    </div>
                    @else
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Todos los campos obligatorios están completos.</p>
                    </div>
                    @endif

                    {{-- ─ Identificación ─ --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Identificación</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Nombres completos',    $u->name,                                                        false],
                                ['Cédula / Documento',   $u->cedula,                                                      false],
                                ['Correo electrónico',   $u->email,                                                       false],
                                ['Celular',              $u->phone,                                                       $fMiss('phone')],
                                ['Sexo',                 $u->sexo,                                                        $fMiss('sexo')],
                                ['Género',               $u->genero,                                                      $fMiss('genero')],
                                ['Estado civil',         $u->estado_civil,                                                $fMiss('estado_civil')],
                                ['Tipo de sangre',       $u->tipo_sangre,                                                 $fMiss('tipo_sangre')],
                                ['Fecha de nacimiento',  $u->fecha_nacimiento ? $u->fecha_nacimiento->format('d/m/Y') : null, $fMiss('fecha_nacimiento')],
                                ['País de nacionalidad', $u->nacionalidad,                                                $fMiss('nacionalidad')],
                                ['Etnia',                $u->etnia,                                                       $fMiss('etnia')],
                                ['Pueblo / Nac.',        $u->pueblo_nacionalidad,                                         $fMiss('pueblo_nacionalidad')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300 break-words">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Discapacidad ─ --}}
                    @if($u->tiene_discapacidad)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 border border-blue-100 dark:border-blue-800/40">
                        <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">Discapacidad</p>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['Tipo',        $u->tipo_discapacidad,     $fMiss('tipo_discapacidad')],
                                ['Porcentaje',  $u->porcentaje_discapacidad ? $u->porcentaje_discapacidad.'%' : null, $fMiss('porcentaje_discapacidad')],
                                ['N.° CONADIS', $u->nro_conadis,           $fMiss('nro_conadis')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-blue-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-blue-800 dark:text-blue-300">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ─ Dirección y procedencia ─ --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Dirección y procedencia</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Dirección de domicilio',   $u->address,              $fMiss('address')],
                                ['País de residencia',       $u->pais_residencia,      $fMiss('pais_residencia')],
                                ['Prov. de residencia',      $u->provincia_residencia, $fMiss('provincia_residencia')],
                                ['Cantón de residencia',     $u->canton_residencia,    $fMiss('canton_residencia')],
                                ['Prov. de nacimiento',      $u->provincia_nacimiento, $fMiss('provincia_nacimiento')],
                                ['Cantón de nacimiento',     $u->canton_nacimiento,    $fMiss('canton_nacimiento')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300 break-words">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Colegio y datos socioeconómicos ─ --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Colegio y situación socioeconómica</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Tipo de colegio',           $u->tipo_colegio,    $fMiss('tipo_colegio')],
                                ['Nombre del colegio',        $u->nombre_colegio,  $fMiss('nombre_colegio')],
                                ['Ocupación',                 $u->ocupacion,       $fMiss('ocupacion')],
                                ['Empleo de ingresos',        $u->empleo_ingresos, $fMiss('empleo_ingresos')],
                                ['Bono de Des. Humano',       $u->bono_dh,         $fMiss('bono_dh')],
                                ['Ingresos del hogar',        $u->ingresos_hogar,  $fMiss('ingresos_hogar')],
                                ['Miembros del hogar',        $u->miembros_hogar,  $fMiss('miembros_hogar')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300 break-words">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Familia ─ --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Datos familiares</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Padre',                $u->padre,           $fMiss('padre')],
                                ['Formación del padre',  $u->formacion_padre, $fMiss('formacion_padre')],
                                ['Madre',                $u->madre,           $fMiss('madre')],
                                ['Formación de la madre',$u->formacion_madre, $fMiss('formacion_madre')],
                                ['Tutor / representante',$u->tutor,           false],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300 break-words">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Emergencia ─ --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Contacto de emergencia</p>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['Nombre',     $u->contacto_emergencia, $fMiss('contacto_emergencia')],
                                ['Parentesco', $u->parentesco_emergencia,$fMiss('parentesco_emergencia')],
                                ['Teléfono',   $u->telefono_emergencia, $fMiss('telefono_emergencia')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Facturación ─ --}}
                    @if(! $u->is_facturador)
                    <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-3 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Datos de facturación (tercero)</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Nombre',       $u->fact_nombre,    $fMiss('fact_nombre')],
                                ['Cédula / RUC', $u->fact_documento, $fMiss('fact_documento')],
                                ['Correo',       $u->fact_correo,    $fMiss('fact_correo')],
                                ['Dirección',    $u->fact_direccion, $fMiss('fact_direccion')],
                                ['Teléfono',     $u->fact_telefono,  $fMiss('fact_telefono')],
                            ] as [$label, $valor, $req])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                @if($req)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 dark:text-red-400">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Falta dato
                                </span>
                                @else
                                <p class="font-medium text-slate-700 dark:text-slate-300 break-words">{{ $valor ?? '—' }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ─ Registro ─ --}}
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inscripción</p>
                            @if($asp->estado !== 'matriculado')
                            @can('gestionar_aspirantes')
                            <button wire:click="abrirEditar"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                           text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700
                                           hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                Editar
                            </button>
                            @endcan
                            @endif
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                ['Cohorte', $asp->cohorte?->nombre ?? '—'],
                                ['Carrera', $asp->carrera?->name ?? '—'],
                                ['Registrado por', $asp->registradoPor->name ?? 'Sistema'],
                                ['Fecha de registro', $asp->created_at->format('d/m/Y H:i')],
                            ] as [$label, $valor])
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ $valor }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─ Panel edición ─ --}}
                    @if($showEditar)
                    <div class="border-t border-indigo-200 dark:border-indigo-700 pt-4 space-y-4">
                        <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Editar datos del aspirante</p>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Nombres</label>
                                <input wire:model="editFirstName" type="text"
                                       class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                              {{ $errors->has('editFirstName') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                @error('editFirstName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Apellidos</label>
                                <input wire:model="editLastName" type="text"
                                       class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                              {{ $errors->has('editLastName') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                @error('editLastName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Cédula</label>
                                <input wire:model="editCedula" type="text"
                                       class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                              {{ $errors->has('editCedula') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                @error('editCedula') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Correo</label>
                                <input wire:model="editEmail" type="email"
                                       class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                              {{ $errors->has('editEmail') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                @error('editEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Carrera</label>
                                <select wire:model="editCarreraId"
                                        class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                               {{ $errors->has('editCarreraId') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                    <option value="">Selecciona carrera</option>
                                    @foreach($carreras as $car)
                                        <option value="{{ $car->id }}">{{ $car->name }}</option>
                                    @endforeach
                                </select>
                                @error('editCarreraId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Cohorte</label>
                                <select wire:model="editCohorteId"
                                        class="w-full rounded-xl border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors
                                               {{ $errors->has('editCohorteId') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                    <option value="">Selecciona cohorte</option>
                                    @foreach($cohortes as $coh)
                                        <option value="{{ $coh->id }}">{{ $coh->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('editCohorteId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-1">
                            <button wire:click="cerrarEditar"
                                    class="px-3 py-1.5 rounded-xl text-xs font-medium text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                Cancelar
                            </button>
                            <button wire:click="guardarEdicion" wire:loading.attr="disabled"
                                    class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-xs font-medium transition-colors">
                                <span wire:loading.remove wire:target="guardarEdicion">Guardar cambios</span>
                                <span wire:loading wire:target="guardarEdicion">Guardando…</span>
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ══ TAB DOCUMENTOS ══════════════════════════════════════════════ --}}
            <div x-show="tab==='documentos'" x-cloak class="overflow-y-auto max-h-[62vh]">
                <div class="px-6 py-5 space-y-3">

                    @if($asp->estado !== 'verificacion')
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-600">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            La revisión y aprobación de documentos solo se habilita cuando el aspirante envíe su solicitud (estado <strong>Verificación</strong>).
                        </p>
                    </div>
                    @endif

                    @if($docsConProblema > 0)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p class="text-xs font-semibold text-red-700 dark:text-red-400">
                            {{ $docsConProblema }} documento{{ $docsConProblema !== 1 ? 's' : '' }} rechazado{{ $docsConProblema !== 1 ? 's' : '' }} — el estudiante recibirá o recibió correo con la observación
                        </p>
                    </div>
                    @endif

                    @foreach($docsInfo as $doc)
                    @php
                        $idCampo = $doc['campo'] ?? 'sin_campo_' . $loop->index;
                        $alpineKey = "'{$idCampo}'";
                    @endphp
                    <div class="rounded-xl border overflow-hidden
                                {{ $doc['path'] && $doc['estado'] === 'rechazado' ? 'border-red-200 dark:border-red-700' : ($doc['path'] && in_array($doc['estado'], ['aprobado','verificado']) ? 'border-emerald-200 dark:border-emerald-700' : 'border-slate-200 dark:border-slate-700') }}">

                        {{-- Doc header --}}
                        <div class="flex items-center justify-between px-4 py-2.5
                                    {{ $doc['path'] && $doc['estado'] === 'rechazado' ? 'bg-red-50 dark:bg-red-900/20' : ($doc['path'] && in_array($doc['estado'], ['aprobado','verificado']) ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-slate-50 dark:bg-slate-900/30') }}">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $doc['label'] }}</span>
                                @if($doc['req'])<span class="text-red-500 text-xs leading-none">*</span>@endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($doc['esPago'] && isset($doc['monto']) && $doc['monto'])
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">${{ number_format($doc['monto'], 2) }}</span>
                                @endif
                                @if($doc['path'])
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $docEstadoBadge[$doc['estado']] ?? 'bg-slate-100 text-slate-500' }}">
                                    {{ ucfirst($doc['estado']) }}
                                </span>
                                @else
                                <span class="text-[10px] text-slate-400 italic">No subido</span>
                                @endif
                            </div>
                        </div>

                        {{-- Doc body --}}
                        @if($doc['path'])
                        <div class="px-4 py-3 space-y-2.5">

                            {{-- Observación previa rechazada --}}
                            @if($doc['obs'] && $doc['estado'] === 'rechazado')
                            <div class="flex items-start gap-2 p-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                                <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <p class="text-xs text-red-700 dark:text-red-400 leading-relaxed">{{ $doc['obs'] }}</p>
                            </div>
                            @endif

                            {{-- Ver documento + acciones --}}
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($doc['path']) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Ver documento
                                </a>

                                @can('gestionar_aspirantes')
                                @if($doc['campo'] && $asp->estado === 'verificacion')
                                <div class="flex items-center gap-2">
                                    {{-- Botones cuando no está en modo rechazo --}}
                                    <div x-show="rechazando !== {{ $alpineKey }}" class="flex gap-1.5">
                                        @php $yaAprobado = in_array($doc['estado'], ['aprobado', 'verificado']); @endphp
                                        <button
                                            @if(!$yaAprobado)
                                            @click="$wire.actualizarDocumento('{{ $doc['campo'] }}', '{{ $doc['esPago'] ? 'verificado' : 'aprobado' }}')"
                                            @else
                                            disabled
                                            @endif
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors
                                                   {{ $yaAprobado
                                                      ? 'bg-emerald-50 dark:bg-emerald-900/10 text-emerald-400 cursor-not-allowed opacity-60'
                                                      : 'bg-emerald-100 hover:bg-emerald-200 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-800/50 dark:text-emerald-300' }}">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            {{ $doc['esPago'] ? 'Verificar pago' : 'Aprobar' }}
                                        </button>
                                        <button @click="rechazando = {{ $alpineKey }}; obs = ''"
                                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors
                                                       bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-800/50 dark:text-red-300">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Rechazar
                                        </button>
                                    </div>
                                </div>
                                @endif
                                @endcan
                            </div>

                            {{-- Formulario inline de rechazo --}}
                            @can('gestionar_aspirantes')
                            @if($doc['campo'] && $asp->estado === 'verificacion')
                            <div x-show="rechazando === {{ $alpineKey }}" x-cloak class="space-y-2 border-t border-red-100 dark:border-red-800/40 pt-2.5">
                                <p class="text-xs font-semibold text-red-700 dark:text-red-400">
                                    Motivo del rechazo — se enviará al estudiante por correo <span class="text-red-500">*</span>
                                </p>
                                <textarea x-model="obs"
                                          rows="3"
                                          placeholder="Describe claramente qué está mal y cómo debe corregirlo…"
                                          class="w-full text-sm rounded-xl border border-red-200 dark:border-red-700 bg-white dark:bg-slate-700
                                                 text-slate-800 dark:text-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400/40 resize-none"></textarea>
                                <div class="flex gap-2">
                                    <button @click="$wire.actualizarDocumento('{{ $doc['campo'] }}', 'rechazado', obs); rechazando = null; obs = ''"
                                            :disabled="!obs.trim()"
                                            class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg font-semibold
                                                   bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white transition-colors">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Confirmar rechazo y notificar
                                    </button>
                                    <button @click="rechazando = null; obs = ''"
                                            class="text-xs px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                            @endif
                            @endcan

                        </div>
                        @endif
                    </div>
                    @endforeach

                    {{-- Observación general --}}
                    @if($asp->observacion_general)
                    <div class="bg-slate-50 dark:bg-slate-700/30 rounded-xl p-3 border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-1">Observación general</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $asp->observacion_general }}</p>
                    </div>
                    @endif

                    {{-- Motivo rechazo global --}}
                    @if($asp->estado === 'rechazado' && $asp->motivo_rechazo)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-700">
                        <p class="text-[10px] font-semibold text-red-500 uppercase tracking-wide mb-1">Motivo de rechazo de solicitud</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $asp->motivo_rechazo }}</p>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ── Panel cambiar estado (dentro del modal, arriba del footer) ── --}}
            @if($showCambiarEstado)
            <div class="mx-6 mb-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl p-4 border border-slate-200 dark:border-slate-600 space-y-3">
                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Cambiar estado a: <strong>{{ $estadoLabels[$nuevoEstado] ?? $nuevoEstado }}</strong>
                </p>
                @if($showRechazar)
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Motivo del rechazo <span class="text-red-500">*</span></label>
                    <textarea wire:model="motivoRechazo" rows="3" placeholder="Explica por qué se rechaza la solicitud completa…"
                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                     text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500/40 resize-none"></textarea>
                    @error('motivoRechazo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                @else
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Observación (opcional)</label>
                    <textarea wire:model="observacion" rows="2" placeholder="Nota adicional…"
                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                     text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lime-500/40 resize-none"></textarea>
                </div>
                @endif
                <div class="flex gap-2">
                    <button wire:click="confirmarCambioEstado"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors shadow-sm
                                   {{ $showRechazar ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-lime-600 hover:bg-lime-700 text-white' }}">
                        Confirmar
                    </button>
                    <button wire:click="$set('showCambiarEstado', false)"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
            @endif

            {{-- ── Footer: acciones de estado global ── --}}
            @if($transicionesPosibles && !$showCambiarEstado)
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30 rounded-b-2xl space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-slate-400 mr-1">Estado global:</span>
                    @can('gestionar_aspirantes')
                    @foreach($transicionesPosibles as $estado => $etiqueta)
                    @php $bloqueado = $estado === 'aprobado' && !$puedeAprobar; @endphp
                    <button
                        @if(!$bloqueado) wire:click="abrirCambiarEstado('{{ $estado }}')" @else disabled @endif
                        title="{{ $bloqueado ? implode(' · ', $razonesNoPuede) : '' }}"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors shadow-sm
                               {{ $bloqueado
                                  ? 'bg-slate-100 dark:bg-slate-700 text-slate-400 cursor-not-allowed opacity-60'
                                  : ($estado === 'rechazado'
                                     ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200'
                                     : ($estado === 'aprobado'
                                        ? 'bg-lime-600 hover:bg-lime-700 text-white'
                                        : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200')) }}">
                        {{ $etiqueta }}
                    </button>
                    @endforeach
                    @endcan
                    <button wire:click="cerrarDetalle"
                            class="ml-auto px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Cerrar
                    </button>
                </div>
                {{-- Razones por las que no se puede aprobar --}}
                @if(!empty($razonesNoPuede) && in_array($asp->estado, ['verificacion']))
                <div class="flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <p class="text-[11px] text-amber-600 dark:text-amber-400 leading-snug">
                        Para aprobar: {{ implode(' · ', $razonesNoPuede) }}
                    </p>
                </div>
                @endif
            </div>
            @else
            <div class="flex items-center justify-end px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30 rounded-b-2xl">
                @if(!$showCambiarEstado)
                <button wire:click="cerrarDetalle"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Cerrar
                </button>
                @endif
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ══ MODAL INDICADORES ══════════════════════════════════════════════ --}}
    @if($showIndicadores)
    @php $ind = $this->indicadores; @endphp
    <div class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
         x-data x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-4xl mb-10"
             @click.outside="$wire.set('showIndicadores', false)">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                    </svg>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white">Indicadores de admisión</h2>
                    <span class="text-xs text-slate-400 dark:text-slate-500">— todos los periodos</span>
                </div>
                <button wire:click="$set('showIndicadores', false)"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6">

                {{-- ── KPI CARDS ────────────────────────────────────────────── --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['Total inscritos',       $ind['total'],    'text-slate-700 dark:text-slate-200',   'bg-slate-100 dark:bg-slate-700'],
                        ['En proceso activo',     $ind['activos'],  'text-blue-700 dark:text-blue-300',     'bg-blue-50 dark:bg-blue-900/30'],
                        ['Aprobados',             $ind['aprobados'],'text-emerald-700 dark:text-emerald-300','bg-emerald-50 dark:bg-emerald-900/30'],
                        ['Inscritos (7 días)',    $ind['ultimos_7_dias'], 'text-indigo-700 dark:text-indigo-300','bg-indigo-50 dark:bg-indigo-900/30'],
                    ] as [$label, $valor, $textClass, $bgClass])
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 {{ $bgClass }}">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $label }}</p>
                        <p class="text-3xl font-black {{ $textClass }}">{{ $valor }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- ── FILA 2: Tasa + Tipo + Docs ──────────────────────────── --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Tasa de aprobación --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Resolución</p>
                        <div class="space-y-2">
                            @if($ind['tasa_aprobacion'] !== null)
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-600 dark:text-slate-400">Tasa aprobación</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $ind['tasa_aprobacion'] }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $ind['tasa_aprobacion'] }}%"></div>
                            </div>
                            @endif
                            <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 pt-1">
                                <span>✓ {{ $ind['aprobados'] }} aprobados</span>
                                <span>✗ {{ $ind['rechazados'] }} rechazados</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tipo de proceso --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Tipo de proceso</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-slate-400">Regular</span>
                                <span class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ $ind['regular'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-slate-400">Validación conocim.</span>
                                <span class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ $ind['validacion'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Documentación --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Documentación aprobada</p>
                        <div class="space-y-2.5">
                            @foreach([
                                ['Cédula',        $ind['docs_cedula_pct']],
                                ['Bach./Hab.',     $ind['docs_bachiller_pct']],
                                ['Pago',          $ind['docs_pago_pct']],
                            ] as [$doc, $pct])
                            <div>
                                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-0.5">
                                    <span>{{ $doc }}</span>
                                    <span class="font-medium">{{ $pct }}%</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                    <div class="h-full rounded-full {{ $pct >= 80 ? 'bg-emerald-500' : ($pct >= 40 ? 'bg-amber-400' : 'bg-red-400') }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ── POR CARRERA ──────────────────────────────────────────── --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Distribución por carrera</p>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                                        <th class="text-left px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400">Carrera</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-slate-500 dark:text-slate-400">Total</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-slate-400 dark:text-slate-500">Pend.</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-blue-500">Proceso</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-amber-500">Verif.</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-emerald-500">Aprobado</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-red-400">Rechaz.</th>
                                        <th class="px-4 py-2.5 w-28"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                    @forelse($ind['por_carrera'] as $fila)
                                    @php
                                        $pctAprobado = $fila['total'] > 0 ? round($fila['aprobado'] / $fila['total'] * 100) : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30">
                                        <td class="px-4 py-2.5 font-medium text-slate-700 dark:text-slate-300">{{ $fila['nombre'] }}</td>
                                        <td class="px-3 py-2.5 text-center font-bold text-slate-800 dark:text-slate-200">{{ $fila['total'] }}</td>
                                        <td class="px-3 py-2.5 text-center text-slate-400">{{ $fila['pendiente'] ?: '—' }}</td>
                                        <td class="px-3 py-2.5 text-center text-blue-600 dark:text-blue-400">{{ $fila['proceso'] ?: '—' }}</td>
                                        <td class="px-3 py-2.5 text-center text-amber-600 dark:text-amber-400">{{ $fila['verificacion'] ?: '—' }}</td>
                                        <td class="px-3 py-2.5 text-center text-emerald-600 dark:text-emerald-400">{{ $fila['aprobado'] ?: '—' }}</td>
                                        <td class="px-3 py-2.5 text-center text-red-500">{{ $fila['rechazado'] ?: '—' }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $pctAprobado }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="8" class="px-4 py-6 text-center text-sm text-slate-400">Sin datos</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /body --}}

            <div class="flex justify-end px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                <button wire:click="$set('showIndicadores', false)"
                        class="px-5 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
