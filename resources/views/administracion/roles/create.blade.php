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
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-lime-600 to-green-800 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-white/90 leading-none">Crear Rol</h1>
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

            <form action="{{ route('administracion.administrativa.roles.store') }}" method="POST">
                @csrf

                {{-- ── Nombre del Rol ── --}}
                <div class="px-6 py-6 sm:px-8">
                    <label for="name"
                        class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-400/70 mb-2">
                        Nombre del Rol
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="Ej: Administrador, Editor, Viewer…"
                        class="w-full px-4 py-3 rounded-xl text-sm text-white/80
                               bg-slate-800 border border-white/[0.08] placeholder-slate-500
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
                            {{ $permissions->flatten()->count() }} disponibles
                        </span>
                    </div>

                    @error('permissions')
                        <div
                            class="mb-4 px-4 py-2.5 rounded-lg bg-red-500/[0.08] border border-red-500/20 text-xs text-red-400">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror

                    {{-- Permisos agrupados por categoría --}}
                    @php
                        $catColors = [
                            ['border-slate-500/40',   'text-slate-400'],
                            ['border-violet-500/40',  'text-violet-400'],
                            ['border-amber-500/40',   'text-amber-400'],
                            ['border-sky-500/40',     'text-sky-400'],
                            ['border-emerald-500/40', 'text-emerald-400'],
                            ['border-lime-500/40',    'text-lime-400'],
                            ['border-orange-500/40',  'text-orange-400'],
                            ['border-rose-500/40',    'text-rose-400'],
                            ['border-cyan-500/40',    'text-cyan-400'],
                            ['border-indigo-500/40',  'text-indigo-400'],
                            ['border-teal-500/40',    'text-teal-400'],
                        ];
                        $ci = 0;
                    @endphp

                    <div class="space-y-4">
                        @foreach ($permissions as $categoria => $grupo)
                            @php
                                [$borderCls, $textCls] = $catColors[$ci % count($catColors)];
                                $ci++;
                            @endphp
                            <div data-cat="{{ Str::slug($categoria ?: 'sin-categoria') }}"
                                class="rounded-xl border border-white/[0.05] overflow-hidden">

                                {{-- Cabecera de categoría --}}
                                <div class="flex items-center justify-between gap-2 px-4 py-2.5 bg-slate-800/70 border-l-2 {{ $borderCls }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[0.62rem] font-semibold tracking-[0.2em] uppercase {{ $textCls }}">
                                            {{ $categoria ?: 'Sin categoría' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[0.58rem] bg-white/5 text-slate-500">
                                            {{ $grupo->count() }}
                                        </span>
                                    </div>
                                    <button type="button" onclick="toggleCategoria(this)"
                                        class="text-[0.6rem] text-slate-500 hover:{{ $textCls }} transition-colors duration-150 select-none">
                                        Sel. todos
                                    </button>
                                </div>

                                {{-- Grid de permisos --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1.5 p-3">
                                    @foreach ($grupo as $permission)
                                        <label for="perm_{{ $permission->id }}"
                                            class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg cursor-pointer border transition-all duration-150 select-none
                                                   bg-slate-800 border-white/[0.05] hover:bg-slate-700/60 hover:border-white/10">

                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                id="perm_{{ $permission->id }}"
                                                onchange="togglePermisoCreate(this)"
                                                class="w-4 h-4 rounded border-slate-500 bg-transparent text-lime-500 accent-lime-500 cursor-pointer flex-shrink-0 focus:ring-lime-500/20 focus:ring-offset-0">

                                            <span class="text-[0.78rem] leading-tight text-slate-400 transition-colors duration-150">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                            </div>
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
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        Crear Rol
                    </button>

                </div>

            </form>

            {{-- Franja de colores bottom --}}
            <div
                class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50">
            </div>

        </div>

    </div>

@push('js')
<script>
    function togglePermisoCreate(checkbox) {
        const label = checkbox.closest('label');
        if (checkbox.checked) {
            label.classList.remove('bg-slate-800', 'border-white/[0.05]');
            label.classList.add('bg-lime-500/[0.08]', 'border-lime-500/25');
            label.querySelector('span').classList.replace('text-slate-400', 'text-white/85');
        } else {
            label.classList.remove('bg-lime-500/[0.08]', 'border-lime-500/25');
            label.classList.add('bg-slate-800', 'border-white/[0.05]');
            label.querySelector('span').classList.replace('text-white/85', 'text-slate-400');
        }
    }

    function toggleCategoria(btn) {
        const section   = btn.closest('[data-cat]');
        const checkboxes = [...section.querySelectorAll('input[type="checkbox"]')];
        const allChecked = checkboxes.every(c => c.checked);
        checkboxes.forEach(c => {
            c.checked = !allChecked;
            c.dispatchEvent(new Event('change'));
        });
        btn.textContent = allChecked ? 'Sel. todos' : 'Quitar todos';
    }
</script>
@endpush

</x-admin-layout>
{{-- <x-admin-layout>
    <div class="p-4 max-w-5xl mx-auto m-4 border-2 border-gray-50 rounded-lg">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Crear Rol</h1>
            <a href="{{ route('administracion.administrativa.roles.index') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white dark:bg-gray-600 font-bold text-sm py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">Volver</a>
        </div>
        <div class="mt-4 p-4">
            <form action="{{ route('administracion.administrativa.roles.store') }}" method="POST" class="">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-800 dark:text-gray-100">Nombre de
                        Rol:</label>
                    <input type="text" name="name" id="name"
                        class="mt-1 w-full dark:bg-gray-700 dark:text-gray-100 block rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50{{ $errors->has('name') ? ' border-red-500' : '' }}"
                        placeholder="Escriba un nombre" value="{{ old('name') }}">
                    @error('name')
                        <span class="text-sm text-red-500 mt-1">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <strong
                        class="block text-sm font-semibold text-gray-800 dark:text-gray-100 underline mb-4">PERMISOS</strong>
                    @error('permissions')
                        <br>
                        <small class="text-red-500">
                            <strong>{{ $message }}</strong>s
                        </small>
                        <br>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($permissions->chunk(2) as $chunk)
                            <div class="space-y-2">
                                @foreach ($chunk as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="mr-2 rounded border-gray-300 text-lime-600 shadow-sm focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50">
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
                        class="mt-2 px-4 py-2 bg-lime-600 text-white rounded-full hover:bg-lime-700 focus:outline-none focus:bg-lime-700 dark:bg-gray-600 dark:text-gray-100 text-sm">Crear
                        Rol</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout> --}}
