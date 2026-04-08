<x-admin-layout>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        {{-- ═══════════════════════════════════════
             BLOQUE 1 — HEADER (slate-900)
        ═══════════════════════════════════════ --}}
        <div class="bg-slate-900 border border-slate-700/50 relative overflow-hidden rounded-xl">

            {{-- Borde izquierdo acento --}}
            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>

            {{-- Línea de colores inferior --}}
            <div
                class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50">
            </div>

            <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

                {{-- Ícono + Título --}}
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-white/90 leading-none">Editar Rol</h1>
                        <p class="text-xs text-lime-400/70 tracking-widest uppercase mt-1">Administración · Roles</p>
                    </div>
                </div>

                {{-- Botón volver --}}
                <a href="{{ route('administracion.administrativa.roles.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-white/10 bg-white/5 text-slate-300 text-xs font-medium tracking-wide hover:bg-white/10 hover:text-white hover:-translate-x-0.5 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>

            </div>
        </div>


        {{-- ═══════════════════════════════════════
             BLOQUE 2 — CARD FORMULARIO (slate-900)
        ═══════════════════════════════════════ --}}
        <div
            class="bg-slate-900 rounded-2xl border border-white/[0.06] overflow-hidden shadow-2xl shadow-black/40 ring-1 ring-inset ring-white/[0.04]">

            {{-- Shimmer top --}}
            <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

            <form action="{{ route('administracion.administrativa.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ── Nombre del Rol ── --}}
                <div class="px-6 py-6 sm:px-8">
                    <label for="name"
                        class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-2">
                        Nombre del Rol
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        placeholder="Ej: Administrador, Editor, Viewer…"
                        class="w-full px-4 py-3 rounded-xl text-sm text-white/80
                               bg-slate-800 border border-white/[0.08] placeholder-slate-200
                               focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                               transition-all duration-200
                               {{ $errors->has('name') ? 'border-red-500/50' : '' }}">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400"><strong>{{ $message }}</strong></p>
                    @enderror
                </div>

                {{-- Separador --}}
                <div class="mx-6 sm:mx-8 h-px bg-white/[0.05]"></div>

                {{-- ── Permisos ── --}}
                <div class="px-6 py-6 sm:px-8">

                    {{-- Header permisos --}}
                    <div class="flex items-center gap-3 mb-5">
                        <span
                            class="text-[0.65rem] font-medium tracking-[0.18em] uppercase text-slate-400 whitespace-nowrap">
                            Permisos
                        </span>
                        <div class="flex-1 h-px bg-white/[0.05]"></div>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[0.62rem] tracking-wide bg-lime-500/10 border border-lime-500/20 text-lime-400">
                            {{ $permissions->count() }} disponibles
                        </span>
                    </div>

                    @error('permissions')
                        <div
                            class="mb-4 px-4 py-2.5 rounded-lg bg-red-500/[0.08] border border-red-500/20 text-xs text-red-400">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror

                    {{-- Grid de permisos --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($permissions as $permission)
                            @php
                                $isChecked = in_array(
                                    $permission->id,
                                    old('permissions', $role->permissions->pluck('id')->toArray()),
                                );
                            @endphp
                            <label for="perm_{{ $permission->id }}"
                                class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg cursor-pointer border transition-all duration-150 select-none
                                       {{ $isChecked
                                           ? 'bg-lime-500/[0.08] border-lime-500/25'
                                           : 'bg-slate-800 border-white/[0.05] hover:bg-slate-700/60 hover:border-white/10' }}">

                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    id="perm_{{ $permission->id }}" {{ $isChecked ? 'checked' : '' }}
                                    onchange="
                                        const row = this.closest('label');
                                        row.classList.toggle('bg-lime-500/[0.08]', this.checked);
                                        row.classList.toggle('border-lime-500/25', this.checked);
                                        row.classList.toggle('bg-slate-800', !this.checked);
                                        row.classList.toggle('border-white/[0.05]', !this.checked);
                                    "
                                    class="w-4 h-4 rounded border-slate-500 bg-transparent text-lime-500 accent-lime-500 cursor-pointer flex-shrink-0 focus:ring-lime-500/20 focus:ring-offset-0">

                                <span
                                    class="text-[0.78rem] leading-tight transition-colors duration-150
                                             {{ $isChecked ? 'text-white/85' : 'text-slate-400' }}">
                                    {{ $permission->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                </div>

                {{-- ── Footer acciones ── --}}
                <div
                    class="px-6 py-4 sm:px-8 bg-black/15 border-t border-white/[0.05] flex items-center justify-end gap-3 flex-wrap">

                    <a href="{{ route('administracion.administrativa.roles.index') }}"
                        class="px-5 py-2 rounded-full text-xs font-medium tracking-wide text-slate-400 border border-white/[0.08] hover:bg-white/5 hover:text-slate-200 transition-all duration-200">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-6 py-2 rounded-full text-xs font-medium tracking-widest uppercase text-white/90
                               bg-gradient-to-r from-green-800/70 via-sky-800/60 to-purple-900/55
                               border border-lime-500/25
                               hover:from-green-700/80 hover:via-sky-700/70 hover:to-purple-800/65
                               hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-900/30
                               active:translate-y-0 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        Actualizar Rol
                    </button>

                </div>

            </form>

            {{-- Franja de colores bottom --}}
            <div
                class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50">
            </div>

        </div>

    </div>

</x-admin-layout>
{{-- <x-admin-layout>
    <div class="p-4 max-w-5xl mx-auto m-4 border-2 border-gray-50 rounded-lg">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Editar Rol</h1>
            <a href="{{ route('administracion.administrativa.roles.index') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white dark:bg-gray-600 font-bold text-sm py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Volver</a>
        </div>
        <div class="mt-4 p-4">
            <form action="{{ route('administracion.administrativa.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-800 dark:text-gray-100">Nombre de
                        Rol:</label>
                    <input type="text" name="name" id="name"
                        class="mt-1 block w-full rounded-md shadow-sm dark:bg-gray-600 dark:text-gray-100 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50{{ $errors->has('name') ? ' border-red-500' : '' }}"
                        placeholder="Escriba un nombre" value="{{ old('name', $role->name) }}">
                    @error('name')
                        <span class="text-sm text-red-500 mt-1">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <strong
                        class="block text-sm font-semibol text-gray-800 dark:text-gray-100 underline mb-4">PERMISOS</strong>
                    @error('permissions')
                        <br>
                        <small class="text-red-500">
                            <strong>{{ $message }}</strong>
                        </small>
                        <br>
                    @enderror
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($permissions->chunk(2) as $chunk)
                            <div class="space-y-2">
                                @foreach ($chunk as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="mr-2 rounded border-gray-300 text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50"
                                            {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label
                                            class="text-sm text-gray-800 dark:text-gray-100">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit"
                        class=" mt-2 px-4 py-2 bg-lime-600 text-white rounded-full hover:bg-lime-700 focus:outline-none focus:bg-lime-700 text-sm font-semibold">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout> --}}
