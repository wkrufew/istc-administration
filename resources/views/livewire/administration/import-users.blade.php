<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Importar Usuarios Masivamente</h2>
                <p class="text-sm text-gray-600 mt-1">Sube un archivo Excel o CSV con los datos de los usuarios</p>
            </div>

            <div class="p-6">
                <!-- Mensajes de éxito -->
                @if ($successMessage)
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold">{{ $successMessage }}</span>
                        </div>
                    </div>
                @endif

                <!-- Errores -->
                @if (count($errors) > 0)
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold mb-2">Se encontraron los siguientes errores:</p>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    @foreach ($errors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Instrucciones -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                    <h3 class="font-semibold mb-2">Instrucciones:</h3>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>Descarga la plantilla de ejemplo haciendo clic en el botón abajo</li>
                        <li>Los campos obligatorios son: <strong>first_name</strong>, <strong>last_name</strong> y
                            <strong>cedula</strong>
                        </li>
                        <li>Los demás campos son opcionales. Si están vacíos en el Excel, no se importarán</li>
                        <li>El campo <strong>rol</strong> debe ser uno de: Administrador, Secretaria, Docente,
                            Estudiante, Admision</li>
                        <li>Si no se especifica email, se generará automáticamente</li>
                        <li>Si no se especifica contraseña, se asignará "12345678" por defecto</li>
                        <li>Para discapacidad e is_facturador, usa "si" o "no"</li>
                        <li>El formato de fecha debe ser: AAAA-MM-DD (ejemplo: 1990-12-31)</li>
                    </ul>
                </div>

                <!-- Botón descargar plantilla -->
                <div class="mb-6">
                    <button wire:click="downloadTemplate"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar Plantilla Excel
                    </button>
                </div>

                <!-- Formulario de carga -->
                <form wire:submit.prevent="import" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Archivo Excel o CSV
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label
                                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Click para seleccionar</span> o arrastra el archivo
                                        aquí
                                    </p>
                                    <p class="text-xs text-gray-500">Excel (.xlsx, .xls) o CSV (máx. 10MB)</p>

                                    @if ($file)
                                        <p class="mt-3 text-sm text-green-600 font-semibold">
                                            ✓ Archivo seleccionado: {{ $file->getClientOriginalName() }}
                                        </p>
                                    @endif
                                </div>
                                <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="hidden" />
                            </label>
                        </div>
                        {{--  @error('file') 
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> 
                        @enderror --}}
                    </div>

                    <!-- Indicador de carga -->
                    <div wire:loading wire:target="file" class="text-sm text-blue-600">
                        Cargando archivo...
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                        <a href="{{ route('administracion.administrativa.estudiantes.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                            Volver
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            @if (!$file) disabled @endif>
                            <span wire:loading.remove wire:target="import">Importar Usuarios</span>
                            <span wire:loading wire:target="import">Importando...</span>
                        </button>
                    </div>
                </form>

                <!-- Estadísticas de importación -->
                @if ($importedCount > 0 || count($errors) > 0)
                    <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">Resumen de Importación</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center">
                                <span class="text-gray-600">Usuarios importados:</span>
                                <span class="ml-2 font-semibold text-green-600">{{ $importedCount }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-600">Errores encontrados:</span>
                                <span class="ml-2 font-semibold text-red-600">{{ count($errors) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Campos del Excel</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Obligatorio</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-2 font-medium">first_name</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Sí</span></td>
                            <td class="px-4 py-2 text-gray-600">Nombre del usuario</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">last_name</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Sí</span></td>
                            <td class="px-4 py-2 text-gray-600">Apellido del usuario</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">cedula</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Sí</span></td>
                            <td class="px-4 py-2 text-gray-600">Número de cédula (único)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">rol</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Opcional</span></td>
                            <td class="px-4 py-2 text-gray-600">Administrador, Secretaria, Docente, Estudiante,
                                Admision</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">email</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Opcional</span>
                            </td>
                            <td class="px-4 py-2 text-gray-600">Se genera automáticamente si está vacío</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium">password</td>
                            <td class="px-4 py-2"><span
                                    class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Opcional</span>
                            </td>
                            <td class="px-4 py-2 text-gray-600">Contraseña (predeterminado: 12345678)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium" colspan="3">
                                <span class="text-gray-500">Todos los demás campos son opcionales...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
