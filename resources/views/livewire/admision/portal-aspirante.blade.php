<div>
@php
    $pasos = [
        ['key' => 'pendiente',    'label' => 'Solicitud recibida'],
        ['key' => 'proceso',      'label' => 'En revisión'],
        ['key' => 'verificacion', 'label' => 'Verificación docs.'],
        ['key' => 'aprobado',     'label' => 'Pre-matrícula aprobada'],
    ];
    $ordenEstados = array_column($pasos, 'key');
    $indiceActual = $aspirante ? array_search($aspirante->estado, $ordenEstados) : -1;
    $indiceActual = $indiceActual === false ? -1 : (int) $indiceActual;

    $docEstadoStyle = [
        'pendiente'  => 'bg-amber-50 border-amber-200 text-amber-700',
        'aprobado'   => 'bg-emerald-50 border-emerald-200 text-emerald-700',
        'rechazado'  => 'bg-red-50 border-red-200 text-red-700',
        'verificado' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
    ];

    $paises = ['Ecuador','Colombia','Perú','Venezuela','Bolivia','Argentina','Brasil','Chile','México','España',
               'Estados Unidos','Cuba','Haití','China','Alemania','Francia','Italia','Otros'];

    $formacionOpts = [
        'Centro de Alfabetización','Jardín de infantes','Primaria','Educación Básica',
        'Secundaria','Educación Media','Superior no Universitaria','Superior Universitaria','Posgrado'
    ];

    $user = auth()->user();

    // Clases reutilizables
    $inputBase  = 'w-full text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
    $inputUpper = $inputBase . ' uppercase';
    $selectBase = $inputBase;

    // ── Cálculo de progreso ────────────────────────────────────────────────
    // Siempre requeridos (marcados con * en el formulario)
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
    // Condicionales
    if ($user && $user->etnia === 'Indígena')
        $camposReqFicha[] = 'pueblo_nacionalidad';
    if ($user && $user->tiene_discapacidad)
        array_push($camposReqFicha, 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis');
    if ($user && ! $user->is_facturador)
        array_push($camposReqFicha, 'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono');

    $totalCampos  = count($camposReqFicha);
    $camposLlenos = 0;
    foreach ($camposReqFicha as $_campo) {
        if (! empty($user->$_campo)) $camposLlenos++;
    }
    $docsReq = 3; // cedula + (bachiller|habilitante) + pago
    $docsOk  = 0;
    if ($aspirante) {
        if ($aspirante->cedula_path)                                        $docsOk++;
        if ($aspirante->bachiller_path || $aspirante->habilitante_path)     $docsOk++;
        if ($aspirante->pago_comprobante_path)                              $docsOk++;
        if ($aspirante->esValidacionConocimientos()) {
            $docsReq += 3;
            if ($aspirante->hoja_vida_path)      $docsOk++;
            if ($aspirante->cert_laborales_path) $docsOk++;
            if ($aspirante->cert_cursos_path)    $docsOk++;
        }
    }
    $totalItems     = $totalCampos + $docsReq;
    $itemsCompletos = $camposLlenos + $docsOk;
    $progreso       = $totalItems > 0 ? (int) round(($itemsCompletos / $totalItems) * 100) : 0;
    $listo          = $progreso >= 100;
    $barColor       = $progreso >= 100 ? 'bg-teal-500' : ($progreso >= 75 ? 'bg-emerald-500' : ($progreso >= 40 ? 'bg-amber-400' : 'bg-red-400'));
@endphp

<x-slot name="header">Mi solicitud de ingreso</x-slot>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

    @if(! $aspirante)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-10 text-center">
        <p class="text-slate-500 font-medium">Tu solicitud aún no ha sido registrada.</p>
        <p class="text-sm text-slate-400 mt-1">Comunícate con secretaría para completar tu registro.</p>
    </div>
    @else

    {{-- ══ BANNER ESTADO ══════════════════════════════════════════════════ --}}
    @if($aspirante->estado === 'rechazado')
    <div class="flex items-start gap-3 p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div>
            <h3 class="font-semibold text-red-800 dark:text-red-300">Tu solicitud no fue aprobada</h3>
            @if($aspirante->motivo_rechazo)<p class="text-sm text-red-700 mt-1">{{ $aspirante->motivo_rechazo }}</p>@endif
            <p class="text-xs text-red-500 mt-1">Si tienes dudas, comunícate con secretaría.</p>
        </div>
    </div>
    @elseif($aspirante->estado === 'aprobado')
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 text-center font-medium text-emerald-800 dark:text-emerald-300">
        ¡Tu solicitud ha sido aprobada! Secretaría se pondrá en contacto contigo para coordinar tu matrícula.
    </div>
    @elseif($aspirante->estado === 'matriculado')
    <div class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 text-center font-medium text-purple-800 dark:text-purple-300">
        Tu matrícula ha sido completada exitosamente. ¡Bienvenido al instituto!
    </div>
    @elseif($aspirante->estado === 'verificacion')
    <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 text-center font-medium text-blue-800 dark:text-blue-300">
        Tu ficha y documentos están siendo revisados por el equipo de secretaría. Te notificaremos pronto.
    </div>
    @endif

    {{-- ══ TRACKER ════════════════════════════════════════════════════════ --}}
    @if(! in_array($aspirante->estado, ['rechazado', 'matriculado']))
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
        <div class="relative">
            <div class="absolute top-4 left-0 right-0 h-0.5 bg-slate-100 dark:bg-slate-700 mx-8"></div>
            <div class="relative flex justify-between">
                @foreach($pasos as $i => $paso)
                @php $completado = $indiceActual > $i; $activo = $indiceActual === $i; @endphp
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 transition-all
                                {{ $completado ? 'bg-blue-500' : ($activo ? 'bg-blue-600 ring-4 ring-blue-100 dark:ring-blue-900' : 'bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600') }}">
                        @if($completado)
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        @elseif($activo)
                            <div class="w-3 h-3 rounded-full bg-white"></div>
                        @else
                            <div class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                        @endif
                    </div>
                    <p class="text-xs font-semibold text-center {{ $activo ? 'text-blue-700 dark:text-blue-400' : ($completado ? 'text-blue-600' : 'text-slate-400') }}">
                        {{ $paso['label'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ══ PROGRESO ════════════════════════════════════════════════════════ --}}
    @if($aspirante->estado === 'proceso')
    <div class="bg-white dark:bg-slate-800 rounded-2xl border {{ $listo ? 'border-teal-200 dark:border-teal-700' : 'border-slate-200 dark:border-slate-700' }} p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                @if($listo)
                <div class="w-5 h-5 rounded-full bg-teal-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                @else
                <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                @endif
                <p class="text-sm font-semibold {{ $listo ? 'text-teal-700 dark:text-teal-300' : 'text-slate-700 dark:text-slate-300' }}">
                    {{ $listo ? '¡Todo listo para enviar!' : 'Progreso de tu solicitud' }}
                </p>
            </div>
            <span class="text-sm font-bold tabular-nums {{ $listo ? 'text-teal-600 dark:text-teal-400' : 'text-slate-500 dark:text-slate-400' }}">{{ $progreso }}%</span>
        </div>
        <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-2">
            <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" style="width:{{ $progreso }}%"></div>
        </div>
        <div class="flex items-center gap-3 text-xs text-slate-400 flex-wrap">
            <span>
                Ficha: <span class="{{ $camposLlenos === $totalCampos ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $camposLlenos }}/{{ $totalCampos }}</span> campos
            </span>
            <span class="text-slate-300 dark:text-slate-600">·</span>
            <span>
                Documentos: <span class="{{ $docsOk === $docsReq ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">{{ $docsOk }}/{{ $docsReq }}</span> requeridos
            </span>
        </div>
        @if($listo)
        <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-700">
            <svg class="w-4 h-4 text-teal-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs font-semibold text-teal-700 dark:text-teal-300">
                Usa el botón al pie de la columna derecha para enviar tu ficha a revisión.
            </p>
        </div>
        @else
        <p class="text-xs text-slate-400 mt-2">
            Completa los campos obligatorios de tu ficha y sube los documentos requeridos para habilitar el envío.
        </p>
        @endif
    </div>
    @endif

    {{-- ══ DOS COLUMNAS ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

        {{-- ─── IZQUIERDA (2/5) ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-6">

            {{-- ── CARD COHORTE ────────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                {{-- Header con gradiente --}}
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-5 py-4">
                    <p class="text-[10px] font-bold text-blue-300 uppercase tracking-widest mb-1">Mi cohorte</p>
                    <h2 class="text-base font-bold text-white leading-tight">{{ $aspirante->cohorte?->nombre ?? '—' }}</h2>
                    <p class="text-sm text-blue-200 mt-0.5 mb-3 leading-snug">{{ $aspirante->carrera?->name ?? '—' }}</p>

                    {{-- Pills: tipo + modalidad --}}
                    @if($aspirante->carrera)
                    <div class="flex flex-wrap gap-1.5">
                        @if($aspirante->carrera->tipo ?? false)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/30 text-indigo-100 ring-1 ring-indigo-300/30">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                            {{ $aspirante->carrera->tipo_label }}
                        </span>
                        @endif
                        @if($aspirante->carrera->modalidad ?? false)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-500/30 text-teal-100 ring-1 ring-teal-300/30">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            {{ $aspirante->carrera->modalidad }}
                        </span>
                        @endif
                        @if($aspirante->carrera->duracion_semestres ?? false)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-500/30 text-blue-100 ring-1 ring-blue-300/30">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $aspirante->carrera->duracion_semestres }} semestres
                        </span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Body: fechas + estado --}}
                @if($aspirante->cohorte)
                @php $cohorte = $aspirante->cohorte; @endphp
                <div class="p-4 grid grid-cols-2 gap-3">

                    {{-- Fecha matrícula --}}
                    @if($cohorte->fecha_inicio_matriculacion ?? false)
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 border border-blue-100 dark:border-blue-800/40">
                        <div class="flex items-center gap-1 mb-1">
                            <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <p class="text-[9px] font-bold text-blue-500 uppercase tracking-wide leading-none">Inicio matrícula</p>
                        </div>
                        <p class="text-xs font-bold text-blue-800 dark:text-blue-300 leading-snug">{{ $cohorte->fecha_inicio_matriculacion->format('d M Y') }}</p>
                    </div>
                    @endif

                    {{-- Fecha inicio clases --}}
                    @if($cohorte->fecha_inicio_clases ?? false)
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 border border-emerald-100 dark:border-emerald-800/40">
                        <div class="flex items-center gap-1 mb-1">
                            <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                            <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-wide leading-none">Inicio de clases</p>
                        </div>
                        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 leading-snug">{{ $cohorte->fecha_inicio_clases->format('d M Y') }}</p>
                    </div>
                    @endif

                    {{-- Estado --}}
                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 border border-slate-100 dark:border-slate-700">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">Estado</p>
                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full
                            {{ ($cohorte->estado ?? '') === 'abierto' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                            {{ ucfirst($cohorte->estado ?? '—') }}
                        </span>
                    </div>

                    {{-- Tipo de proceso --}}
                    @if($aspirante->esValidacionConocimientos())
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 border border-indigo-100 dark:border-indigo-800/40">
                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-wide mb-1">Mi proceso</p>
                        <p class="text-[11px] font-bold text-indigo-800 dark:text-indigo-300 leading-snug">Validación de conocimientos</p>
                    </div>
                    @else
                    <div class="bg-lime-50 dark:bg-lime-900/20 rounded-xl p-3 border border-lime-100 dark:border-lime-800/40">
                        <p class="text-[9px] font-bold text-lime-500 uppercase tracking-wide mb-1">Mi proceso</p>
                        <p class="text-[11px] font-bold text-lime-800 dark:text-lime-300 leading-snug">Regular</p>
                    </div>
                    @endif

                </div>
                @endif
            </div>

            {{-- ── CARD MATERIAS PRIMER SEMESTRE ───────────────────────────── --}}
            @if($primerSemestre && $primerSemestre->materias->isNotEmpty())
            @php $totalCreditos = $primerSemestre->materias->sum('credits'); @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $primerSemestre->name }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Materias que cursarás</p>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                        {{ $primerSemestre->materias->count() }} materias
                    </span>
                </div>
                <div class="divide-y divide-slate-50 dark:divide-slate-700/60">
                    @foreach($primerSemestre->materias as $materia)
                    <div class="px-4 py-2.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 dark:hover:bg-slate-700/20 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-snug">{{ $materia->name }}</p>
                            @if($materia->code)
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $materia->code }}</p>
                            @endif
                        </div>
                        @if($materia->credits)
                        <div class="flex-shrink-0 text-center min-w-[2.5rem]">
                            <p class="text-base font-bold text-indigo-600 dark:text-indigo-400 leading-none">{{ $materia->credits + 0 }}</p>
                            <p class="text-[9px] font-semibold text-indigo-400 dark:text-indigo-500 uppercase tracking-wide mt-0.5">créditos</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if($totalCreditos > 0)
                <div class="px-4 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 border-t border-indigo-100 dark:border-indigo-800/40 flex items-center justify-between">
                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Total del semestre</span>
                    <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/50 px-3 py-1 rounded-full">
                        {{ $totalCreditos + 0 }} créditos
                    </span>
                </div>
                @endif
            </div>
            @endif

        </div>{{-- /col izquierda --}}

        {{-- ─── DERECHA (3/5) ─────────────────────────────────────────────── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- ── FICHA TÉCNICA ──────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden"
                 x-data="{ seccion: 'personal' }">

                {{-- Tabs — select en móvil, pestañas en desktop --}}
                <div class="sm:hidden px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                    <select x-model="seccion"
                            class="w-full text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                        <option value="personal">Datos personales</option>
                        <option value="procedencia">Procedencia</option>
                        <option value="familia">Familia</option>
                        <option value="emergencia">Emergencia</option>
                        <option value="facturacion">Facturación</option>
                    </select>
                </div>
                <div class="hidden sm:flex border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                    @foreach([
                        ['key' => 'personal',    'label' => 'Datos personales'],
                        ['key' => 'procedencia', 'label' => 'Procedencia'],
                        ['key' => 'familia',     'label' => 'Familia'],
                        ['key' => 'emergencia',  'label' => 'Emergencia'],
                        ['key' => 'facturacion', 'label' => 'Facturación'],
                    ] as $tab)
                    <button x-on:click="seccion = '{{ $tab['key'] }}'"
                            :class="seccion === '{{ $tab['key'] }}' ? 'border-b-2 border-blue-600 text-blue-700 dark:text-blue-400 bg-white dark:bg-slate-800' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                            class="flex-1 py-3 text-xs font-semibold text-center whitespace-nowrap transition-colors">
                        {{ $tab['label'] }}
                    </button>
                    @endforeach
                </div>

                <div class="p-5">

                    {{-- ── TAB: Datos personales ──────────────────────────── --}}
                    <div x-show="seccion === 'personal'" x-cloak class="space-y-4">

                        {{-- Readonly --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nombres completos</label>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-700/50 rounded-xl px-3 py-2.5">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Cédula / Documento</label>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-700/50 rounded-xl px-3 py-2.5">{{ $user->cedula ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Correo electrónico</label>
                                <p class="text-sm text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700/50 rounded-xl px-3 py-2.5">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Número de celular <span class="text-red-500">*</span></label>
                                <input wire:model="phone" type="text" placeholder="0987654321" class="{{ $inputBase }}">
                            </div>
                        </div>

                        {{-- Sexo y Género --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Sexo <span class="text-red-500">*</span></label>
                                <select wire:model="sexo" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Género <span class="text-red-500">*</span></label>
                                <select wire:model="genero" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Transgénero">Transgénero</option>
                                    <option value="Fluido">Fluido</option>
                                    <option value="Cisgénero">Cisgénero</option>
                                    <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                </select>
                            </div>
                        </div>

                        {{-- Estado civil / Tipo sangre / Etnia --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Estado civil <span class="text-red-500">*</span></label>
                                <select wire:model="estado_civil" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach(['Soltero/a','Casado/a','Divorciado/a','Unión libre','Viudo/a'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de sangre <span class="text-red-500">*</span></label>
                                <select wire:model="tipo_sangre" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Etnia <span class="text-red-500">*</span></label>
                                <select wire:model.live="etnia" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach(['Indígena','Afroecuatoriano','Negro','Mulato','Montuvio','Mestizo','Blanco','Otro'] as $e)
                                    <option value="{{ $e }}">{{ $e }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Pueblo / Nacionalidad indígena --}}
                        @if($etnia === 'Indígena')
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Pueblo o nacionalidad indígena</label>
                            <input wire:model="pueblo_nacionalidad" type="text" placeholder="Ej: Kichwa, Shuar, Achuar…" class="{{ $inputUpper }}">
                        </div>
                        @endif

                        {{-- Fecha nacimiento / País de nacionalidad --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Fecha de nacimiento <span class="text-red-500">*</span></label>
                                <input wire:model="fecha_nacimiento" type="date" class="{{ $inputBase }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">País de nacionalidad <span class="text-red-500">*</span></label>
                                <select wire:model="nacionalidad" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach($paises as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Dirección de domicilio <span class="text-red-500">*</span></label>
                            <input wire:model="address" type="text" placeholder="Barrio, calle principal y secundaria" class="{{ $inputUpper }}">
                        </div>

                        {{-- Discapacidad --}}
                        <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-4 space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="tiene_discapacidad" type="checkbox" class="w-4 h-4 rounded accent-blue-600">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tengo discapacidad registrada en CONADIS</span>
                            </label>
                            @if($tiene_discapacidad)
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de discapacidad <span class="text-red-500">*</span></label>
                                    <select wire:model="tipo_discapacidad" class="{{ $selectBase }}">
                                        <option value="">— Selecciona —</option>
                                        @foreach(['Intelectual','Física','Visual','Auditiva','Mental','Otra'] as $d)
                                        <option value="{{ $d }}">{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Porcentaje (%) <span class="text-red-500">*</span></label>
                                    <input wire:model="porcentaje_discapacidad" type="number" min="0" max="100" placeholder="Ej: 45" class="{{ $inputBase }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">N.° CONADIS <span class="text-red-500">*</span></label>
                                    <input wire:model="nro_conadis" type="text" placeholder="N.° del carnet" class="{{ $inputUpper }}">
                                </div>
                            </div>
                            {{-- Certificado CONADIS --}}
                            <div class="pt-1">
                                <label class="block text-xs font-medium text-slate-500 mb-1.5">Certificado CONADIS <span class="text-slate-400">(opcional si aún no lo tienes)</span></label>
                                @if($certPath)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($certPath) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        Ver certificado actual
                                    </a>
                                </div>
                                @endif
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-blue-300 hover:bg-blue-50/40 cursor-pointer transition-colors group">
                                    @if($archivoCertDiscapacidad)
                                        <span class="text-xs text-blue-700 font-medium truncate flex-1">{{ $archivoCertDiscapacidad->getClientOriginalName() }}</span>
                                        <button wire:click="subirCertDiscapacidad" class="text-xs px-2.5 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium flex-shrink-0">
                                            <span wire:loading.remove wire:target="subirCertDiscapacidad">Subir</span>
                                            <span wire:loading wire:target="subirCertDiscapacidad">…</span>
                                        </button>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs text-slate-500 group-hover:text-blue-600">{{ $certPath ? 'Reemplazar certificado' : 'Subir certificado CONADIS' }} — PDF, JPG o PNG, máx. 5 MB</span>
                                    @endif
                                    <input type="file" wire:model="archivoCertDiscapacidad" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                </label>
                                @error('archivoCertDiscapacidad') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>

                    </div>

                    {{-- ── TAB: Procedencia ───────────────────────────────── --}}
                    <div x-show="seccion === 'procedencia'" x-cloak class="space-y-4">
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide">Lugar de nacimiento</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Provincia <span class="text-red-500">*</span></label>
                                <input wire:model="provincia_nacimiento" type="text" placeholder="Chimborazo" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Cantón <span class="text-red-500">*</span></label>
                                <input wire:model="canton_nacimiento" type="text" placeholder="Cumandá" class="{{ $inputUpper }}">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide pt-1">Residencia actual</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">País <span class="text-red-500">*</span></label>
                                <select wire:model="pais_residencia" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach($paises as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Provincia <span class="text-red-500">*</span></label>
                                <input wire:model="provincia_residencia" type="text" placeholder="Chimborazo" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Cantón <span class="text-red-500">*</span></label>
                                <input wire:model="canton_residencia" type="text" placeholder="Cumandá" class="{{ $inputUpper }}">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide pt-1">Colegio de procedencia</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de colegio <span class="text-red-500">*</span></label>
                                <select wire:model="tipo_colegio" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach(['Fiscal','Fiscomisional','Particular','Municipal','Extranjero'] as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nombre del colegio <span class="text-red-500">*</span></label>
                                <input wire:model="nombre_colegio" type="text" placeholder="Nombre del colegio" class="{{ $inputUpper }}">
                                <p class="text-[10px] text-slate-400 mt-1">Se guardará automáticamente en mayúsculas.</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide pt-1">Datos del hogar</p>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Ocupación <span class="text-red-500">*</span></label>
                            <input wire:model="ocupacion" type="text" placeholder="Ej: Estudiante, Trabaja en empresa, Estudio y trabajo" class="{{ $inputUpper }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">¿En qué emplea sus ingresos? <span class="text-red-500">*</span></label>
                            <input wire:model="empleo_ingresos" type="text" placeholder="Ej: Alimentación, educación, arriendo, ahorro…" class="{{ $inputUpper }}">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">¿Recibe Bono de Desarrollo Humano? <span class="text-red-500">*</span></label>
                                <select wire:model="bono_dh" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    <option value="SI">Sí</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Total ingresos del hogar <span class="text-red-500">*</span></label>
                                <input wire:model="ingresos_hogar" type="text" placeholder="Ej: $450 mensuales" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">N.° de miembros del hogar <span class="text-red-500">*</span></label>
                                <input wire:model="miembros_hogar" type="number" min="1" placeholder="4" class="{{ $inputBase }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── TAB: Familia ────────────────────────────────────── --}}
                    <div x-show="seccion === 'familia'" x-cloak class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nombre del padre <span class="text-red-500">*</span></label>
                                <input wire:model="padre" type="text" placeholder="Nombres completos" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Formación del padre <span class="text-red-500">*</span></label>
                                <select wire:model="formacion_padre" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach($formacionOpts as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nombre de la madre <span class="text-red-500">*</span></label>
                                <input wire:model="madre" type="text" placeholder="Nombres completos" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Formación de la madre <span class="text-red-500">*</span></label>
                                <select wire:model="formacion_madre" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach($formacionOpts as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tutor / representante legal <span class="text-slate-400 text-[10px]">(si aplica)</span></label>
                            <input wire:model="tutor" type="text" placeholder="Nombres completos del tutor" class="{{ $inputUpper }}">
                        </div>
                    </div>

                    {{-- ── TAB: Emergencia ─────────────────────────────────── --}}
                    <div x-show="seccion === 'emergencia'" x-cloak class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">En caso de emergencia llamar a <span class="text-red-500">*</span></label>
                            <input wire:model="contacto_emergencia" type="text" placeholder="Nombres completos" class="{{ $inputUpper }}">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Parentesco <span class="text-red-500">*</span></label>
                                <select wire:model="parentesco_emergencia" class="{{ $selectBase }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach(['Padre','Madre','Cónyuge','Hermano/a','Tío/a','Otro'] as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Teléfono de contacto <span class="text-red-500">*</span></label>
                                <input wire:model="telefono_emergencia" type="text" placeholder="0987654321" class="{{ $inputBase }}">
                            </div>
                        </div>
                    </div>

                    {{-- ── TAB: Facturación ─────────────────────────────────── --}}
                    <div x-show="seccion === 'facturacion'" x-cloak class="space-y-4">
                        <div class="p-3.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                            <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-1">¿Para qué sirve esta sección?</p>
                            <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                                Esta información es para emitir facturas cuando quien paga la matrícula es un tercero — padre, madre, tutor u otra persona —
                                especialmente en el caso de estudiantes menores de edad. Si eres tú quien factura, activa la opción a continuación.
                            </p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <input wire:model.live="is_facturador" type="checkbox" class="w-4 h-4 rounded accent-blue-600">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Mis datos de facturación son los mismos que los de esta ficha</span>
                        </label>
                        @if(! $is_facturador)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Nombres y apellidos de quien factura <span class="text-red-500">*</span></label>
                                <input wire:model="fact_nombre" type="text" placeholder="Nombre completo del facturador" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Cédula / RUC <span class="text-red-500">*</span></label>
                                <input wire:model="fact_documento" type="text" placeholder="Cédula o RUC" class="{{ $inputBase }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                                <input wire:model="fact_correo" type="email" placeholder="correo@ejemplo.com" class="{{ $inputBase }}">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Dirección <span class="text-red-500">*</span></label>
                                <input wire:model="fact_direccion" type="text" placeholder="Dirección del facturador" class="{{ $inputUpper }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Teléfono <span class="text-red-500">*</span></label>
                                <input wire:model="fact_telefono" type="text" placeholder="0987654321" class="{{ $inputBase }}">
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- ── BOTÓN GUARDAR --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-4 space-y-3">
                        <button wire:click="guardarFicha" wire:loading.attr="disabled"
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 text-white text-sm font-semibold transition-colors shadow-sm">
                            <svg wire:loading.remove wire:target="guardarFicha" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                            </svg>
                            <svg wire:loading wire:target="guardarFicha" class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="guardarFicha">Guardar información</span>
                            <span wire:loading wire:target="guardarFicha">Guardando…</span>
                        </button>
                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-700">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Puedes guardar en cualquier momento y seguir llenando después. Para enviar a revisión debes completar todos los campos con <span class="text-red-400 font-semibold">*</span> y adjuntar los documentos requeridos en la sección de abajo.
                            </p>
                        </div>
                    </div>

                </div>{{-- /p-5 --}}
            </div>{{-- /ficha --}}

            {{-- ── DOCUMENTOS ─────────────────────────────────────────────── --}}
            @if(in_array($aspirante->estado, ['pendiente', 'proceso', 'verificacion']))
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Mis documentos</h2>
                    @if($aspirante->estado === 'proceso')
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">Puedes subir documentos</span>
                    @endif
                </div>
                @php
                $puedeSubir    = in_array($aspirante->estado, ['pendiente', 'proceso']);
                $puedeSubirDoc = fn(string $campo) => $puedeSubir
                    || ($aspirante->estado === 'verificacion' && $aspirante->{"${campo}_estado"} === 'rechazado');
                @endphp
                <div class="p-4 space-y-3">

                    {{-- ── Banner documentos rechazados ── --}}
                    @php
                    $docsRechazados = array_filter([
                        $aspirante->cedula_estado         === 'rechazado' ? 'Cédula de identidad' : null,
                        $aspirante->bachiller_estado      === 'rechazado' ? 'Título de bachiller' : null,
                        $aspirante->habilitante_estado    === 'rechazado' ? 'Documento habilitante' : null,
                        $aspirante->pago_estado           === 'rechazado' ? 'Comprobante de pago' : null,
                        ($aspirante->esValidacionConocimientos() && $aspirante->hoja_vida_estado      === 'rechazado') ? 'Hoja de vida' : null,
                        ($aspirante->esValidacionConocimientos() && $aspirante->cert_laborales_estado === 'rechazado') ? 'Certificados laborales' : null,
                        ($aspirante->esValidacionConocimientos() && $aspirante->cert_cursos_estado    === 'rechazado') ? 'Certificados de cursos' : null,
                    ]);
                    @endphp
                    @if(count($docsRechazados) > 0)
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                                {{ count($docsRechazados) === 1 ? 'Tienes un documento rechazado' : 'Tienes ' . count($docsRechazados) . ' documentos rechazados' }}
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5 leading-relaxed">
                                Lee la observación en cada documento marcado en rojo, corrige el problema y vuelve a subirlo:
                                <strong>{{ implode(', ', $docsRechazados) }}</strong>.
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- ── Cédula ── --}}
                    <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Cédula de identidad y papeleta de votación<span class="text-red-500 text-xs">*</span></span>
                            @if($aspirante->cedula_path)
                            <span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->cedula_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->cedula_estado) }}</span>
                            @endif
                        </div>
                        @if($aspirante->cedula_observacion && $aspirante->cedula_estado === 'rechazado')
                        <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->cedula_observacion }}</p>
                        @endif
                        @if($aspirante->cedula_path)
                        <a href="{{ Storage::url($aspirante->cedula_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            Ver documento
                        </a>
                        @endif
                        @if($puedeSubirDoc('cedula'))
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-blue-300 hover:bg-blue-50/40 cursor-pointer transition-colors group">
                            @if($archivoCedula)
                                <span class="text-xs text-blue-700 font-medium truncate flex-1">{{ $archivoCedula->getClientOriginalName() }}</span>
                                <button wire:click="subirCedula" class="text-xs px-2.5 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium flex-shrink-0">
                                    <span wire:loading.remove wire:target="subirCedula">Subir</span>
                                    <span wire:loading wire:target="subirCedula">…</span>
                                </button>
                            @else
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <span class="text-xs text-slate-500 group-hover:text-blue-600">{{ $aspirante->cedula_path ? 'Reemplazar cédula' : 'Subir cédula' }} — PDF, JPG o PNG, máx. 5 MB</span>
                            @endif
                            <input type="file" wire:model="archivoCedula" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        </label>
                        @error('archivoCedula') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- ── Bachiller ── --}}
                    <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Título de bachiller <span class="text-red-500 text-xs">*</span></span>
                            @if($aspirante->bachiller_path)
                            <span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->bachiller_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->bachiller_estado) }}</span>
                            @endif
                        </div>
                        @if($aspirante->bachiller_observacion && $aspirante->bachiller_estado === 'rechazado')
                        <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->bachiller_observacion }}</p>
                        @endif
                        @if($aspirante->bachiller_path)
                        <a href="{{ Storage::url($aspirante->bachiller_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            Ver documento
                        </a>
                        @endif
                        @if($puedeSubirDoc('bachiller'))
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-blue-300 hover:bg-blue-50/40 cursor-pointer transition-colors group">
                            @if($archivoBachiller)
                                <span class="text-xs text-blue-700 font-medium truncate flex-1">{{ $archivoBachiller->getClientOriginalName() }}</span>
                                <button wire:click="subirBachiller" class="text-xs px-2.5 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium flex-shrink-0">
                                    <span wire:loading.remove wire:target="subirBachiller">Subir</span>
                                    <span wire:loading wire:target="subirBachiller">…</span>
                                </button>
                            @else
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <span class="text-xs text-slate-500 group-hover:text-blue-600">{{ $aspirante->bachiller_path ? 'Reemplazar título' : 'Subir título de bachiller' }} — PDF, JPG o PNG, máx. 5 MB</span>
                            @endif
                            <input type="file" wire:model="archivoBachiller" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        </label>
                        @error('archivoBachiller') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- ── Habilitante ── --}}
                    <div class="border border-amber-100 dark:border-amber-800/40 rounded-xl overflow-hidden">
                        <div class="px-3.5 py-2.5 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-100 dark:border-amber-800/40">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-400">Documento habilitante — solo para quienes aún cursan el bachillerato</p>
                            <p class="text-[11px] text-amber-600 dark:text-amber-500 mt-0.5">
                                Si ya tienes tu título de bachiller, súbelo arriba y <strong>omite este campo</strong>.
                                Este documento aplica únicamente a aspirantes que aún no cuentan con el título de bachiller
                                (ej. certificado de penúltimo año o constancia de estudios).
                            </p>
                        </div>
                        <div class="p-3.5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Documento habilitante</span>
                                @if($aspirante->habilitante_path)
                                <span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->habilitante_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->habilitante_estado) }}</span>
                                @endif
                            </div>
                            @if($aspirante->habilitante_observacion && $aspirante->habilitante_estado === 'rechazado')
                            <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->habilitante_observacion }}</p>
                            @endif
                            @if($aspirante->habilitante_path)
                            <a href="{{ Storage::url($aspirante->habilitante_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                Ver documento
                            </a>
                            @endif
                            @if($puedeSubirDoc('habilitante'))
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-amber-300 hover:bg-amber-50/40 cursor-pointer transition-colors group">
                                @if($archivoHabilitante)
                                    <span class="text-xs text-amber-700 font-medium truncate flex-1">{{ $archivoHabilitante->getClientOriginalName() }}</span>
                                    <button wire:click="subirHabilitante" class="text-xs px-2.5 py-1 rounded-lg bg-amber-500 text-white hover:bg-amber-600 font-medium flex-shrink-0">
                                        <span wire:loading.remove wire:target="subirHabilitante">Subir</span>
                                        <span wire:loading wire:target="subirHabilitante">…</span>
                                    </button>
                                @else
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    <span class="text-xs text-slate-500 group-hover:text-amber-600">{{ $aspirante->habilitante_path ? 'Reemplazar documento' : 'Subir documento habilitante' }} — PDF, JPG o PNG, máx. 5 MB</span>
                                @endif
                                <input type="file" wire:model="archivoHabilitante" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                            </label>
                            @error('archivoHabilitante') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    </div>

                    {{-- ── Comprobante de pago ── --}}
                    <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Comprobante de pago de matrícula <span class="text-red-500 text-xs">*</span></span>
                            @if($aspirante->pago_comprobante_path)
                            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->pago_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">
                                {{ ucfirst($aspirante->pago_estado) }}@if($aspirante->pago_monto) · ${{ number_format($aspirante->pago_monto, 2) }}@endif
                            </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Adjunta el comprobante del pago de matrícula que realizaste en secretaría o por transferencia bancaria. Puede ser una foto, captura de pantalla o PDF del recibo.
                        </p>
                        @if($aspirante->pago_observacion && $aspirante->pago_estado === 'rechazado')
                        <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->pago_observacion }}</p>
                        @endif
                        @if($aspirante->pago_comprobante_path)
                        <a href="{{ Storage::url($aspirante->pago_comprobante_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            Ver comprobante
                        </a>
                        @endif
                        @if($puedeSubirDoc('pago'))
                        <div class="space-y-2">
                            <input wire:model="pagoMonto" type="number" step="0.01" min="0" placeholder="Monto pagado (ej: 50.00)"
                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-blue-300 hover:bg-blue-50/40 cursor-pointer transition-colors group">
                                @if($archivoPago)
                                    <span class="text-xs text-blue-700 font-medium truncate flex-1">{{ $archivoPago->getClientOriginalName() }}</span>
                                    <button wire:click="subirPago" class="text-xs px-2.5 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-medium flex-shrink-0">
                                        <span wire:loading.remove wire:target="subirPago">Subir</span>
                                        <span wire:loading wire:target="subirPago">…</span>
                                    </button>
                                @else
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    <span class="text-xs text-slate-500 group-hover:text-blue-600">{{ $aspirante->pago_comprobante_path ? 'Reemplazar comprobante' : 'Subir comprobante' }} — PDF, JPG o PNG, máx. 5 MB</span>
                                @endif
                                <input type="file" wire:model="archivoPago" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                            </label>
                            @error('archivoPago') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>

                    {{-- ── Documentos adicionales: Validación de conocimientos ── --}}
                    @if($aspirante->esValidacionConocimientos())
                    <div class="border-2 border-indigo-200 dark:border-indigo-700 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/30 border-b border-indigo-200 dark:border-indigo-700">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                <h3 class="text-sm font-bold text-indigo-800 dark:text-indigo-300">Documentos de Validación de Conocimientos</h3>
                            </div>
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 leading-relaxed">
                                Tu proceso es de <strong>validación de conocimientos</strong>. Además de los documentos anteriores, debes adjuntar los siguientes.
                            </p>
                            <div class="mt-2 flex items-start gap-1.5 p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                                <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <p class="text-[10px] font-semibold text-amber-700 dark:text-amber-400">Todos los documentos deben estar escaneados y ser claramente legibles. Documentos borrosos o ilegibles serán rechazados.</p>
                            </div>
                        </div>
                        <div class="p-4 space-y-3">

                            {{-- Hoja de vida --}}
                            <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hoja de vida <span class="text-red-500 text-xs">*</span></span>
                                    @if($aspirante->hoja_vida_path)
                                    <span class="text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->hoja_vida_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->hoja_vida_estado) }}</span>
                                    @endif
                                </div>
                                @if($aspirante->hoja_vida_observacion && $aspirante->hoja_vida_estado === 'rechazado')
                                <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->hoja_vida_observacion }}</p>
                                @endif
                                @if($aspirante->hoja_vida_path)
                                <a href="{{ Storage::url($aspirante->hoja_vida_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Ver documento
                                </a>
                                @endif
                                @if($puedeSubirDoc('hoja_vida'))
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-indigo-300 hover:bg-indigo-50/40 cursor-pointer transition-colors group">
                                    @if($archivoHojaVida)
                                        <span class="text-xs text-indigo-700 font-medium truncate flex-1">{{ $archivoHojaVida->getClientOriginalName() }}</span>
                                        <button wire:click="subirHojaVida" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium flex-shrink-0">
                                            <span wire:loading.remove wire:target="subirHojaVida">Subir</span>
                                            <span wire:loading wire:target="subirHojaVida">…</span>
                                        </button>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs text-slate-500 group-hover:text-indigo-600">{{ $aspirante->hoja_vida_path ? 'Reemplazar hoja de vida' : 'Subir hoja de vida' }} — PDF, JPG o PNG, máx. 5 MB</span>
                                    @endif
                                    <input type="file" wire:model="archivoHojaVida" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                </label>
                                @error('archivoHojaVida') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                @endif
                            </div>

                            {{-- Certificados laborales --}}
                            <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Certificados laborales <span class="text-red-500 text-xs">*</span></span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Unifica todos en un <strong>solo PDF</strong> antes de subir</p>
                                    </div>
                                    @if($aspirante->cert_laborales_path)
                                    <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->cert_laborales_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->cert_laborales_estado) }}</span>
                                    @endif
                                </div>
                                @if($aspirante->cert_laborales_observacion && $aspirante->cert_laborales_estado === 'rechazado')
                                <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->cert_laborales_observacion }}</p>
                                @endif
                                @if($aspirante->cert_laborales_path)
                                <a href="{{ Storage::url($aspirante->cert_laborales_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Ver documento
                                </a>
                                @endif
                                @if($puedeSubirDoc('cert_laborales'))
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-indigo-300 hover:bg-indigo-50/40 cursor-pointer transition-colors group">
                                    @if($archivoCertLaborales)
                                        <span class="text-xs text-indigo-700 font-medium truncate flex-1">{{ $archivoCertLaborales->getClientOriginalName() }}</span>
                                        <button wire:click="subirCertLaborales" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium flex-shrink-0">
                                            <span wire:loading.remove wire:target="subirCertLaborales">Subir</span>
                                            <span wire:loading wire:target="subirCertLaborales">…</span>
                                        </button>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs text-slate-500 group-hover:text-indigo-600">{{ $aspirante->cert_laborales_path ? 'Reemplazar certificados' : 'Subir certificados laborales (PDF único)' }} — máx. 5 MB</span>
                                    @endif
                                    <input type="file" wire:model="archivoCertLaborales" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                </label>
                                @error('archivoCertLaborales') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                @endif
                            </div>

                            {{-- Certificados de cursos / capacitaciones --}}
                            <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Certificados de cursos o capacitaciones <span class="text-red-500 text-xs">*</span></span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Unifica todos en un <strong>solo PDF</strong> antes de subir</p>
                                    </div>
                                    @if($aspirante->cert_cursos_path)
                                    <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full border font-medium {{ $docEstadoStyle[$aspirante->cert_cursos_estado] ?? 'bg-slate-50 border-slate-200 text-slate-500' }}">{{ ucfirst($aspirante->cert_cursos_estado) }}</span>
                                    @endif
                                </div>
                                @if($aspirante->cert_cursos_observacion && $aspirante->cert_cursos_estado === 'rechazado')
                                <p class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ $aspirante->cert_cursos_observacion }}</p>
                                @endif
                                @if($aspirante->cert_cursos_path)
                                <a href="{{ Storage::url($aspirante->cert_cursos_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Ver documento
                                </a>
                                @endif
                                @if($puedeSubirDoc('cert_cursos'))
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-indigo-300 hover:bg-indigo-50/40 cursor-pointer transition-colors group">
                                    @if($archivoCertCursos)
                                        <span class="text-xs text-indigo-700 font-medium truncate flex-1">{{ $archivoCertCursos->getClientOriginalName() }}</span>
                                        <button wire:click="subirCertCursos" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium flex-shrink-0">
                                            <span wire:loading.remove wire:target="subirCertCursos">Subir</span>
                                            <span wire:loading wire:target="subirCertCursos">…</span>
                                        </button>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs text-slate-500 group-hover:text-indigo-600">{{ $aspirante->cert_cursos_path ? 'Reemplazar certificados' : 'Subir certificados de cursos (PDF único)' }} — máx. 5 MB</span>
                                    @endif
                                    <input type="file" wire:model="archivoCertCursos" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                </label>
                                @error('archivoCertCursos') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                @endif
                            </div>

                            {{-- Mecanizado IESS — opcional --}}
                            <div class="border border-slate-100 dark:border-slate-700 rounded-xl p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Mecanizado del IESS</span>
                                        <span class="ml-2 text-[10px] font-semibold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full">opcional</span>
                                    </div>
                                    @if($aspirante->mecanizado_iess_path)
                                    <span class="text-xs px-2 py-0.5 rounded-full border font-medium bg-slate-50 border-slate-200 text-slate-500">Subido</span>
                                    @endif
                                </div>
                                @if($aspirante->mecanizado_iess_path)
                                <a href="{{ Storage::url($aspirante->mecanizado_iess_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    Ver documento
                                </a>
                                @endif
                                @if($puedeSubir)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-colors group">
                                    @if($archivoMecanizadoIess)
                                        <span class="text-xs text-slate-700 font-medium truncate flex-1">{{ $archivoMecanizadoIess->getClientOriginalName() }}</span>
                                        <button wire:click="subirMecanizadoIess" class="text-xs px-2.5 py-1 rounded-lg bg-slate-600 text-white hover:bg-slate-700 font-medium flex-shrink-0">
                                            <span wire:loading.remove wire:target="subirMecanizadoIess">Subir</span>
                                            <span wire:loading wire:target="subirMecanizadoIess">…</span>
                                        </button>
                                    @else
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                        <span class="text-xs text-slate-500 group-hover:text-slate-600">{{ $aspirante->mecanizado_iess_path ? 'Reemplazar mecanizado' : 'Subir mecanizado del IESS' }} — PDF, JPG o PNG, máx. 5 MB</span>
                                    @endif
                                    <input type="file" wire:model="archivoMecanizadoIess" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                </label>
                                @error('archivoMecanizadoIess') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                @endif
                            </div>

                        </div>
                    </div>
                    @endif

                    {{-- ── Botón enviar a revisión ── --}}
                    @if($aspirante->estado === 'proceso')
                    <div class="pt-1">
                        <button
                            @if($listo)
                            wire:click="enviarRevision"
                            wire:confirm="¿Estás seguro de enviar tu ficha y documentación para revisión? Asegúrate de que todo sea correcto antes de continuar."
                            @else
                            disabled
                            @endif
                            class="w-full py-3 rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center justify-center gap-2
                                   {{ $listo
                                      ? 'bg-teal-600 hover:bg-teal-700 text-white cursor-pointer'
                                      : 'bg-slate-100 dark:bg-slate-700 text-slate-400 cursor-not-allowed' }}">
                            @if($listo)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            Enviar ficha y documentos para revisión
                            @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            {{ $progreso }}% completado — aún no se puede enviar
                            @endif
                        </button>
                        @if(!$listo)
                        <p class="text-xs text-slate-400 text-center mt-2">Faltan {{ 100 - $progreso }}% — completa los campos obligatorios y sube los documentos requeridos.</p>
                        @endif
                    </div>
                    @endif

                </div>{{-- /p-4 docs --}}
            </div>
            @endif

        </div>{{-- /col derecha --}}
    </div>{{-- /grid --}}

    @endif
</div>
</div>
