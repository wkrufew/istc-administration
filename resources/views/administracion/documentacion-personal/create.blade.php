<x-admin-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        {{-- Card --}}
        <div
            class="bg-white dark:bg-gray-900/60 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden backdrop-blur">

            {{-- Header --}}
            <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">
                    Cargar Documentos del Docente
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Sube los archivos en PDF. Debes cargar al menos un documento.
                </p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="px-6 pt-6">
                    <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="h-10 w-10 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.75 6a.75.75 0 0 0-1.5 0v5.25a.75.75 0 0 0 1.5 0V8.25ZM12 16.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div class="flex-1">
                                <p class="font-bold text-red-800 dark:text-red-200">
                                    Revisa los campos
                                </p>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-300 space-y-1 list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('administracion.administrativa.documentacion-personal.store') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-2">
                @csrf

                {{-- Docente --}}
                <div>
                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                        Docente <span class="text-red-500">*</span>
                    </label>

                    @if ($user)
                        <input type="text"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-950/50 text-gray-800 dark:text-gray-200 px-4 py-3 text-sm shadow-sm"
                            value="{{ $user->name }} - {{ $user->email }}" disabled>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                    @else
                        <select name="user_id" required
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950/50 text-gray-900 dark:text-gray-100 px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <option value="">Seleccione un docente</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('user_id')
                        <p class="text-sm text-red-600 dark:text-red-300 mt-2 font-semibold">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-gray-100">
                                Archivos (PDF)
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Al menos uno es requerido.
                            </p>
                        </div>

                        <span
                            class="hidden sm:inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200">
                            Solo PDF
                        </span>
                    </div>

                    {{-- Files Grid --}}
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Curriculum --}}
                        <div
                            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 p-4">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                                Hoja de Vida (Curriculum)
                            </label>

                            <input type="file" name="file_curriculum" accept="application/pdf"
                                class="block w-full text-sm text-gray-700 dark:text-gray-200
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0
                                file:text-sm file:font-bold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                cursor-pointer" />

                            @error('file_curriculum')
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Senescyt --}}
                        <div
                            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 p-4">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                                Título Senescyt
                            </label>

                            <input type="file" name="file_senescyt" accept="application/pdf"
                                class="block w-full text-sm text-gray-700 dark:text-gray-200
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0
                                file:text-sm file:font-bold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                cursor-pointer" />

                            @error('file_senescyt')
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Contrato --}}
                        <div
                            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 p-4">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                                Contrato
                            </label>

                            <input type="file" name="file_contrato" accept="application/pdf"
                                class="block w-full text-sm text-gray-700 dark:text-gray-200
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0
                                file:text-sm file:font-bold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                cursor-pointer" />

                            @error('file_contrato')
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Otro --}}
                        <div
                            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 p-4">
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">
                                Otro Documento
                            </label>

                            <input type="file" name="file_otro" accept="application/pdf"
                                class="block w-full text-sm text-gray-700 dark:text-gray-200
                                file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0
                                file:text-sm file:font-bold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700
                                cursor-pointer" />

                            @error('file_otro')
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 text-sm font-extrabold shadow-sm transition">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h11l5 5v9a2 2 0 0 1-2 2H5Zm7-14v4h4V6h-4Z" />
                        </svg>
                        Guardar Documentos
                    </button>

                    <a href="{{ route('administracion.administrativa.documentacion-personal.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950/50 px-5 py-3 text-sm font-extrabold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
