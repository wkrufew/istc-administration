<x-admin-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Editar Documentos de Docente
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Actualiza los archivos del docente. Si no deseas cambiar un documento, deja el campo vacío.
            </p>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div
                class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                <div class="flex items-start gap-3">
                    <div class="mt-1">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Ups... revisa estos errores:</p>
                        <ul class="mt-2 list-disc pl-5 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card principal --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 overflow-hidden">

            <form
                action="{{ route('administracion.administrativa.documentacion-personal.update', $documentacion_personal) }}"
                method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- Docente --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Información del Docente
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Selecciona el docente al que pertenecen estos documentos.
                            </p>
                        </div>

                        <div class="w-full sm:w-[420px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Docente *
                            </label>
                            <select name="user_id" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition
                                    focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30
                                    dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 dark:focus:ring-blue-400/30">
                                <option value="">Seleccione un docente</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $documentacion_personal->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>

                            @error('user_id')
                                <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Archivos --}}
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Documentos del Docente
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Sube archivos en PDF. Se recomienda que los documentos sean legibles y sin contraseña.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- ITEM: Curriculum --}}
                        <div
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Hoja de Vida (Curriculum)
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        Formato PDF
                                    </p>
                                </div>

                                @if ($documentacion_personal->file_curriculum)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Cargado
                                    </span>
                                @endif
                            </div>

                            @if ($documentacion_personal->file_curriculum)
                                <a href="{{ Storage::url($documentacion_personal->file_curriculum) }}" target="_blank"
                                    class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M12.293 2.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-8 8a1 1 0 01-.293.207l-4 2a1 1 0 01-1.316-1.316l2-4a1 1 0 01.207-.293l8-8z" />
                                    </svg>
                                    Ver archivo actual
                                </a>
                            @endif

                            <div class="mt-4">
                                <input type="file" name="file_curriculum" accept="application/pdf"
                                    class="block w-full text-sm
                                        file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                                        hover:file:bg-blue-700
                                        dark:file:bg-blue-500 dark:hover:file:bg-blue-600
                                        text-gray-700 dark:text-gray-200" />
                                @error('file_curriculum')
                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- ITEM: Senescyt --}}
                        <div
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Título Senescyt
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        Formato PDF
                                    </p>
                                </div>

                                @if ($documentacion_personal->file_senescyt)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Cargado
                                    </span>
                                @endif
                            </div>

                            @if ($documentacion_personal->file_senescyt)
                                <a href="{{ Storage::url($documentacion_personal->file_senescyt) }}" target="_blank"
                                    class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    Ver archivo actual
                                </a>
                            @endif

                            <div class="mt-4">
                                <input type="file" name="file_senescyt" accept="application/pdf"
                                    class="block w-full text-sm
                                        file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                                        hover:file:bg-blue-700
                                        dark:file:bg-blue-500 dark:hover:file:bg-blue-600
                                        text-gray-700 dark:text-gray-200" />
                                @error('file_senescyt')
                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- ITEM: Contrato --}}
                        <div
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Contrato
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        Formato PDF
                                    </p>
                                </div>

                                @if ($documentacion_personal->file_contrato)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Cargado
                                    </span>
                                @endif
                            </div>

                            @if ($documentacion_personal->file_contrato)
                                <a href="{{ Storage::url($documentacion_personal->file_contrato) }}" target="_blank"
                                    class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    Ver archivo actual
                                </a>
                            @endif

                            <div class="mt-4">
                                <input type="file" name="file_contrato" accept="application/pdf"
                                    class="block w-full text-sm
                                        file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                                        hover:file:bg-blue-700
                                        dark:file:bg-blue-500 dark:hover:file:bg-blue-600
                                        text-gray-700 dark:text-gray-200" />
                                @error('file_contrato')
                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- ITEM: Cédula --}}
                        <div
                            class="rounded-2xl border border-gray-200 bg-gray-50 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        Cédula
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        Formato PDF
                                    </p>
                                </div>

                                @if ($documentacion_personal->file_otro)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Cargado
                                    </span>
                                @endif
                            </div>

                            @if ($documentacion_personal->file_otro)
                                <a href="{{ Storage::url($documentacion_personal->file_otro) }}" target="_blank"
                                    class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    Ver archivo actual
                                </a>
                            @endif

                            <div class="mt-4">
                                <input type="file" name="file_otro" accept="application/pdf"
                                    class="block w-full text-sm
                                        file:mr-4 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white
                                        hover:file:bg-blue-700
                                        dark:file:bg-blue-500 dark:hover:file:bg-blue-600
                                        text-gray-700 dark:text-gray-200" />
                                @error('file_otro')
                                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- Botones --}}
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <a href="{{ route('administracion.administrativa.documentacion-personal.index') }}"
                            class="inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            Volver
                        </a>

                        <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700
                            focus:outline-none focus:ring-2 focus:ring-blue-500/40
                            dark:bg-blue-500 dark:hover:bg-blue-600">
                            Actualizar Documentos
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
