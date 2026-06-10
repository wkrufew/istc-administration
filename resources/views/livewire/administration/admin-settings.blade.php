<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 space-y-6">

        {{-- HEADER --}}
        <div
            class="mb-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Configuración del Sistema</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                Gestiona los parámetros globales del instituto. Los cambios aplican de inmediato.
            </p>
        </div>

        {{-- TABS --}}
        <div
            class="flex flex-wrap gap-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-1.5 shadow-sm">
            @foreach ([
        'instituto' => ['label' => 'Instituto', 'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z'],
        'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z'],
        'smtp' => ['label' => 'Correo SMTP', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75'],
        'documentos' => ['label' => 'Documentos', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
        'notificaciones' => ['label' => 'Notificaciones', 'icon' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0'],
        'matricula'      => ['label' => 'Matrícula',      'icon' => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z'],
        'cedula_api'     => ['label' => 'API Cédula',     'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 21Z'],
        ...((int) env('MOODLE_MODE', 0) === 1 ? [
            'moodle' => ['label' => 'Moodle', 'icon' => 'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z'],
        ] : []),
    ] as $tabKey => $tabData)
                <button wire:click="$set('tab', '{{ $tabKey }}')"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                           {{ $tab === $tabKey
                               ? 'bg-blue-600 text-white shadow-sm'
                               : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="{{ $tabData['icon'] }}" />
                    </svg>
                    <span class="hidden sm:inline">{{ $tabData['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- ================================================================
             TAB: INSTITUTO
             ================================================================ --}}
        @if ($tab === 'instituto')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3
                    class="text-base font-bold text-gray-800 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3">
                    Información del Instituto
                </h3>

                {{-- Logos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Logo principal --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">
                            Logo Principal <span class="font-normal text-gray-400">(PNG/JPG, máx 2 MB)</span>
                        </label>
                        <div class="flex items-center gap-4">
                            @if ($instituto_logo_path)
                                <img src="{{ Storage::url($instituto_logo_path) }}"
                                    class="h-14 w-auto rounded-xl border border-gray-200 dark:border-gray-600 object-contain bg-white p-1"
                                    alt="Logo actual">
                            @else
                                <div
                                    class="h-14 w-24 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-xs text-gray-400">
                                    Sin logo
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" wire:model="nuevoLogo" accept="image/*"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('nuevoLogo')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Favicon --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">
                            Favicon <span class="font-normal text-gray-400">(PNG/ICO/WebP, máx 512 KB)</span>
                        </label>
                        <div class="flex items-center gap-4">
                            @if ($instituto_favicon_path)
                                <img src="{{ Storage::url($instituto_favicon_path) }}"
                                    class="h-10 w-10 rounded-lg border border-gray-200 dark:border-gray-600 object-contain bg-white p-1"
                                    alt="Favicon actual">
                            @else
                                <div
                                    class="h-10 w-10 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-xs text-gray-400">
                                    ico
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" wire:model="nuevoFavicon" accept="image/*,.ico"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('nuevoFavicon')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Nombre largo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="instituto_nombre_largo"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Instituto Superior Tecnológico...">
                        @error('instituto_nombre_largo')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Nombre abreviado / Siglas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="instituto_nombre_corto"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="ISTC">
                        @error('instituto_nombre_corto')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">RUC</label>
                        <input type="text" wire:model="instituto_ruc"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="0690012345001">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Código SENESCYT</label>
                        <input type="text" wire:model="instituto_senescyt"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="07-2024-01-SETEC">
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Dirección</label>
                        <input type="text" wire:model="instituto_direccion"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Av. Principal y Calle Secundaria, Ciudad">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Teléfono</label>
                        <input type="text" wire:model="instituto_telefono"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="+593 999 999 999">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Email
                            institucional</label>
                        <input type="email" wire:model="instituto_email"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="info@instituto.edu.ec">
                        @error('instituto_email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Sitio
                            web</label>
                        <input type="url" wire:model="instituto_web"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="https://www.instituto.edu.ec">
                        @error('instituto_web')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarInstituto" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarInstituto">Guardar instituto</span>
                        <span wire:loading wire:target="guardarInstituto">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: WHATSAPP
             ================================================================ --}}
        @if ($tab === 'whatsapp')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">WhatsApp Business (Meta)</h3>
                    {{-- Toggle activo --}}
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                            {{ $whatsapp_activo === '1' ? 'Activo' : 'Inactivo' }}
                        </span>
                        <div class="relative"
                            wire:click="$set('whatsapp_activo', '{{ $whatsapp_activo === '1' ? '0' : '1' }}')">
                            <div
                                class="w-10 h-5 rounded-full transition {{ $whatsapp_activo === '1' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform {{ $whatsapp_activo === '1' ? 'translate-x-5' : 'translate-x-0' }}">
                            </div>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Phone Number ID
                            <span class="font-normal text-gray-400">(WHATSAPP_PHONE_NUMBER_ID)</span>
                        </label>
                        <input type="text" wire:model="whatsapp_phone_number_id"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="593983942105">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Access Token
                            <span class="font-normal text-gray-400">(cifrado en BD)</span>
                        </label>
                        <input type="password" wire:model="whatsapp_access_token"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="EAAxxxxxxxxxxxxxxx" autocomplete="new-password">
                        <p class="mt-1 text-xs text-gray-400">Deja en blanco para no modificar el token guardado.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Template: Confirmación de matrícula
                            <span class="font-normal text-gray-400">(renovación)</span>
                        </label>
                        <input type="text" wire:model="whatsapp_template_confirmacion"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="matricula_confirmacion">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Template: Bienvenida
                            <span class="font-normal text-gray-400">(primera matrícula + credenciales)</span>
                        </label>
                        <input type="text" wire:model="whatsapp_template_bienvenida"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="matricula_bienvenida">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Template: Confirmación de pago
                            <span class="font-normal text-gray-400">(colegiatura, multa, arrastre...)</span>
                        </label>
                        <input type="text" wire:model="whatsapp_template_pago"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="pago_confirmacion">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Template: Pago primera matrícula
                            <span class="font-normal text-gray-400">(matrícula + inscripción auto-liquidada)</span>
                        </label>
                        <input type="text" wire:model="whatsapp_template_pago_primera"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="pago_primera_matricula">
                    </div>
                </div>

                <div
                    class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 p-3 text-xs text-amber-700 dark:text-amber-400">
                    Los datos se obtienen desde <strong>Meta for Developers → WhatsApp → API Setup</strong>.
                    El Access Token se almacena cifrado con AES-256.
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarWhatsapp" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarWhatsapp">Guardar WhatsApp</span>
                        <span wire:loading wire:target="guardarWhatsapp">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: SMTP
             ================================================================ --}}
        @if ($tab === 'smtp')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Configuración de Correo SMTP</h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                            {{ $smtp_activo === '1' ? 'Activo' : 'Inactivo' }}
                        </span>
                        <div class="relative"
                            wire:click="$set('smtp_activo', '{{ $smtp_activo === '1' ? '0' : '1' }}')">
                            <div
                                class="w-10 h-5 rounded-full transition {{ $smtp_activo === '1' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform {{ $smtp_activo === '1' ? 'translate-x-5' : 'translate-x-0' }}">
                            </div>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Driver</label>
                        <select wire:model="smtp_driver"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="smtp">SMTP</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="ses">Amazon SES</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Encriptación</label>
                        <select wire:model="smtp_encryption"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="none">Ninguna</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Host
                            SMTP</label>
                        <input type="text" wire:model="smtp_host"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="smtp.gmail.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Puerto</label>
                        <input type="number" wire:model="smtp_port"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="587">
                        @error('smtp_port')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Usuario /
                            Email SMTP</label>
                        <input type="text" wire:model="smtp_username"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="usuario@gmail.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Contraseña / App Password
                            <span class="font-normal text-gray-400">(cifrado en BD)</span>
                        </label>
                        <input type="password" wire:model="smtp_password"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="••••••••••••" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Nombre del
                            remitente</label>
                        <input type="text" wire:model="smtp_from_name"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Instituto Tecnológico">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Email del
                            remitente</label>
                        <input type="email" wire:model="smtp_from_address"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                            placeholder="noreply@instituto.edu.ec">
                        @error('smtp_from_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Diagnóstico: config activa --}}
                <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50 p-4 space-y-2">
                    <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wide flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Configuración activa para el envío
                    </p>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-mono text-xs text-blue-900 dark:text-blue-300">
                        <span class="text-blue-500 dark:text-blue-500">Host</span>
                        <span>{{ $smtp_host ?: '—' }}</span>
                        <span class="text-blue-500 dark:text-blue-500">Puerto</span>
                        <span>{{ $smtp_port ?: '—' }}</span>
                        <span class="text-blue-500 dark:text-blue-500">Cifrado</span>
                        <span>{{ $smtp_encryption ?: '—' }}</span>
                        <span class="text-blue-500 dark:text-blue-500">Usuario</span>
                        <span>{{ $smtp_username ?: '—' }}</span>
                        <span class="text-blue-500 dark:text-blue-500">Remitente</span>
                        <span>{{ $smtp_from_address ?: '(usa usuario)' }}</span>
                        <span class="text-blue-500 dark:text-blue-500">Contraseña</span>
                        <span>{{ $smtp_password ? str_repeat('•', min(strlen($smtp_password), 10)) : '(vacía)' }}</span>
                    </div>
                    <p class="text-[0.65rem] text-blue-500 dark:text-blue-600 pt-1">
                        El detalle de errores se escribe en <code class="bg-blue-100 dark:bg-blue-900/50 px-1 rounded">storage/logs/laravel.log</code>
                    </p>
                </div>

                {{-- Test email --}}
                <div
                    class="rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 space-y-3">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                        Enviar correo de prueba
                    </p>
                    <div class="flex gap-2">
                        <input type="email" wire:model="emailPrueba"
                            class="flex-1 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="destino@ejemplo.com">
                        <button wire:click="enviarCorreoPrueba" wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition disabled:opacity-50 whitespace-nowrap">
                            <span wire:loading.remove wire:target="enviarCorreoPrueba">Enviar prueba</span>
                            <span wire:loading wire:target="enviarCorreoPrueba">
                                <svg class="inline w-3.5 h-3.5 animate-spin -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                Conectando…
                            </span>
                        </button>
                    </div>
                    @error('emailPrueba')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="text-[0.65rem] text-gray-400 dark:text-gray-500">
                        Si el botón queda en "Conectando…" más de 30 segundos, el servidor SMTP no responde. Verifica host y puerto.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarSmtp" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarSmtp">Guardar SMTP</span>
                        <span wire:loading wire:target="guardarSmtp">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: DOCUMENTOS
             ================================================================ --}}
        @if ($tab === 'documentos')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3
                    class="text-base font-bold text-gray-800 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3">
                    Datos para Actas y Documentos PDF
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Rector / Director
                        </label>
                        <input type="text" wire:model="doc_rector"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Ing. Nombre Apellido, MSc.">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Secretario/a Académico/a
                        </label>
                        <input type="text" wire:model="doc_secretario"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Lcda. Nombre Apellido">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Coordinación Académica
                        </label>
                        <input type="text" wire:model="doc_coordinador"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Ing. Nombre Apellido, Mg.">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Departamento Financiero
                            <span class="font-normal text-gray-400">(opcional — aparece en certificados)</span>
                        </label>
                        <input type="text" wire:model="doc_departamento_financiero"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Lcda. Nombre Apellido">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Ciudad (para encabezados)
                        </label>
                        <input type="text" wire:model="doc_ciudad"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Cuenca, Ecuador">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Texto de pie de página en PDFs
                        </label>
                        <textarea wire:model="doc_pie_pagina" rows="2"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Documento generado por el sistema académico del Instituto..."></textarea>
                        @error('doc_pie_pagina')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarDocumentos" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarDocumentos">Guardar documentos</span>
                        <span wire:loading wire:target="guardarDocumentos">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: NOTIFICACIONES
             ================================================================ --}}
        @if ($tab === 'notificaciones')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3
                    class="text-base font-bold text-gray-800 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3">
                    Preferencias de Notificaciones
                </h3>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Activa o desactiva el envío de notificaciones por canal y evento.
                    Requiere que WhatsApp y SMTP estén configurados y activos.
                </p>

                @php
                    $eventos = [
                        [
                            'clave' => 'matricula',
                            'label' => 'Matrículas',// Matrícula nueva
                            'icon' =>
                                'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
                        ],
                        [
                            'clave' => 'pago',
                            'label' => 'Pago de matrícula',
                            'icon' =>
                                'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
                        ],
                        // Titulación: habilitado a futuro
                        // ['clave' => 'titulacion', 'label' => 'Proceso de titulación', ...]
                    ];
                @endphp

                <div class="space-y-3">
                    {{-- Cabecera --}}
                    <div class="grid grid-cols-3 gap-4 px-4">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Evento
                        </div>
                        <div class="text-xs font-bold text-center text-green-600 uppercase tracking-wide">WhatsApp
                        </div>
                        <div class="text-xs font-bold text-center text-blue-600 uppercase tracking-wide">Email</div>
                    </div>

                    @foreach ($eventos as $ev)
                        <div
                            class="grid grid-cols-3 gap-4 items-center bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="{{ $ev['icon'] }}" />
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $ev['label'] }}</span>
                            </div>
                            {{-- WhatsApp toggle --}}
                            <div class="flex justify-center">
                                @php
                                    $waKey = 'notif_' . $ev['clave'] . '_whatsapp';
                                    $waVal = $this->{$waKey};
                                @endphp
                                <div class="relative cursor-pointer"
                                    wire:click="$set('{{ $waKey }}', '{{ $waVal === '1' ? '0' : '1' }}')">
                                    <div
                                        class="w-10 h-5 rounded-full transition {{ $waVal === '1' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                                    </div>
                                    <div
                                        class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform {{ $waVal === '1' ? 'translate-x-5' : 'translate-x-0' }}">
                                    </div>
                                </div>
                            </div>
                            {{-- Email toggle --}}
                            <div class="flex justify-center">
                                @php
                                    $emailKey = 'notif_' . $ev['clave'] . '_email';
                                    $emailVal = $this->{$emailKey};
                                @endphp
                                <div class="relative cursor-pointer"
                                    wire:click="$set('{{ $emailKey }}', '{{ $emailVal === '1' ? '0' : '1' }}')">
                                    <div
                                        class="w-10 h-5 rounded-full transition {{ $emailVal === '1' ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                                    </div>
                                    <div
                                        class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform {{ $emailVal === '1' ? 'translate-x-5' : 'translate-x-0' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarNotificaciones" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarNotificaciones">Guardar preferencias</span>
                        <span wire:loading wire:target="guardarNotificaciones">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: MATRÍCULA
             ================================================================ --}}
        @if ($tab === 'matricula')
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <h3
                    class="text-base font-bold text-gray-800 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 pb-3">
                    Parámetros de Matrícula
                </h3>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Configura los valores económicos que se aplican automáticamente al momento de crear matrículas.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- Valor de inscripción --}}
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-amber-100 dark:bg-amber-800/40 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Valor de Inscripción</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400">Solo se cobra en la primera matrícula del estudiante</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                Monto en USD <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-500 text-sm">$</span>
                                <input type="number" wire:model="matricula_valor_inscripcion"
                                    step="0.01" min="0" max="9999.99"
                                    class="w-full pl-7 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm"
                                    placeholder="10.00">
                            </div>
                            @error('matricula_valor_inscripcion')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Porcentaje arrastre --}}
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-orange-100 dark:bg-orange-800/40 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-orange-800 dark:text-orange-300">Penalización por Arrastre</p>
                                <p class="text-xs text-orange-600 dark:text-orange-400">Porcentaje adicional sobre el costo de materias arrastradas</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                Porcentaje (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="matricula_porcentaje_arrastre"
                                    step="0.01" min="0" max="100"
                                    class="w-full pr-8 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm"
                                    placeholder="10">
                                <span class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm">%</span>
                            </div>
                            @error('matricula_porcentaje_arrastre')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50 p-3 text-xs text-blue-700 dark:text-blue-400">
                    <strong>Nota:</strong> El valor de inscripción genera una obligación financiera separada de tipo
                    <code class="bg-blue-100 dark:bg-blue-900/50 px-1 rounded">INSCRIPCION</code> al crear la primera matrícula de cada estudiante.
                    El porcentaje de arrastre se aplica al costo por crédito de cada materia pendiente.
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarMatricula" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarMatricula">Guardar parámetros</span>
                        <span wire:loading wire:target="guardarMatricula">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: API CÉDULA
             ================================================================ --}}
        @if ($tab === 'cedula_api')
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Consulta de Cédula</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Configure el servicio externo para consulta automática de datos a partir del número de cédula.
                        El token se almacena cifrado en la base de datos.
                    </p>
                </div>

                {{-- Info box --}}
                <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                        Al crear un usuario, el botón <strong>Consultar</strong> junto al campo de cédula
                        llamará a esta API para obtener y prellenar automáticamente: nombres, apellidos,
                        género, estado civil, fecha de nacimiento, nacionalidad y datos de padres.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5">

                    {{-- URL base --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
                            URL Base de la API
                        </label>
                        <input type="url" wire:model="cedula_api_url"
                               placeholder="https://apicedula.socket-studio.com/consulta-cedula/consulta/"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm
                                      text-gray-900 dark:text-gray-100 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
                        <p class="mt-1 text-xs text-gray-400">La cédula se agrega al final de la URL automáticamente.</p>
                        @error('cedula_api_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Token --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
                            Token de Autorización (Bearer)
                            <span class="ml-1 font-normal text-gray-400 normal-case">— se guarda cifrado</span>
                        </label>
                        <textarea wire:model="cedula_api_token" rows="3"
                                  placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                                  class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                         bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-xs font-mono
                                         text-gray-900 dark:text-gray-100 placeholder-gray-400
                                         focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all resize-none"></textarea>
                        <p class="mt-1 text-xs text-gray-400">Pega aquí el JWT completo. No incluyas el prefijo "Bearer".</p>
                        @error('cedula_api_token') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarCedulaApi" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarCedulaApi">Guardar configuración</span>
                        <span wire:loading wire:target="guardarCedulaApi">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- ================================================================
             TAB: MOODLE (solo visible si MOODLE_MODE=1 en .env)
             ================================================================ --}}
        @if($tab === 'moodle' && (int) env('MOODLE_MODE', 0) === 1)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-5">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Integración con Moodle</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Configura el campus virtual. El token y la URL provienen del servicio externo creado en Moodle.
                        </p>
                    </div>
                    {{-- Indicador de estado --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                 {{ $moodle_activo === '1' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $moodle_activo === '1' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ $moodle_activo === '1' ? 'Integración activa' : 'Desactivada' }}
                    </span>
                </div>

                {{-- Activar integración --}}
                <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Activar integración Moodle</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Habilita la creación automática de usuarios y gestión desde esta plataforma.
                        </p>
                    </div>
                    <button wire:click="$set('moodle_activo', '{{ $moodle_activo === '1' ? '0' : '1' }}')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                   {{ $moodle_activo === '1' ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                     {{ $moodle_activo === '1' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                {{-- URL del campus --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
                        URL del Campus Moodle
                    </label>
                    <input type="url" wire:model="moodle_url" placeholder="https://elearning.miinstituto.edu.ec"
                           class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                  text-gray-900 dark:text-gray-100 px-4 py-2.5 text-sm
                                  focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition">
                    @error('moodle_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Token --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">
                        Token API REST
                    </label>
                    <input type="password" wire:model="moodle_token" placeholder="Token del servicio externo en Moodle"
                           autocomplete="new-password"
                           class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                  text-gray-900 dark:text-gray-100 px-4 py-2.5 text-sm font-mono
                                  focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 outline-none transition">
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        Obtenido en Moodle: Administración → Plugins → Servicios web → Gestionar tokens.
                    </p>
                    @error('moodle_token')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info funciones configuradas --}}
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-700 dark:text-emerald-400 space-y-1">
                    <p class="font-semibold">Funciones configuradas en el servicio externo:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-emerald-600 dark:text-emerald-500 font-mono">
                        <li>core_user_create_users</li>
                        <li>core_user_update_users</li>
                        <li>core_user_get_users_by_field</li>
                    </ul>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="guardarMoodle" wire:loading.attr="disabled"
                        class="px-6 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardarMoodle">Guardar configuración</span>
                        <span wire:loading wire:target="guardarMoodle">Guardando...</span>
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
