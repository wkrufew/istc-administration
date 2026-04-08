<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Editar Usuario</h2>
        </div>

        @if (session()->has('message'))
            <div class="m-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="update" class="p-6 space-y-6" autocomplete="off">

            <!-- Foto de Perfil -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Foto de Perfil</h3>
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        @if ($profile_photo)
                            <img class="h-24 w-24 object-cover rounded-full" src="{{ $profile_photo->temporaryUrl() }}"
                                alt="Vista previa">
                            {{-- {{ $profile_photo_actual }} --}}
                        @elseif($profile_photo_actual)
                            <img class="h-24 w-24 object-cover rounded-full"
                                src="{{ Storage::url($profile_photo_actual) }}" alt="Foto actual">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block">
                            <span class="sr-only">Elegir foto de perfil</span>
                            <input type="file" wire:model="profile_photo" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        </label>
                        @if ($profile_photo_actual)
                            <button type="button" wire:click="deletePhoto"
                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                                Eliminar foto actual
                            </button>
                        @endif
                    </div>
                </div>
                @error('profile_photo')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Datos Básicos -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos Básicos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" wire:model="first_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('first_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido *</label>
                        <input type="text" wire:model="last_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('last_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cédula *</label>
                        <input type="text" wire:model="cedula"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('cedula')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico *</label>
                        <input type="email" wire:model="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nueva Contraseña (dejar vacío para no
                            cambiar)</label>
                        <input type="password" wire:model="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                        <input type="password" wire:model="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol *</label>
                        <select wire:model="role_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione un rol</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" wire:model="phone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                        <input type="date" wire:model="fecha_nacimiento"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Género</label>
                        <select wire:model="genero"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" wire:model="address"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Datos Académicos -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos Académicos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Matrícula</label>
                        <input type="text" wire:model="matricula_numero"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nacionalidad</label>
                        <input type="text" wire:model="nacionalidad"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                        <input type="text" wire:model="estado_civil"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Datos Familiares -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos Familiares</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Padre</label>
                        <input type="text" wire:model="padre"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Madre</label>
                        <input type="text" wire:model="madre"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tutor Legal</label>
                        <input type="text" wire:model="tutor"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Datos Médicos -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos Médicos y de Emergencia</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Sangre</label>
                        <input type="text" wire:model="tipo_sangre"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono de Emergencia</label>
                        <input type="text" wire:model="telefono_emergencia"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Contacto de Emergencia</label>
                        <input type="text" wire:model="contacto_emergencia"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Observaciones Médicas</label>
                        <textarea wire:model="observaciones_medicas" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Discapacidad -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Discapacidad</h3>

                <div class="flex items-center space-x-3 mb-4">
                    <label class="text-sm font-medium text-gray-700">¿Presenta alguna discapacidad?</label>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="discapacidad" class="peer sr-only" />
                        <div
                            class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-blue-300">
                        </div>
                    </label>
                    <span class="text-sm font-semibold {{ $discapacidad ? 'text-blue-600' : 'text-gray-500' }}">
                        {{ $discapacidad ? 'Sí' : 'No' }}
                    </span>
                </div>

                @if ($discapacidad)
                    <div class="space-y-4 mt-4 p-4 bg-blue-50 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descripción de la Discapacidad
                                *</label>
                            <textarea wire:model="discapacidad_descripcion" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('discapacidad_descripcion')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Certificado de Discapacidad</label>

                            @if ($certificado_discapacidad_actual)
                                <div class="mb-2 p-3 bg-white rounded border flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Certificado actual guardado</span>
                                    <button type="button" wire:click="deleteCertificado"
                                        class="text-sm text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                </div>
                            @endif

                            <input type="file" wire:model="certificado_discapacidad" accept="image/*,.pdf"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-xs text-gray-500 mt-1">Formatos aceptados: JPG, PNG, PDF (máx. 2MB)</p>
                            @error('certificado_discapacidad')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>

            <!-- Datos de Facturación -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos de Facturación</h3>

                <div class="flex items-center space-x-3 mb-4">
                    <label class="text-sm font-medium text-gray-700">¿Es el mismo usuario quien factura?</label>
                    {{-- <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model.live="is_facturador" class="peer sr-only" />
                        <div
                            class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300">
                        </div>
                    </label> --}}
                    <label class="relative inline-flex cursor-pointer items-center mt-1">
                        <input id="switch" wire:model.live="is_facturador" type="checkbox" class="peer sr-only"
                            id="switch-{{ $is_facturador }}" @if ($is_facturador) checked @endif />
                        <label for="switch" class="hidden"></label>
                        <div
                            class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300">
                        </div>
                    </label>
                    <span class="text-sm font-semibold {{ $is_facturador ? 'text-green-600' : 'text-gray-500' }}">
                        {{ $is_facturador ? 'Sí' : 'No' }}
                    </span>
                </div>

                @if (!$is_facturador)
                    <div class="space-y-4 mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                                <input type="text" wire:model="fact_nombre"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fact_nombre')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Documento *</label>
                                <input type="text" wire:model="fact_documento"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fact_documento')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Correo *</label>
                                <input type="email" wire:model="fact_correo"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fact_correo')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono *</label>
                                <input type="text" wire:model="fact_telefono"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fact_telefono')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Dirección *</label>
                                <input type="text" wire:model="fact_direccion"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fact_direccion')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estado -->
            <div>
                <div class="flex items-center space-x-3">
                    <label class="text-sm font-medium text-gray-700">Estado del Usuario</label>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" wire:model="is_active" class="peer sr-only" />
                        <div
                            class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300">
                        </div>
                    </label>
                    <span class="text-sm font-semibold {{ $is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('administracion.administrativa.estudiantes.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    Volver
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Actualizar Usuario
                </button>
            </div>
        </form>
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
                    title: data.message
                });
            });
        });
    </script>
@endpush
