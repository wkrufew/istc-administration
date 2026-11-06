@php
    $badge = [
        'sin_cargar' => ['Sin cargar', 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'],
        'pendiente'  => ['Pendiente de revisión', 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
        'aprobado'   => ['Aprobado', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'rechazado'  => ['Rechazado', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
        'solicitado' => ['Solicitado', 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300'],
    ];
    $btn = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors disabled:opacity-50';
    $cargados = $vigentePorTipo->filter(fn($d) => $d->tieneArchivo())->count();
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 px-6 py-3.5 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2 flex-wrap">
            <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Documentos</h3>
            @if ($perfil === 'estudiante')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $esValidacion ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                    {{ $esValidacion ? 'Validación de conocimientos' : 'Proceso regular' }}
                </span>
            @endif
        </div>
        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $cargados }} de {{ count($tipos) }} cargados · ninguno es obligatorio</span>
    </div>

    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
        @foreach ($tipos as $tipo => $nombre)
            @php
                $doc = $vigentePorTipo->get($tipo);
                $estado = $doc?->tieneArchivo() || $doc?->estado === 'solicitado' ? $doc->estado : 'sin_cargar';
                $nVersiones = $versiones[$tipo] ?? 0;
            @endphp
            <li class="px-6 py-4" wire:key="doc-{{ $tipo }}">
                <div class="flex flex-col lg:flex-row lg:items-start gap-3">
                    {{-- Documento y estado --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $nombre }}</p>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge[$estado][1] }}">{{ $badge[$estado][0] }}</span>
                        </div>
                        @if ($doc?->observacion && in_array($doc->estado, ['rechazado', 'solicitado'], true))
                            <p class="text-xs mt-1 {{ $doc->estado === 'rechazado' ? 'text-red-600 dark:text-red-400' : 'text-sky-700 dark:text-sky-300' }}">
                                Observación: {{ $doc->observacion }}
                            </p>
                        @endif
                        @if ($doc?->tieneArchivo())
                            <p class="text-[11px] text-slate-400 mt-1">
                                Subido {{ $doc->subido_at?->format('d/m/Y') ?? '—' }}
                                @if ($doc->subidoPor) por {{ $doc->subidoPor->name }} @elseif ($doc->origen === 'admision') en admisión @endif
                                @if ($doc->revisado_at && $doc->estado !== 'pendiente') · revisado {{ $doc->revisado_at->format('d/m/Y') }}@if ($doc->revisadoPor) por {{ $doc->revisadoPor->name }}@endif @endif
                            </p>
                        @endif
                        {{-- Lo que secretaría confirmó al verificar el pago en admisión --}}
                        @if ($tipo === 'pago_admision' && $pagoAdmision)
                            <p class="text-xs mt-1.5 inline-flex flex-wrap items-center gap-x-2 gap-y-0.5 rounded-lg px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300">
                                <span>Monto confirmado: <strong>${{ number_format($pagoAdmision['monto'], 2) }}</strong></span>
                                @if ($pagoAdmision['metodo']) <span>· {{ $pagoAdmision['metodo'] }}</span> @endif
                                @if ($pagoAdmision['fecha_pago']) <span>· Pagado el {{ $pagoAdmision['fecha_pago'] }}</span> @endif
                                @if ($pagoAdmision['verificado']) <span>· Verificado el {{ $pagoAdmision['verificado'] }}</span> @endif
                                <span>· {{ $pagoAdmision['matricula'] ? 'Aplicado a la matrícula ' . $pagoAdmision['matricula'] : ($pagoAdmision['aplicado'] ? 'Aplicado a su matrícula' : 'Aún no aplicado a una matrícula') }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-wrap items-center gap-1.5">
                        @if ($doc?->tieneArchivo())
                            <a href="{{ $doc->url() }}" target="_blank" class="{{ $btn }} border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Ver</a>
                            @if ($doc->estado !== 'aprobado')
                                <button wire:click="aprobar({{ $doc->id }})" wire:loading.attr="disabled" class="{{ $btn }} border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30">Aprobar</button>
                            @endif
                            @if ($doc->estado !== 'rechazado')
                                <button wire:click="abrirAccion('rechazar', {{ $doc->id }})" class="{{ $btn }} border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">Rechazar</button>
                            @endif
                            @if ($puedeEliminar)
                                <button wire:click="abrirAccion('eliminar', {{ $doc->id }})" title="Solo si se subió por error: borra el archivo para siempre"
                                        class="{{ $btn }} border-red-600 bg-red-600 text-white hover:bg-red-700">Eliminar definitivamente</button>
                            @endif
                        @endif
                        @if ($estado !== 'solicitado')
                            <button wire:click="abrirAccion('solicitar', null, '{{ $tipo }}')" class="{{ $btn }} border-sky-200 dark:border-sky-800 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-900/30">Solicitar</button>
                        @endif
                        @if ($nVersiones > 0)
                            <button wire:click="verHistorial('{{ $tipo }}')" class="{{ $btn }} border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">
                                Historial ({{ $nVersiones }})
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Subir / reemplazar --}}
                <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2">
                    <input type="file" wire:model="archivos.{{ $tipo }}" accept="application/pdf,image/jpeg,image/png"
                           class="block w-full sm:w-auto text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200 cursor-pointer">
                    @if (! empty($archivos[$tipo]))
                        <button wire:click="subir('{{ $tipo }}')" wire:loading.attr="disabled" wire:target="subir, archivos.{{ $tipo }}"
                                class="{{ $btn }} border-blue-600 bg-blue-600 text-white hover:bg-blue-700">
                            {{ $doc?->tieneArchivo() ? 'Reemplazar' : 'Subir' }}
                        </button>
                    @endif
                    <span wire:loading wire:target="archivos.{{ $tipo }}" class="text-xs text-slate-400">Cargando archivo…</span>
                </div>
                @error("archivos.{$tipo}") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                {{-- Historial de versiones --}}
                @if ($historialTipo === $tipo)
                    <div class="mt-3 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Subido</th>
                                    <th class="px-3 py-2 text-left font-semibold">Estado</th>
                                    <th class="px-3 py-2 text-left font-semibold">Observación</th>
                                    <th class="px-3 py-2 text-right font-semibold"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($historial as $v)
                                    <tr wire:key="ver-{{ $v->id }}" class="{{ $v->vigente ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                        <td class="px-3 py-2 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                            {{ $v->subido_at?->format('d/m/Y H:i') ?? '—' }}
                                            <span class="text-slate-400">· {{ $v->subidoPor?->name ?? ($v->origen === 'admision' ? 'Admisión' : ucfirst($v->origen)) }}</span>
                                            @if ($v->vigente) <span class="ml-1 font-semibold text-blue-600 dark:text-blue-400">Actual</span> @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @php $e = $v->tieneArchivo() || $v->estado === 'solicitado' ? $v->estado : 'sin_cargar'; @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badge[$e][1] }}">{{ $badge[$e][0] }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-slate-500 dark:text-slate-400">{{ $v->observacion ?? '—' }}</td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap">
                                            @if ($v->tieneArchivo())
                                                <a href="{{ $v->url() }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Ver</a>
                                                @if ($puedeEliminar)
                                                    <button wire:click="abrirAccion('eliminar', {{ $v->id }})" class="ml-2 text-red-500 hover:underline">Eliminar definitivamente</button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </li>
        @endforeach

        {{-- Otros documentos (varios, cada uno con su nombre) --}}
        <li class="px-6 py-4">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Otros documentos</p>
            @if ($otros->isNotEmpty())
                <ul class="mt-2 space-y-1.5">
                    @foreach ($otros as $o)
                        <li wire:key="otro-{{ $o->id }}" class="flex items-center justify-between gap-2 text-xs">
                            <span class="text-slate-600 dark:text-slate-300 truncate">{{ $o->nombreTipo() }}
                                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badge[$o->estado][1] ?? '' }}">{{ $badge[$o->estado][0] ?? $o->estado }}</span>
                            </span>
                            <span class="flex items-center gap-2 shrink-0">
                                <a href="{{ $o->url() }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">Ver</a>
                                <button wire:click="quitar({{ $o->id }})" wire:confirm="¿Quitar este documento? Queda en el historial." class="text-slate-500 hover:underline">Quitar</button>
                                @if ($puedeEliminar)
                                    <button wire:click="abrirAccion('eliminar', {{ $o->id }})" class="text-red-500 hover:underline">Eliminar definitivamente</button>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-xs text-slate-400 mt-1">Ninguno.</p>
            @endif
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-2 items-center">
                <input type="text" wire:model="descripcionOtro" maxlength="150" placeholder="Qué documento es (ej.: Certificado de votación)"
                       class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-1.5 text-xs text-slate-800 dark:text-slate-100">
                <input type="file" wire:model="archivos.otro" accept="application/pdf,image/jpeg,image/png"
                       class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-slate-200">
                <button wire:click="subir('otro')" wire:loading.attr="disabled" @disabled(empty($archivos['otro']))
                        class="{{ $btn }} border-blue-600 bg-blue-600 text-white hover:bg-blue-700">Agregar</button>
            </div>
            @error('descripcionOtro') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            @error('archivos.otro') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </li>
    </ul>

    {{-- Modal: rechazar / solicitar / eliminar definitivamente --}}
    @if ($accion)
        @php
            $titulo = ['rechazar' => 'Rechazar documento', 'solicitar' => 'Solicitar documento', 'eliminar' => 'Eliminar definitivamente'][$accion];
            $nombreDoc = $accionDoc?->nombreTipo() ?? ($tipos[$accionTipo] ?? 'Documento');
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ $titulo }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $nombreDoc }}</p>
                </div>
                <div class="px-6 py-5 space-y-3 text-sm">
                    @if ($accion === 'eliminar')
                        <p class="text-xs rounded-lg px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300">
                            El archivo se borra del servidor y no se puede recuperar. Úsalo solo si se subió por error (por ejemplo, el documento de otra persona).
                            Queda registrado en Auditoría con tu motivo.
                        </p>
                    @elseif ($accion === 'rechazar')
                        <p class="text-xs text-slate-500 dark:text-slate-400">Explica qué está mal para que lo corrija y lo vuelva a subir.</p>
                    @else
                        <p class="text-xs text-slate-500 dark:text-slate-400">Indica qué necesitas: la persona lo verá como documento solicitado.</p>
                    @endif
                    <textarea wire:model="accionTexto" rows="3" maxlength="500"
                              placeholder="{{ $accion === 'eliminar' ? 'Motivo (ej.: se subió la cédula de otra persona)' : 'Observación' }}"
                              class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-800 dark:text-slate-100"></textarea>
                    @error('accionTexto') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                    <button wire:click="cerrarAccion" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Cancelar</button>
                    <button wire:click="confirmarAccion" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl text-sm font-semibold text-white {{ $accion === 'eliminar' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                        {{ ['rechazar' => 'Rechazar', 'solicitar' => 'Solicitar', 'eliminar' => 'Eliminar para siempre'][$accion] }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
