<x-admin-layout>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

        {{-- ═══════════════════════════════════════
             HEADER
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 relative overflow-hidden rounded-xl">

            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-lime-500 to-sky-600 opacity-70"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-green-700 via-lime-500 to-sky-600 opacity-50"></div>

            <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-700 to-sky-700 flex items-center justify-center shadow-lg shadow-green-900/40 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800 dark:text-white/90 leading-none">Editar Rol</h1>
                        <p class="text-xs text-lime-600 dark:text-lime-400/70 tracking-widest uppercase mt-1">Administración · Roles</p>
                    </div>
                </div>

                <a href="{{ route('administracion.administrativa.roles.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full
                           border border-slate-200 dark:border-white/10
                           bg-slate-100 dark:bg-white/5
                           text-slate-600 dark:text-slate-300 text-xs font-medium tracking-wide
                           hover:bg-slate-200 dark:hover:bg-white/10
                           hover:text-slate-800 dark:hover:text-white
                           hover:-translate-x-0.5 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
             CARD FORMULARIO
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-white/[0.06] overflow-hidden shadow-sm dark:shadow-2xl dark:shadow-black/40 ring-1 ring-inset ring-slate-900/[0.04] dark:ring-white/[0.04]">

            <div class="h-px bg-gradient-to-r from-transparent via-lime-500/30 to-transparent"></div>

            <form action="{{ route('administracion.administrativa.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ── Nombre del Rol ── --}}
                <div class="px-6 py-6 sm:px-8">
                    <label for="name"
                        class="block text-[0.65rem] font-medium tracking-[0.15em] uppercase text-lime-600 dark:text-lime-400/70 mb-2">
                        Nombre del Rol
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        placeholder="Ej: Administrador, Editor, Viewer…"
                        class="w-full px-4 py-3 rounded-xl text-sm
                               text-slate-800 dark:text-white/80
                               bg-white dark:bg-slate-800
                               border border-slate-200 dark:border-white/[0.08]
                               placeholder-slate-400 dark:placeholder-slate-500
                               focus:outline-none focus:border-lime-500/50 focus:ring-2 focus:ring-lime-500/10
                               transition-all duration-200
                               {{ $errors->has('name') ? 'border-red-500/50' : '' }}">
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500 dark:text-red-400"><strong>{{ $message }}</strong></p>
                    @enderror
                </div>

                {{-- Separador --}}
                <div class="mx-6 sm:mx-8 h-px bg-slate-200 dark:bg-white/[0.05]"></div>

                {{-- ── Permisos ── --}}
                <div class="px-6 py-6 sm:px-8">

                    <div class="flex items-center gap-3 mb-5">
                        <span class="text-[0.65rem] font-medium tracking-[0.18em] uppercase text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            Permisos
                        </span>
                        <div class="flex-1 h-px bg-slate-200 dark:bg-white/[0.05]"></div>
                        <span class="px-2.5 py-0.5 rounded-full text-[0.62rem] tracking-wide bg-lime-500/10 border border-lime-500/20 text-lime-600 dark:text-lime-400">
                            {{ $permissions->count() }} disponibles
                        </span>
                    </div>

                    @error('permissions')
                        <div class="mb-4 px-4 py-2.5 rounded-lg bg-red-500/[0.08] border border-red-500/20 text-xs text-red-500 dark:text-red-400">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror

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
                                           : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-white/[0.05] hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:border-slate-300 dark:hover:border-white/10' }}">

                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    id="perm_{{ $permission->id }}" {{ $isChecked ? 'checked' : '' }}
                                    onchange="togglePermiso(this)"
                                    class="w-4 h-4 rounded border-slate-400 dark:border-slate-500 bg-transparent text-lime-500 accent-lime-500 cursor-pointer flex-shrink-0 focus:ring-lime-500/20 focus:ring-offset-0">

                                <span class="text-[0.78rem] leading-tight transition-colors duration-150
                                             {{ $isChecked ? 'text-slate-800 dark:text-white/85' : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ $permission->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                </div>

                {{-- ── Footer acciones ── --}}
                <div class="px-6 py-4 sm:px-8 bg-slate-50 dark:bg-black/15 border-t border-slate-200 dark:border-white/[0.05] flex items-center justify-end gap-3 flex-wrap">

                    <a href="{{ route('administracion.administrativa.roles.index') }}"
                        class="px-5 py-2 rounded-full text-xs font-medium tracking-wide
                               text-slate-500 dark:text-slate-400
                               border border-slate-200 dark:border-white/[0.08]
                               hover:bg-slate-100 dark:hover:bg-white/5
                               hover:text-slate-700 dark:hover:text-slate-200
                               transition-all duration-200">
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

            <div class="h-0.5 bg-gradient-to-r from-green-700 via-lime-500 via-sky-600 via-purple-700 to-amber-500 opacity-50"></div>

        </div>

    </div>

    @push('js')
    <script>
        function togglePermiso(checkbox) {
            const label = checkbox.closest('label');
            const dark = document.documentElement.classList.contains('dark');
            const span = label.querySelector('span');

            if (checkbox.checked) {
                label.classList.remove(
                    dark ? 'bg-slate-800' : 'bg-slate-50',
                    dark ? 'border-white/[0.05]' : 'border-slate-200'
                );
                label.classList.add('bg-lime-500/[0.08]', 'border-lime-500/25');
                if (span) {
                    span.classList.remove('text-slate-600', 'dark:text-slate-400');
                    span.classList.add(dark ? 'text-white/85' : 'text-slate-800');
                }
            } else {
                label.classList.remove('bg-lime-500/[0.08]', 'border-lime-500/25');
                label.classList.add(
                    dark ? 'bg-slate-800' : 'bg-slate-50',
                    dark ? 'border-white/[0.05]' : 'border-slate-200'
                );
                if (span) {
                    span.classList.remove('text-white/85', 'text-slate-800');
                    span.classList.add(dark ? 'text-slate-400' : 'text-slate-600');
                }
            }
        }
    </script>
    @endpush

</x-admin-layout>
