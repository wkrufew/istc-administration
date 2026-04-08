<div class="w-full">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Card principal --}}
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-md overflow-hidden">

            {{-- Header --}}
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-gray-200 dark:border-gray-700">

                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        Actas del Consejo
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Administra, publica y edita las actas registradas.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

                    {{-- Search --}}
                    <div class="relative w-full sm:w-[360px]">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            🔎
                        </span>
                        <input wire:model.live="search" type="search"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Buscar por título del acta..." />
                    </div>

                    {{-- Button --}}
                    <a href="{{ route('administracion.administrativa.actas-colegiado.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-sm w-full sm:w-auto">
                        <span>➕</span>
                        Nueva Acta
                    </a>
                </div>
            </div>

            {{-- Notificación --}}
            @if (session('notificacion'))
                <div class="px-6 pt-5">
                    <div x-data="{ open: true }" x-show="open"
                        class="flex items-start justify-between gap-4 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/30 px-4 py-3 text-blue-700 dark:text-blue-200">
                        <div>
                            <p class="font-semibold">¡Listo!</p>
                            <p class="text-sm opacity-90">{{ session('notificacion') }}</p>
                        </div>
                        <button x-on:click="open = false"
                            class="text-blue-700 dark:text-blue-200 hover:opacity-70 transition">
                            ✕
                        </button>
                    </div>
                </div>
            @endif

            {{-- Tabla / Lista --}}
            <div class="px-6 py-5">

                @if ($documents->count())

                    {{-- Tabla desktop --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Documento
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Creación
                                    </th>
                                    <th class="px-4 py-3 text-right">
                                        <span class="sr-only">Opciones</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @foreach ($documents as $document)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition">

                                        {{-- Título --}}
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">

                                                {{-- icono --}}
                                                <div
                                                    class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                    @isset($document->file)
                                                        <svg class="w-5 h-5 fill-green-600 dark:fill-green-400"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                            <path
                                                                d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 144-208 0c-35.3 0-64 28.7-64 64l0 144-48 0c-35.3 0-64-28.7-64-64L0 64zm384 64l-128 0L256 0 384 128zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z" />
                                                        </svg>
                                                    @else
                                                        <span class="text-gray-400">📄</span>
                                                    @endisset
                                                </div>

                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                        {{ $document->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        ID: {{ $document->id }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Estado --}}
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex flex-col items-center gap-2">

                                                @if ($document->is_active)
                                                    <span
                                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-900">
                                                        Publicado
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-900">
                                                        Borrador
                                                    </span>
                                                @endif

                                                {{-- switch --}}
                                                <label class="relative inline-flex cursor-pointer items-center">
                                                    <input type="checkbox" class="peer sr-only"
                                                        @if ($document->is_active == 1) checked @endif
                                                        wire:click="toggleStatusc({{ $document->id }})" />

                                                    <div
                                                        class="peer h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full">
                                                    </div>
                                                </label>
                                            </div>
                                        </td>

                                        {{-- Fecha --}}
                                        <td class="px-4 py-4 text-center text-sm text-gray-700 dark:text-gray-200">
                                            {{ $document->created_at->isoFormat('D [de] MMMM [de] Y') }}
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="px-4 py-4">
                                            <div class="flex justify-end items-center gap-3">

                                                <a href="{{ route('administracion.administrativa.actas-colegiado.edit', $document) }}"
                                                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-green-50 dark:hover:bg-green-900/30 transition">
                                                    <svg class="w-5 h-5 fill-green-600 dark:fill-green-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path
                                                            d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                                                    </svg>
                                                </a>

                                                <button wire:click="deleteDocumento({{ $document->id }})"
                                                    wire:confirm="Esta seguro que desea eliminar este documento?"
                                                    class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                                    <svg class="w-5 h-5 fill-red-600 dark:fill-red-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path
                                                            d="M135.168 64h177.664c19.393 0 35.136 13.208 38.368 32h38.624c13.255 0 24 10.745 24 24s-10.745 24-24 24H25.376c-13.255 0-24-10.745-24-24s10.745-24 24-24h38.624C100.032 77.208 115.775 64 135.168 64zM37.376 160h373.248l-21.824 341.312C386.768 513.264 361.616 536 331.36 536H116.64c-30.256 0-55.408-22.736-57.44-54.688L37.376 160zm116.8 24c-13.255 0-24 10.745-24 24v208c0 13.255 10.745 24 24 24s24-10.745 24-24V208c0-13.255-10.745-24-24-24zm80 0c-13.255 0-24 10.745-24 24v208c0 13.255 10.745 24 24 24s24-10.745 24-24V208c0-13.255-10.745-24-24-24zm80 0c-13.255 0-24 10.745-24 24v208c0 13.255 10.745 24 24 24s24-10.745 24-24V208c0-13.255-10.745-24-24-24z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Vista móvil (cards) --}}
                    <div class="md:hidden space-y-3">
                        @foreach ($documents as $document)
                            <div
                                class="p-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">

                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $document->title }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $document->created_at->isoFormat('D [de] MMMM [de] Y') }}
                                        </p>
                                    </div>

                                    @if ($document->is_active)
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                            Publicado
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                            Borrador
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <label
                                        class="inline-flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                        <span>Estado</span>
                                        <input type="checkbox" class="peer sr-only"
                                            @if ($document->is_active == 1) checked @endif
                                            wire:click="toggleStatusc({{ $document->id }})" />
                                        <div
                                            class="peer h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full">
                                        </div>
                                    </label>

                                    <div class="flex gap-2">
                                        <a href="{{ route('administracion.administrativa.actas-colegiado.edit', $document) }}"
                                            class="px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm font-semibold text-green-600 dark:text-green-400">
                                            Editar
                                        </a>
                                        <button wire:click="deleteDocumento({{ $document->id }})"
                                            wire:confirm="Esta seguro que desea eliminar este documento?"
                                            class="px-3 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm font-semibold text-red-600 dark:text-red-400">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- paginación --}}
                    @if ($documents->hasPages())
                        <div class="mt-6">
                            {{ $documents->links() }}
                        </div>
                    @endif
                @else
                    {{-- Empty state --}}
                    <div
                        class="flex flex-col items-center justify-center text-center py-14 px-6 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                        <div class="text-4xl mb-3">📄</div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                            No se encontraron actas
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Prueba con otro título o crea una nueva acta.
                        </p>

                        <a href="{{ route('administracion.administrativa.actas-colegiado.create') }}"
                            class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                            ➕ Nueva Acta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SweetAlert Livewire --}}
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
