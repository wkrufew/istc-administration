<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Card principal --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="p-5 sm:p-6 border-b border-gray-200 dark:border-gray-800">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
                            Documentación Personal
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Gestión de archivos cargados por docentes (curriculum, senescyt, contrato, etc).
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Buscador --}}
                        <div class="relative w-full sm:w-[380px]">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10.5 3.75a6.75 6.75 0 1 0 4.02 12.17l3.78 3.78a.75.75 0 1 0 1.06-1.06l-3.78-3.78A6.75 6.75 0 0 0 10.5 3.75Zm-5.25 6.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <input wire:model.live="search" type="search"
                                placeholder="Buscar por nombre o correo del docente..."
                                class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" />
                        </div>

                        {{-- Botón volver --}}
                        <a href="{{ route('administracion.administrativa.docentes.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            {{-- Notificación --}}
            @if (session('notificacion'))
                <div class="px-5 sm:px-6 pt-5">
                    <div x-data="{ open: true }" x-show="open"
                        class="flex items-start justify-between gap-3 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950/40 p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53-1.28-1.28a.75.75 0 1 0-1.06 1.06l1.92 1.92c.3.3.79.28 1.06-.1l3.816-5.258Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">
                                    ¡Acción completada!
                                </p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    {{ session('notificacion') }}
                                </p>
                            </div>
                        </div>

                        <button type="button" x-on:click="open = false"
                            class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-200 transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Tabla --}}
            <div class="p-5 sm:p-6">
                @if ($documentacion_personals->count())
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-950">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Docente
                                    </th>

                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Archivos
                                    </th>

                                    <th
                                        class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-300">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach ($documentacion_personals as $documentacion_personal)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">
                                        {{-- Docente --}}
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                {{-- Avatar --}}
                                                <div
                                                    class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                                                    <span class="text-blue-700 dark:text-blue-300 font-bold text-sm">
                                                        {{ strtoupper(substr($documentacion_personal->user->name ?? 'NA', 0, 2)) }}
                                                    </span>
                                                </div>

                                                {{-- Nombre + correo --}}
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $documentacion_personal->user->name ?? 'Sin usuario' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $documentacion_personal->user->email ?? '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Archivos --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <span
                                                    class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200">
                                                    {{ $this->countFiles($documentacion_personal) }}
                                                </span>

                                                <div class="flex flex-wrap justify-center gap-1">
                                                    @php
                                                        $badges = [
                                                            'file_curriculum' => 'Curriculum',
                                                            'file_senescyt' => 'Senescyt',
                                                            'file_contrato' => 'Contrato',
                                                            'file_otro' => 'Otro',
                                                        ];
                                                    @endphp

                                                    @foreach ($badges as $field => $label)
                                                        @if ($documentacion_personal->$field)
                                                            <span
                                                                class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-[11px] font-semibold bg-green-100 dark:bg-green-950 text-green-800 dark:text-green-200">
                                                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.333a1 1 0 0 1-1.425.006L3.29 9.704A1 1 0 1 1 4.704 8.29l3.01 3.01 6.54-6.61a1 1 0 0 1 1.45.6Z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                {{ $label }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Fecha --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ $documentacion_personal->created_at->isoFormat('D [de] MMMM [de] Y') }}
                                            </span>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center gap-2">
                                                {{-- Editar --}}
                                                <a href="{{ route('administracion.administrativa.documentacion-personal.edit', $documentacion_personal) }}"
                                                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                                                    title="Editar">
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor">
                                                        <path
                                                            d="M21.7 5.3a1 1 0 0 0 0-1.4l-1.6-1.6a1 1 0 0 0-1.4 0l-1.7 1.7 3 3 1.7-1.7ZM3 17.3V21h3.7l10.9-10.9-3-3L3 17.3Z" />
                                                    </svg>
                                                </a>

                                                {{-- Eliminar --}}
                                                <button wire:click="deleteDocument({{ $documentacion_personal->id }})"
                                                    wire:confirm="¿Está seguro que desea eliminar todos los documentos de este docente?"
                                                    class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950 transition"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4 text-red-600 dark:text-red-300"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor">
                                                        <path
                                                            d="M9 3a1 1 0 0 0-1 1v1H4a1 1 0 1 0 0 2h1v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7h1a1 1 0 1 0 0-2h-4V4a1 1 0 0 0-1-1H9Zm1 4h4v13h-4V7Z" />
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
                    @if ($documentacion_personals->hasPages())
                        <div class="pt-4">
                            {{ $documentacion_personals->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty state --}}
                    <div
                        class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center bg-gray-50 dark:bg-gray-950">
                        <div
                            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-950">
                            <svg class="w-6 h-6 text-blue-700 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M7 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-6-6H7Zm7 1.5L19.5 9H14V3.5Z" />
                            </svg>
                        </div>

                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                            No hay resultados
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            No se encuentran documentos con ese criterio de búsqueda.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Alerts --}}
        @push('js')
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('alert', (eventData) => {
                        const data = eventData[0];

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });

                        Toast.fire({
                            icon: data.type,
                            title: data.message,
                        });
                    });
                });
            </script>
        @endpush
    </div>
</div>
