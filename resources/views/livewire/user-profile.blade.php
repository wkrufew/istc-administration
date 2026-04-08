<div>
    {{-- @if ($this->esDocente)
        Hola soy docente
    @endif

    @if ($this->esEstudiante)
        Hola soy estudiante
    @endif --}}

    <div class="max-w-7xl mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-800">
                    👤 Mi Perfil
                </h2>
                <p class="text-sm text-gray-500">
                    Actualiza tu información personal y tu contraseña.
                </p>
            </div>

            {{-- Datos bloqueados --}}
            <div class="flex flex-wrap gap-2">
                {{-- <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    Rol ID: {{ auth()->user()->getRoleNames()->first() ?? '—' }}
                </span> --}}

                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    Estado: {{ $is_active ? 'Activo' : 'Inactivo' }}
                </span>
                @if ($this->esEstudiante)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                        Número de Registro #: {{ $matricula_numero ?? '—' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="mt-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            {{-- Foto + Resumen --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border p-5">
                    <h3 class="font-bold text-gray-800 mb-3">
                        📸 Foto de perfil
                    </h3>
                    <div class="flex flex-col items-center gap-4">
                        <div class="shrink-0">
                            @if ($photo)
                                <img class="h-24 w-24 object-cover rounded-full" src="{{ $photo->temporaryUrl() }}"
                                    alt="Vista previa">
                                {{-- {{ $photo_preview }} --}}
                            @elseif($photo_preview)
                                <img class="h-24 w-24 object-cover rounded-full"
                                    src="{{ Storage::url($photo_preview) }}" alt="Foto actual">
                            @else
                                <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            {{-- </div> --}}
                        </div>

                        <div class="w-full">
                            <input type="file" wire:model.live="photo" class="w-full text-sm border rounded-xl p-2">
                            @error('photo')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <p class="text-xs text-gray-500 mt-2">
                                Formato recomendado: JPG o PNG (máx. 2MB)
                            </p>

                            @if ($photo_preview)
                                <button type="button" wire:click="deletePhoto"
                                    class="mt-2 text-sm text-red-600 hover:text-red-800">
                                    Eliminar foto actual
                                </button>
                            @endif

                            @if ($photo)
                                <div class="mt-6 flex justify-center">
                                    <button wire:click="savePhoto" wire:loading.attr="disabled"
                                        class="px-5 py-2.5 rounded-xl bg-gray-800 text-white font-semibold hover:bg-black transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="savePhoto">🔁 Guardar Foto</span>
                                        <span wire:loading wire:target="savePhoto">Actualizando...</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Password --}}
                        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                            <div class="p-5 border-b bg-gradient-to-r from-gray-50 to-slate-50">
                                <h3 class="font-bold text-gray-800">
                                    🔐 Seguridad
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Cambia tu contraseña cuando lo necesites.
                                </p>
                            </div>

                            <div class="p-5">

                                @if (session('success_password'))
                                    <div
                                        class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                                        {{ session('success_password') }}
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    {{-- Actual --}}
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-semibold text-gray-700">Contraseña actual</label>
                                        <input type="password" wire:model.defer="current_password"
                                            class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        @error('current_password')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Nueva --}}
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700">Nueva contraseña</label>
                                        <input type="password" wire:model.defer="password"
                                            class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                        @error('password')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Confirmar --}}
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700">Confirmar contraseña</label>
                                        <input type="password" wire:model.defer="password_confirmation"
                                            class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                </div>

                                <div class="mt-6 flex justify-center">
                                    <button wire:click="updatePassword" wire:loading.attr="disabled"
                                        class="px-5 py-2.5 rounded-xl bg-gray-800 text-white font-semibold hover:bg-black transition disabled:opacity-50">
                                        <span wire:loading.remove wire:target="updatePassword">🔁 Cambiar
                                            contraseña</span>
                                        <span wire:loading wire:target="updatePassword">Actualizando...</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            <div class="mt-10 sm:mt-0">
                                @livewire('profile.two-factor-authentication-form')
                            </div>
                        @endif

                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.logout-other-browser-sessions-form')
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Datos personales --}}
                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                    <div class="p-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="font-bold text-gray-800">
                            🧾 Datos personales
                        </h3>
                        <p class="text-sm text-gray-600">
                            Actualiza tu información. Los cambios se guardan inmediatamente.
                        </p>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Nombres --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Nombres</label>
                                <input type="text" wire:model.defer="firstname"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('firstname')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Apellidos --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Apellidos</label>
                                <input type="text" wire:model.defer="lastname"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('lastname')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cédula --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Cédula</label>
                                <input type="text" wire:model.defer="cedula"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('cedula')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Correo</label>
                                <input type="email" wire:model.defer="email"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('email')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Teléfono</label>
                                <input type="text" wire:model.defer="phone"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('phone')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dirección --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Dirección</label>
                                <input type="text" wire:model.defer="address"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('address')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Fecha nacimiento --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Fecha de nacimiento</label>
                                <input type="date" wire:model.defer="fecha_nacimiento"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @error('fecha_nacimiento')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Sexo --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Genero</label>
                                <select wire:model.defer="genero"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— Seleccionar —</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('genero')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Estado civil --}}
                            <div class="">
                                <label class="text-sm font-semibold text-gray-700">Estado civil</label>
                                {{-- <input type="text" wire:model.defer="estado_civil"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"> --}}
                                <select wire:model.defer="estado_civil"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">— Seleccionar —</option>
                                    <option value="Soltero">Soltero (a)</option>
                                    <option value="Casado">Casado (a)</option>
                                    <option value="Divorciado">Divorciado (a)</option>
                                    <option value="Viudo">Viudo (a)</option>
                                    <option value="Union Libre">Union Libre (a)</option>

                                </select>
                                @error('estado_civil')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- Nacionalidad o etnia --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Identificación étnica</label>

                                <select wire:model.defer="nacionalidad"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">— Seleccione —</option>

                                    <option value="Mestizo">Mestizo</option>
                                    <option value="Indigena">Indígena</option>
                                    <option value="Afroecuatoriano">Afroecuatoriano</option>
                                    <option value="Negro">Negro</option>
                                    <option value="Mulato">Mulato</option>
                                    <option value="Montubio">Montubio</option>
                                    <option value="Blanco">Blanco</option>
                                    <option value="Otro">Otro</option>
                                    <option value="No sabe / No responde">No sabe / No responde</option>

                                </select>

                                @error('nacionalidad')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- Tipo de sangre --}}
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Tipo de sangre</label>

                                <select wire:model.defer="tipo_sangre"
                                    class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                                    <option value="">— Seleccione —</option>

                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>

                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>

                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>

                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>

                                    <option value="No sabe / No responde">No sabe / No responde</option>
                                </select>

                                @error('tipo_sangre')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Observaciones médicas --}}
                            <div class="">
                                <label class="text-sm font-semibold text-gray-700">
                                    Observaciones Médicas
                                </label>

                                <textarea wire:model.defer="observaciones_medicas" rows="3"
                                    placeholder="Ej: Alergias, tratamientos, enfermedades crónicas, medicación..."
                                    class="w-full mt-1 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700
               shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200
               transition duration-150"></textarea>

                                @error('observaciones_medicas')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Discapacidad -->
                            <div class="border-b pb-2">
                                <h3 class="text-lg font-semibold text-gray-700 mb-4">Discapacidad</h3>

                                <div class="flex items-center space-x-3 mb-4">
                                    <label class="text-sm font-medium text-gray-700">¿Presenta alguna
                                        discapacidad?</label>
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" wire:model.live="discapacidad" class="peer sr-only" />
                                        <div
                                            class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-blue-300">
                                        </div>
                                    </label>
                                    <span
                                        class="text-sm font-semibold {{ $discapacidad ? 'text-blue-600' : 'text-gray-500' }}">
                                        {{ $discapacidad ? 'Sí' : 'No' }}
                                    </span>
                                </div>

                                @if ($discapacidad)
                                    <div class="space-y-4 mt-4 p-4 bg-blue-50 rounded-lg">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Descripción de la
                                                Discapacidad
                                                *</label>
                                            <textarea wire:model="discapacidad_descripcion" rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                            @error('discapacidad_descripcion')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Certificado de
                                                Discapacidad</label>

                                            @if ($certificado_discapacidad_actual)
                                                <div
                                                    class="mb-2 p-3 bg-white rounded border flex items-center justify-between">
                                                    <span class="text-sm text-gray-600">Certificado actual
                                                        guardado</span>
                                                    <button type="button" wire:click="deleteCertificado"
                                                        class="text-sm text-red-600 hover:text-red-800">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            @endif

                                            <input type="file" wire:model="certificado_discapacidad"
                                                accept="image/*,.pdf"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                            <p class="text-xs text-gray-500 mt-1">Formatos aceptados: JPG, PNG, PDF
                                                (máx. 2MB)</p>
                                            @error('certificado_discapacidad')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Datos de Facturación -->
                            @if ($this->esEstudiante)
                                <div class="border-b pb-6">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos de Facturación</h3>

                                    <div class="flex items-center space-x-3 mb-4">
                                        <label class="text-sm font-medium text-gray-700">¿Es el mismo usuario quien
                                            factura?</label>
                                        <label class="relative inline-flex cursor-pointer items-center mt-1">
                                            <input id="switch" wire:model.live="is_facturador" type="checkbox"
                                                class="peer sr-only" id="switch-{{ $is_facturador }}"
                                                @if ($is_facturador) checked @endif />
                                            <label for="switch" class="hidden"></label>
                                            <div
                                                class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-green-300">
                                            </div>
                                        </label>
                                        <span
                                            class="text-sm font-semibold {{ $is_facturador ? 'text-green-600' : 'text-gray-500' }}">
                                            {{ $is_facturador ? 'Sí' : 'No' }}
                                        </span>
                                    </div>

                                    @if (!$is_facturador)
                                        <div class="space-y-4 mt-4 p-4 bg-gray-50 rounded-lg">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Nombre
                                                        Completo
                                                        *</label>
                                                    <input type="text" wire:model="fact_nombre"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    @error('fact_nombre')
                                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Documento
                                                        *</label>
                                                    <input type="text" wire:model="fact_documento"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    @error('fact_documento')
                                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Correo
                                                        *</label>
                                                    <input type="email" wire:model="fact_correo"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    @error('fact_correo')
                                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Teléfono
                                                        *</label>
                                                    <input type="text" wire:model="fact_telefono"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    @error('fact_telefono')
                                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700">Dirección
                                                        *</label>
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
                            @endif
                            <div class="mx-auto my-auto md:col-span-2">
                                <button wire:click="saveProfile" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="saveProfile">💾 Guardar cambios</span>
                                    <span wire:loading wire:target="saveProfile">Guardando...</span>
                                </button>
                            </div>

                        </div>

                        {{-- Botón --}}
                        {{-- <div class="mt-6 flex justify-center">
                            <button wire:click="saveProfile" wire:loading.attr="disabled"
                                class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="saveProfile">💾 Guardar cambios</span>
                                <span wire:loading wire:target="saveProfile">Guardando...</span>
                            </button>
                        </div> --}}
                    </div>
                </div>

                @if ($this->esEstudiante)
                    {{-- Datos de emergencia --}}
                    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                        <div class="p-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                            <h3 class="font-bold text-gray-800">
                                🧾 Datos de Emergencia
                            </h3>
                            <p class="text-sm text-gray-600">
                                Actualiza la informacion de los contactos de emergencia. Los cambios se guardan
                                inmediatamente.
                            </p>
                        </div>

                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- Nombres del padre --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Nombres del Padre</label>
                                    <input type="text" wire:model.defer="padre"
                                        class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @error('padre')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- nombres de la madre --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Nombres de la Madre</label>
                                    <input type="text" wire:model.defer="madre"
                                        class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @error('madre')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- tutot legal --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Tutor Legal</label>
                                    <input type="text" wire:model.defer="tutor"
                                        class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @error('tutor')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- contacto de emergencia --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Nombre del contacto de
                                        Emergencia</label>
                                    <input type="tel" wire:model.defer="contacto_emergencia"
                                        class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @error('contacto_emergencia')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- telefono de emergencia --}}
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Telefono de Emergencia</label>
                                    <input type="tel" wire:model.defer="telefono_emergencia"
                                        class="w-full mt-1 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    @error('telefono_emergencia')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            {{-- Botón --}}
                            <div class="mt-6 flex justify-center">
                                <button wire:click="saveProfile" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="saveProfile">💾 Guardar cambios</span>
                                    <span wire:loading wire:target="saveProfile">Guardando...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>

</div>
