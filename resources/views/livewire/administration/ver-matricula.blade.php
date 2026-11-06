<div>
    @if($mostrar)
    @php
        $m          = $this->matricula;
        $editable   = $this->editable;
        $docentes   = $this->docentes;
        $estadoColor = fn($estado) => match($estado) {
            'Habilitada', 'Pagado', 'Completada', 'Aprobado', 'Finalizado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/25 dark:text-emerald-400',
            'Pendiente_Pago', 'Pendiente', 'Parcial', 'Inscrito'            => 'bg-amber-100 text-amber-700 dark:bg-amber-900/25 dark:text-amber-400',
            'Vencido', 'Retirada', 'Retirado', 'Cancelada', 'Reprobado'     => 'bg-red-100 text-red-700 dark:bg-red-900/25 dark:text-red-400',
            default                                                         => 'bg-slate-100 text-slate-600 dark:bg-slate-700/60 dark:text-slate-300',
        };
        $tipoLabel = ['Nueva' => 'Nueva', 'Renovacion' => 'Renovación', 'Arrastre' => 'Con arrastre', 'Validacion' => 'Validación de conocimientos'];
    @endphp

    <div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto"
         x-data x-init="document.body.style.overflow='hidden'"
         x-on:keydown.escape.window="$wire.cerrar()">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrar"></div>

        <div class="relative z-10 w-full max-w-4xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl shadow-black/50
                    border border-slate-200 dark:border-white/[0.08] overflow-hidden" x-on:click.stop>

            <div class="h-1 bg-gradient-to-r from-sky-600 via-sky-500 to-indigo-600"></div>

            {{-- ─── HEADER ─── --}}
            <div class="px-6 py-4 flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 dark:border-white/[0.06]">
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white/90">Matrícula del estudiante</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ $this->estudiante?->name }}
                        @if($this->estudiante?->cedula) · C.I. {{ $this->estudiante->cedula }} @endif
                        @if($this->estudiante?->matricula_numero) · {{ $this->estudiante->matricula_numero }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if($this->matriculas->count() > 1)
                    <select wire:model.live="matriculaId"
                        class="text-xs rounded-lg border-slate-200 dark:border-white/[0.08] bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 py-1.5 pl-2.5 pr-8">
                        @foreach($this->matriculas as $op)
                            <option value="{{ $op->id }}">
                                {{ $op->periodo?->code ?? '—' }} · {{ $op->code }}{{ $op->tipo === 'Validacion' ? ' (validación)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                    <button wire:click="cerrar" class="text-slate-400 hover:text-slate-600 dark:hover:text-white/70 transition-colors" title="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">
            @if(! $m)
                <p class="py-10 text-center text-sm text-slate-500">Este estudiante no tiene matrículas registradas.</p>
            @else

                {{-- ─── RESUMEN ─── --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.1em] text-slate-400">Código</p>
                        <p class="font-mono font-semibold text-slate-700 dark:text-white/85 mt-0.5">{{ $m->code }}</p>
                    </div>
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.1em] text-slate-400">Período</p>
                        <p class="font-semibold text-slate-700 dark:text-white/85 mt-0.5">{{ $m->periodo?->code ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.1em] text-slate-400">Carrera</p>
                        <p class="font-semibold text-slate-700 dark:text-white/85 mt-0.5">{{ $m->carrera?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[0.65rem] uppercase tracking-[0.1em] text-slate-400">Estado</p>
                        <p class="mt-0.5 flex flex-wrap gap-1">
                            <span class="px-2 py-0.5 rounded-md font-medium {{ $estadoColor($m->estado) }}">{{ str_replace('_', ' ', $m->estado) }}</span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300">{{ $tipoLabel[$m->tipo] ?? $m->tipo }}</span>
                        </p>
                    </div>
                </div>
                <p class="text-[0.7rem] text-slate-500 dark:text-slate-400 -mt-2">
                    Registrada el {{ $m->fecha_matricula?->format('d/m/Y') ?? '—' }}
                    @if($m->observaciones) · {{ $m->observaciones }} @endif
                </p>

                @if(! $editable && $m->tipo !== 'Validacion' && auth()->user()?->can('editar_matriculas'))
                <p class="text-[0.7rem] px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                    Solo lectura: las correcciones solo se hacen en matrículas vigentes del período en curso de su carrera.
                </p>
                @endif

                {{-- ─── MATERIAS ─── --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                            Materias ({{ $m->detalles->count() }})
                        </h3>
                        @if($editable && $accion === null)
                        <button wire:click="iniciarAccion('agregar')"
                            class="text-[0.7rem] font-semibold px-2.5 py-1 rounded-lg text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/40 transition">
                            + Agregar materia
                        </button>
                        @endif
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-white/[0.08]">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                                <tr class="text-left">
                                    <th class="px-3 py-2 font-medium">Materia</th>
                                    <th class="px-3 py-2 font-medium">Paralelo</th>
                                    <th class="px-3 py-2 font-medium">Docente</th>
                                    <th class="px-3 py-2 font-medium">Estado</th>
                                    <th class="px-3 py-2 font-medium text-right">Nota</th>
                                    @if($editable)<th class="px-3 py-2"></th>@endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04] text-slate-700 dark:text-slate-300">
                                @forelse($m->detalles->sortBy(fn($d) => [$d->materia?->semestre?->order, $d->materia?->name]) as $d)
                                @php $conRegistros = $d->calificaciones_count > 0 || $d->asistencias_count > 0; @endphp
                                <tr class="{{ $detalleId === $d->id ? 'bg-sky-50/60 dark:bg-sky-900/10' : '' }}">
                                    <td class="px-3 py-2">
                                        <p class="font-medium">{{ $d->materia?->name ?? '—' }}</p>
                                        <p class="text-[0.65rem] text-slate-400">
                                            {{ $d->materia?->semestre?->name }}
                                            @if($d->tipo === 'Arrastre') · <span class="text-amber-600 dark:text-amber-400 font-medium">Arrastre</span> @endif
                                            @if($d->tipo === 'Validacion') · <span class="text-purple-600 dark:text-purple-400 font-medium">Convalidada</span> @endif
                                        </p>
                                    </td>
                                    <td class="px-3 py-2">{{ $d->paralelo?->name ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $docentes[$d->materia_id . '_' . $d->paralelo_id] ?? '—' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-1.5 py-0.5 rounded {{ $estadoColor($d->estado) }}">{{ $d->estado }}</span>
                                        @if($d->asistencias_count > 0)
                                            <span class="block text-[0.6rem] text-slate-400 mt-0.5">{{ $d->asistencias_count }} asistencia(s)</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right tabular-nums">
                                        @if($d->calificacion?->nota_final !== null)
                                            {{ number_format((float) $d->calificacion->nota_final, 2) }}
                                            @if($d->calificacion->es_borrador)<span class="block text-[0.6rem] text-slate-400">borrador</span>@endif
                                        @elseif($d->calificaciones_count > 0)
                                            <span class="text-slate-400">en curso</span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    @if($editable)
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        @if($accion === null && $d->estado !== 'Retirado')
                                            <button wire:click="iniciarAccion('paralelo', {{ $d->id }})"
                                                class="text-[0.65rem] font-semibold text-sky-600 dark:text-sky-400 hover:underline">Cambiar paralelo</button>
                                            @if(! $conRegistros && $d->tipo !== 'Arrastre' && $m->detalles->count() > 1)
                                                <button wire:click="iniciarAccion('quitar', {{ $d->id }})"
                                                    class="ml-2 text-[0.65rem] font-semibold text-red-600 dark:text-red-400 hover:underline">Quitar</button>
                                            @endif
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-slate-400">Sin materias registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ─── PANEL DE LA ACCIÓN EN CURSO ─── --}}
                @if($editable && $accion)
                @php $detalleSel = $m->detalles->firstWhere('id', $detalleId); @endphp
                <div class="p-4 rounded-xl border border-sky-200 dark:border-sky-500/30 bg-sky-50/50 dark:bg-sky-900/10 space-y-3 text-xs">
                    <p class="font-semibold text-slate-700 dark:text-white/85">
                        @if($accion === 'paralelo') Cambiar de paralelo: {{ $detalleSel?->materia?->name }} (actual: {{ $detalleSel?->paralelo?->name ?? '—' }})
                        @elseif($accion === 'agregar') Agregar una materia a {{ $m->code }}
                        @else Quitar {{ $detalleSel?->materia?->name }} de la matrícula
                        @endif
                    </p>

                    @if($accion === 'paralelo')
                        <p class="text-slate-500 dark:text-slate-400">
                            Las notas y asistencias se conservan. Las notas en curso pasan al docente del paralelo nuevo para que pueda seguir calificando.
                        </p>
                    @elseif($accion === 'quitar')
                        <p class="text-slate-500 dark:text-slate-400">La materia no tiene notas ni asistencias; se elimina de la matrícula y libera su cupo.</p>
                    @endif

                    @if($accion === 'agregar')
                        @if(empty($this->materiasParaAgregar))
                            <p class="text-slate-500 dark:text-slate-400">No hay materias que se puedan agregar: todas están aprobadas, inscritas, en arrastre, sin prerrequisitos o sin oferta en este período.</p>
                        @else
                        <label class="block">
                            <span class="text-slate-600 dark:text-slate-300">Materia</span>
                            <select wire:model.live="nuevaMateriaId"
                                class="mt-1 w-full rounded-lg border-slate-200 dark:border-white/[0.08] bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs py-2">
                                <option value="">Selecciona…</option>
                                @foreach($this->materiasParaAgregar as $op)
                                    <option value="{{ $op['id'] }}">{{ $op['semestre'] }} · {{ $op['name'] }}</option>
                                @endforeach
                            </select>
                            @error('nuevaMateriaId')<span class="text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
                        </label>
                        @endif
                    @endif

                    @if($accion === 'paralelo' || ($accion === 'agregar' && $nuevaMateriaId))
                        <label class="block">
                            <span class="text-slate-600 dark:text-slate-300">Paralelo</span>
                            @if(empty($this->paralelosDisponibles))
                                <p class="mt-1 text-slate-500 dark:text-slate-400">No hay otro paralelo ofertado para esta materia en el período.</p>
                            @else
                            <select wire:model="nuevoParaleloId"
                                class="mt-1 w-full rounded-lg border-slate-200 dark:border-white/[0.08] bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs py-2">
                                <option value="">Selecciona…</option>
                                @foreach($this->paralelosDisponibles as $op)
                                    <option value="{{ $op['id'] }}" @disabled($op['disponible'] <= 0)>
                                        {{ $op['name'] }} — {{ $op['disponible'] > 0 ? $op['disponible'] . ' cupo(s)' : 'sin cupo' }}
                                    </option>
                                @endforeach
                            </select>
                            @endif
                            @error('nuevoParaleloId')<span class="text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
                        </label>
                    @endif

                    <label class="block">
                        <span class="text-slate-600 dark:text-slate-300">Motivo</span>
                        <textarea wire:model="motivo" rows="2" placeholder="Queda registrado en la auditoría"
                            class="mt-1 w-full rounded-lg border-slate-200 dark:border-white/[0.08] bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs px-3 py-2 resize-none"></textarea>
                        @error('motivo')<span class="text-red-600 dark:text-red-400">{{ $message }}</span>@enderror
                    </label>

                    <div class="flex justify-end gap-2">
                        <button wire:click="cancelarAccion"
                            class="px-3 py-1.5 rounded-lg font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-100 dark:hover:bg-white/[0.04]">
                            Cancelar
                        </button>
                        @php
                            [$metodo, $etiqueta, $color] = match($accion) {
                                'paralelo' => ['guardarCambioParalelo', 'Cambiar paralelo', 'bg-sky-600 hover:bg-sky-700'],
                                'agregar'  => ['guardarAgregarMateria', 'Agregar materia', 'bg-sky-600 hover:bg-sky-700'],
                                default    => ['guardarQuitarMateria', 'Quitar materia', 'bg-red-600 hover:bg-red-700'],
                            };
                        @endphp
                        <button wire:click="{{ $metodo }}" wire:loading.attr="disabled" wire:target="{{ $metodo }}"
                            class="px-3 py-1.5 rounded-lg font-semibold text-white {{ $color }} disabled:opacity-50 transition">
                            {{ $etiqueta }}
                        </button>
                    </div>
                </div>
                @endif

                {{-- ─── OBLIGACIONES ─── --}}
                <div>
                    <h3 class="text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400 mb-2">
                        Obligaciones financieras ({{ $m->obligacionesFinancieras->count() }})
                    </h3>
                    @if($m->obligacionesFinancieras->isEmpty())
                        <p class="text-xs text-slate-400">Sin obligaciones registradas.</p>
                    @else
                    @php
                        $vigentes = $m->obligacionesFinancieras->where('estado', '!=', 'Invalidado');
                        $pagado   = $vigentes->sum(fn($o) => $o->pagos->where('estado', \App\Models\Pago::ESTADO_APROBADO)->sum('monto'));
                        $total    = $vigentes->sum(fn($o) => (float) $o->monto_final);
                    @endphp
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-white/[0.08]">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                                <tr class="text-left">
                                    <th class="px-3 py-2 font-medium">Concepto</th>
                                    <th class="px-3 py-2 font-medium text-right">Original</th>
                                    <th class="px-3 py-2 font-medium text-right">Descuento</th>
                                    <th class="px-3 py-2 font-medium text-right">A pagar</th>
                                    <th class="px-3 py-2 font-medium text-right">Pagado</th>
                                    <th class="px-3 py-2 font-medium">Vence</th>
                                    <th class="px-3 py-2 font-medium">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/[0.04] text-slate-700 dark:text-slate-300 tabular-nums">
                                @foreach($m->obligacionesFinancieras as $o)
                                @php $pagadoO = $o->pagos->where('estado', \App\Models\Pago::ESTADO_APROBADO)->sum('monto'); @endphp
                                <tr class="{{ $o->estado === 'Invalidado' ? 'opacity-50' : '' }}">
                                    <td class="px-3 py-2">
                                        <p class="font-medium">{{ ucfirst(strtolower($o->tipo)) }}</p>
                                        <p class="text-[0.65rem] text-slate-400">
                                            {{ $o->descripcion }}
                                            @if($o->becaAplicada) · Beca {{ $o->becaAplicada->tipoBeca?->nombre }} @endif
                                            @if($o->convenioAplicado) · Convenio {{ $o->convenioAplicado->tipoConvenio?->nombre }} @endif
                                        </p>
                                    </td>
                                    <td class="px-3 py-2 text-right">${{ number_format((float) $o->monto_original, 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ (float) $o->descuento > 0 ? '−$' . number_format((float) $o->descuento, 2) : '—' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold">${{ number_format((float) $o->monto_final, 2) }}</td>
                                    <td class="px-3 py-2 text-right">${{ number_format((float) $pagadoO, 2) }}</td>
                                    <td class="px-3 py-2">{{ $o->fecha_vencimiento ? \Carbon\Carbon::parse($o->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                                    <td class="px-3 py-2"><span class="px-1.5 py-0.5 rounded {{ $estadoColor($o->estado) }}">{{ $o->estado }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-800/60 font-semibold text-slate-700 dark:text-slate-200 tabular-nums">
                                <tr>
                                    <td class="px-3 py-2" colspan="3">Total vigente</td>
                                    <td class="px-3 py-2 text-right">${{ number_format($total, 2) }}</td>
                                    <td class="px-3 py-2 text-right">${{ number_format($pagado, 2) }}</td>
                                    <td class="px-3 py-2" colspan="2">Pendiente: ${{ number_format(max(0, $total - $pagado), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>
            @endif
            </div>

            <div class="px-6 py-3 border-t border-slate-100 dark:border-white/[0.06] bg-slate-50 dark:bg-black/10 flex justify-end">
                <button wire:click="cerrar"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-white/[0.08] hover:bg-slate-100 dark:hover:bg-white/[0.04] transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
