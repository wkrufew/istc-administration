<div>
    {{-- ── HEADER ────────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Documentación del Personal
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Consulta el estado de los expedientes del personal. Para cargar o editar, ve al listado de docentes.
                </p>
            </div>

            <a href="{{ route('administracion.administrativa.docentes.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-950 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-200
                       hover:bg-gray-50 dark:hover:bg-gray-800 transition shrink-0">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Volver a Docentes
            </a>
        </div>

        {{-- Buscador --}}
        <div class="mt-5 relative max-w-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 4.02 12.17l3.78 3.78a.75.75 0 1 0 1.06-1.06l-3.78-3.78A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z" clip-rule="evenodd" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="search"
                placeholder="Buscar por nombre, cédula o correo..."
                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950
                       text-gray-900 dark:text-gray-100 pl-9 pr-4 py-2.5 text-sm
                       placeholder:text-gray-400 dark:placeholder:text-gray-500
                       focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500 transition" />
        </div>
    </div>

    {{-- ── TABLA ─────────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">

        @if ($documentos->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Docente / Personal
                            </th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Archivos
                            </th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">
                                Cargado
                            </th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($documentos as $doc)
                            @php
                                $count = $this->countFiles($doc);
                                $files = [
                                    'file_curriculum' => ['label' => 'CV',  'full' => 'Curriculum'],
                                    'file_senescyt'   => ['label' => 'SEN', 'full' => 'Senescyt'],
                                    'file_cedula'     => ['label' => 'CED', 'full' => 'Cédula'],
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition group">

                                {{-- Docente --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 rounded-xl bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                                            <span class="text-blue-700 dark:text-blue-300 font-bold text-sm">
                                                {{ strtoupper(substr($doc->user->name ?? 'NA', 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                                                {{ $doc->user->name ?? 'Sin usuario' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $doc->user->email ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Archivos --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        {{-- Contador --}}
                                        <span @class([
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold',
                                            'bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-300' => $count === 3,
                                            'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300'  => $count > 0 && $count < 3,
                                            'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500'      => $count === 0,
                                        ])>
                                            {{ $count }} / 3
                                        </span>

                                        {{-- Links de archivo --}}
                                        <div class="flex flex-wrap justify-center gap-1">
                                            @foreach ($files as $field => $meta)
                                                @if ($doc->$field)
                                                    <a href="{{ asset('storage/' . $doc->$field) }}" target="_blank"
                                                        title="{{ $meta['full'] }}"
                                                        class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold
                                                               bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-300
                                                               hover:bg-green-200 dark:hover:bg-green-900 transition">
                                                        <svg class="w-2.5 h-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                                                            <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                                                        </svg>
                                                        {{ $meta['label'] }}
                                                    </a>
                                                @else
                                                    <span title="{{ $meta['full'] }}"
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                                               bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600">
                                                        {{ $meta['label'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </td>

                                {{-- Fecha --}}
                                <td class="px-5 py-4 text-center hidden md:table-cell">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $doc->created_at->isoFormat('D MMM YYYY') }}
                                    </span>
                                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">
                                        {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">
                                        {{-- Eliminar --}}
                                        <button
                                            x-data
                                            @click="Swal.fire({
                                                title: '¿Eliminar documentos?',
                                                html: 'Se eliminarán todos los archivos de <strong>{{ addslashes($doc->user->name ?? 'este docente') }}</strong>. Esta acción no se puede deshacer.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: 'Sí, eliminar',
                                                cancelButtonText: 'Cancelar',
                                            }).then(r => r.isConfirmed && $wire.deleteDocument({{ $doc->id }}))"
                                            title="Eliminar"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-red-200 dark:border-red-900
                                                   bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950
                                                   text-red-600 dark:text-red-400 transition">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($documentos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $documentos->links() }}
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="h-16 w-16 rounded-2xl bg-blue-100 dark:bg-blue-950 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75-6.75a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                        <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                    @if($search)
                        Sin resultados para "{{ $search }}"
                    @else
                        No hay documentos registrados
                    @endif
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-xs">
                    @if($search)
                        Intenta con otro nombre, cédula o correo electrónico.
                    @else
                        Aún no hay expedientes. Cárgalos desde el listado de docentes.
                    @endif
                </p>
            </div>
        @endif
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('toast', ({ message, type }) => {
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
                    Toast.fire({ icon: type, title: message });
                });
            });
        </script>
    @endpush
</div>
