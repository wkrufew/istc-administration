<div class="w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Normas Aprobadas
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Administra documentos, estado de publicación y acciones.
                </p>
            </div>

            <a href="{{ route('administracion.administrativa.normas-aprobadas.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 11h8v2h-8v8h-2v-8H3v-2h8V3h2v8z" />
                </svg>
                Nuevo Documento
            </a>
        </div>

        {{-- Notificación --}}
        @if (session('notificacion'))
            <div x-data="{ open: true }" x-show="open"
                class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-800 shadow-sm
                       dark:border-blue-800/50 dark:bg-blue-950/40 dark:text-blue-200">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-bold">¡Listo!</p>
                        <p class="text-sm">{{ session('notificacion') }}</p>
                    </div>

                    <button x-on:click="open=false"
                        class="rounded-lg p-1 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 8.586L4.293 2.879A1 1 0 102.879 4.293L8.586 10l-5.707 5.707a1 1 0 101.414 1.414L10 11.414l5.707 5.707a1 1 0 001.414-1.414L11.414 10l5.707-5.707a1 1 0 00-1.414-1.414L10 8.586z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Search --}}
        <div class="mb-5">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M10 2a8 8 0 105.293 14.293l4.707 4.707 1.414-1.414-4.707-4.707A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z" />
                    </svg>
                </span>

                <input wire:model.live="search" type="search" placeholder="Escriba el título del documento..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-white text-gray-900 shadow-sm
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
                           dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 dark:focus:ring-blue-400" />
            </div>
        </div>

        {{-- Tabla / Card --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden
                    dark:bg-gray-900 dark:border-gray-700">

            @if ($documents->count())

                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Título
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Creación
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($documents as $document)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                    {{-- Título --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-600
                                                        dark:bg-gray-800 dark:text-gray-300">
                                                @if ($document->file)
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm1 7V3.5L19.5 9H15z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M4 4h16v2H4V4zm0 6h16v2H4v-2zm0 6h10v2H4v-2z" />
                                                    </svg>
                                                @endif
                                            </div>

                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $document->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    ID: {{ $document->id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center gap-2">

                                            @if ($document->is_active)
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700
                                                             dark:bg-green-900/40 dark:text-green-300">
                                                    Publicado
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700
                                                             dark:bg-red-900/40 dark:text-red-300">
                                                    Borrador
                                                </span>
                                            @endif

                                            {{-- Switch --}}
                                            <label class="relative inline-flex cursor-pointer items-center">
                                                <input type="checkbox" class="peer sr-only"
                                                    @if ($document->is_active == 1) checked @endif
                                                    wire:click="toggleStatusc({{ $document->id }})" />

                                                <div
                                                    class="peer h-6 w-11 rounded-full bg-gray-200
                                                           after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-['']
                                                           peer-checked:bg-green-600 peer-checked:after:translate-x-full
                                                           dark:bg-gray-700 dark:after:bg-gray-100 dark:peer-checked:bg-green-500">
                                                </div>
                                            </label>
                                        </div>
                                    </td>

                                    {{-- Fecha --}}
                                    <td
                                        class="px-6 py-4 text-center text-sm font-medium text-gray-700 dark:text-gray-200">
                                        {{ $document->created_at->isoFormat('D [de] MMMM [de] Y') }}
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">

                                            <a href="{{ route('administracion.administrativa.normas-aprobadas.edit', $document) }}"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white hover:bg-gray-50
                                                       dark:bg-gray-900 dark:border-gray-700 dark:hover:bg-gray-800 transition">
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400"
                                                    fill="currentColor" viewBox="0 0 512 512">
                                                    <path
                                                        d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zM172.4 241.7c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7z" />
                                                </svg>
                                            </a>

                                            <button wire:click="deleteDocumento({{ $document->id }})"
                                                wire:confirm="Esta seguro que desea eliminar este documento?"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white hover:bg-red-50
                                                       dark:bg-gray-900 dark:border-gray-700 dark:hover:bg-red-900/20 transition">
                                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor"
                                                    viewBox="0 0 448 512">
                                                    <path
                                                        d="M135.168 64h177.664c19.393 0 35.136 13.208 38.368 32h38.624c13.255 0 24 10.745 24 24s-10.745 24-24 24H25.376c-13.255 0-24-10.745-24-24s10.745-24 24-24h38.624C100.032 77.208 115.775 64 135.168 64zM37.376 160h373.248l-21.824 341.312C386.768 513.264 361.616 536 331.36 536H116.64c-30.256 0-55.408-22.736-57.44-54.688L37.376 160z" />
                                                </svg>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($documents as $document)
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $document->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $document->created_at->isoFormat('D [de] MMMM [de] Y') }}
                                    </p>
                                </div>

                                @if ($document->is_active)
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700
                                                 dark:bg-green-900/40 dark:text-green-300">
                                        Publicado
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700
                                                 dark:bg-red-900/40 dark:text-red-300">
                                        Borrador
                                    </span>
                                @endif
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" class="peer sr-only"
                                        @if ($document->is_active == 1) checked @endif
                                        wire:click="toggleStatusc({{ $document->id }})" />
                                    <div
                                        class="peer h-6 w-11 rounded-full bg-gray-200
                                               after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-['']
                                               peer-checked:bg-green-600 peer-checked:after:translate-x-full
                                               dark:bg-gray-700 dark:after:bg-gray-100 dark:peer-checked:bg-green-500">
                                    </div>
                                </label>

                                <div class="flex gap-2">
                                    <a href="{{ route('administracion.administrativa.normas-aprobadas.edit', $document) }}"
                                        class="px-3 py-2 rounded-xl bg-gray-100 text-gray-800 font-semibold text-sm
                                               dark:bg-gray-800 dark:text-gray-100">
                                        Editar
                                    </a>

                                    <button wire:click="deleteDocumento({{ $document->id }})"
                                        wire:confirm="Esta seguro que desea eliminar este documento?"
                                        class="px-3 py-2 rounded-xl bg-red-600 text-white font-semibold text-sm">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if ($documents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 font-medium">
                        No se encuentran registros con ese nombre.
                    </p>
                </div>
            @endif
        </div>

    </div>

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
