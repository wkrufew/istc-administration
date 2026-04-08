{{-- <x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Dashboard Administrativo
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Resumen general del instituto, estudiantes, docentes y finanzas.
            </p>
        </div>
    </x-slot>

    <section class="w-full py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div
                    class="group rounded-2xl border border-gray-200 bg-white/70 backdrop-blur-xl shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Roles registrados</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">6</p>
                            <p class="text-xs text-gray-400 mt-1">Control de permisos y accesos</p>
                        </div>

                        <div
                            class="h-12 w-12 rounded-2xl bg-blue-600/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                            </svg>
                        </div>
                    </div>
                </div>

                
                <div
                    class="group rounded-2xl border border-gray-200 bg-white/70 backdrop-blur-xl shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Usuarios registrados</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">120</p>
                            <p class="text-xs text-gray-400 mt-1">Admins, docentes y estudiantes</p>
                        </div>

                        <div
                            class="h-12 w-12 rounded-2xl bg-emerald-600/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M16 11c1.657 0 3-1.791 3-4s-1.343-4-3-4-3 1.791-3 4 1.343 4 3 4zM8 11c1.657 0 3-1.791 3-4S9.657 3 8 3 5 4.791 5 7s1.343 4 3 4zm0 2c-2.67 0-8 1.34-8 4v3h10v-3c0-1.13.39-2.17 1.05-3.02C10.1 13.41 9.1 13 8 13zm8 0c-1.1 0-2.1.41-3.05.98.66.85 1.05 1.89 1.05 3.02v3h10v-3c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                    </div>
                </div>

               
                <div
                    class="group rounded-2xl border border-gray-200 bg-white/70 backdrop-blur-xl shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Carreras activas</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">10</p>
                            <p class="text-xs text-gray-400 mt-1">Programas disponibles</p>
                        </div>

                        <div
                            class="h-12 w-12 rounded-2xl bg-violet-600/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 13L3 10v8l9 4 9-4v-8l-9 5z" />
                            </svg>
                        </div>
                    </div>
                </div>

             
                <div
                    class="group rounded-2xl border border-gray-200 bg-white/70 backdrop-blur-xl shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Materias registradas</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">50</p>
                            <p class="text-xs text-gray-400 mt-1">Asignaturas totales</p>
                        </div>

                        <div
                            class="h-12 w-12 rounded-2xl bg-cyan-600/10 flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M4 4h16v2H4V4zm0 6h16v2H4v-2zm0 6h10v2H4v-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>


            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                
                <div
                    class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Estudiantes registrados</p>
                            <span
                                class="text-xs px-2 py-1 rounded-full bg-emerald-600/10 text-emerald-700 dark:text-emerald-400">
                                Activos
                            </span>
                        </div>

                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">50</p>

                        <div class="mt-4 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <p>• Nuevos este mes: <span class="font-semibold text-gray-800 dark:text-white">+8</span>
                            </p>
                            <p>• Retirados: <span class="font-semibold text-gray-800 dark:text-white">2</span></p>
                            <p>• Morosos: <span class="font-semibold text-red-600">5</span></p>
                        </div>
                    </div>
                </div>

              
                <div
                    class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Docentes registrados</p>
                            <span
                                class="text-xs px-2 py-1 rounded-full bg-blue-600/10 text-blue-700 dark:text-blue-400">
                                Disponibles
                            </span>
                        </div>

                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">15</p>

                        <div class="mt-4 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <p>• Activos: <span class="font-semibold text-gray-800 dark:text-white">12</span></p>
                            <p>• Inactivos: <span class="font-semibold text-gray-800 dark:text-white">3</span></p>
                            <p>• Carga académica alta: <span class="font-semibold text-orange-600">4</span></p>
                        </div>
                    </div>
                </div>

                
                <div
                    class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pagos pendientes</p>
                            <span class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-700 dark:text-red-400">
                                Alerta
                            </span>
                        </div>

                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">20</p>

                        <div class="mt-4 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <p>• Pagos vencidos: <span class="font-semibold text-red-600">9</span></p>
                            <p>• Próximos a vencer: <span class="font-semibold text-orange-600">6</span></p>
                            <p>• En revisión: <span class="font-semibold text-gray-800 dark:text-white">5</span></p>
                        </div>
                    </div>
                </div>

                
                <div
                    class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ingresos del mes</p>
                            <span
                                class="text-xs px-2 py-1 rounded-full bg-emerald-600/10 text-emerald-700 dark:text-emerald-400">
                                OK
                            </span>
                        </div>

                        <p class="text-3xl font-bold mt-2 text-gray-900 dark:text-white">$ 500.36</p>

                        <div class="mt-4 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <p>• Meta mensual: <span class="font-semibold text-gray-800 dark:text-white">$ 1,000</span>
                            </p>
                            <p>• Progreso: <span class="font-semibold text-emerald-600">50%</span></p>
                            <p>• Promedio diario: <span class="font-semibold text-gray-800 dark:text-white">$
                                    16.67</span></p>
                        </div>
                    </div>
                </div>

            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Acciones rápidas
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Accesos directos para tareas frecuentes.
                        </p>

                        <div class="mt-6 grid grid-cols-1 gap-3">
                            <a href="#"
                                class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">➕ Registrar
                                    estudiante</span>
                                <span class="text-xs text-gray-400">Nuevo</span>
                            </a>

                            <a href="#"
                                class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">📌 Crear carrera</span>
                                <span class="text-xs text-gray-400">Gestión</span>
                            </a>

                            <a href="#"
                                class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">📚 Asignar
                                    materias</span>
                                <span class="text-xs text-gray-400">Académico</span>
                            </a>

                            <a href="#"
                                class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition dark:border-gray-800 dark:hover:bg-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">💳 Revisar pagos</span>
                                <span class="text-xs text-red-500 font-semibold">Urgente</span>
                            </a>
                        </div>
                    </div>
                </div>


              
                <div
                    class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Últimos estudiantes registrados
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Aquí luego cargas desde la base.
                            </p>
                        </div>

                        <a href="#"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Ver todos →
                        </a>
                    </div>

                    <div class="px-6 pb-6 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-800">
                                    <th class="py-3">Nombre</th>
                                    <th class="py-3">Carrera</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-right">Acción</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y dark:divide-gray-800">
                                <tr>
                                    <td class="py-3 font-medium text-gray-900 dark:text-white">Juan Pérez</td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400">Desarrollo de Software</td>
                                    <td class="py-3">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-emerald-600/10 text-emerald-700 dark:text-emerald-400">
                                            Activo
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="#"
                                            class="text-blue-600 hover:underline dark:text-blue-400">Ver</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="py-3 font-medium text-gray-900 dark:text-white">María López</td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400">Contabilidad</td>
                                    <td class="py-3">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-orange-600/10 text-orange-700 dark:text-orange-400">
                                            Pendiente pago
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="#"
                                            class="text-blue-600 hover:underline dark:text-blue-400">Ver</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="py-3 font-medium text-gray-900 dark:text-white">Carlos Díaz</td>
                                    <td class="py-3 text-gray-500 dark:text-gray-400">Administración</td>
                                    <td class="py-3">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-red-600/10 text-red-700 dark:text-red-400">
                                            Moroso
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="#"
                                            class="text-blue-600 hover:underline dark:text-blue-400">Ver</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>



            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Estado académico
                        </h3>

                        <div class="mt-5 space-y-4">
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Matrículas completadas</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">72%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mt-2">
                                    <div class="bg-emerald-600 h-2 rounded-full w-[72%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Pagos al día</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">58%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mt-2">
                                    <div class="bg-blue-600 h-2 rounded-full w-[58%]"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Aprobación general</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">80%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mt-2">
                                    <div class="bg-violet-600 h-2 rounded-full w-[80%]"></div>
                                </div>
                            </div>
                        </div>

                        <p class="mt-5 text-xs text-gray-400">
                            (Estos porcentajes son placeholders para luego conectarlos a DB)
                        </p>
                    </div>
                </div>


                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Alertas del sistema
                        </h3>

                        <div class="mt-5 space-y-3 text-sm">
                            <div class="p-3 rounded-xl bg-red-600/10 text-red-700 dark:text-red-400">
                                ⚠️ 9 pagos vencidos requieren atención.
                            </div>

                            <div class="p-3 rounded-xl bg-orange-600/10 text-orange-700 dark:text-orange-400">
                                ⏳ 6 matrículas pendientes de confirmación.
                            </div>

                            <div class="p-3 rounded-xl bg-blue-600/10 text-blue-700 dark:text-blue-400">
                                📌 3 docentes aún no han actualizado su perfil.
                            </div>
                        </div>
                    </div>
                </div>


                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Instituto
                        </h3>

                        <div class="mt-5 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <p><span class="font-semibold text-gray-900 dark:text-white">Nombre:</span> ISTC</p>
                            <p><span class="font-semibold text-gray-900 dark:text-white">Período:</span> 2025 - 2026
                            </p>
                            <p><span class="font-semibold text-gray-900 dark:text-white">Modalidad:</span> Presencial /
                                Online</p>
                            <p><span class="font-semibold text-gray-900 dark:text-white">Sede:</span> Principal</p>
                        </div>

                        <a href="#"
                            class="mt-6 inline-flex items-center justify-center w-full px-4 py-3 rounded-xl bg-gray-900 text-white hover:bg-black transition dark:bg-white dark:text-gray-900">
                            Configurar instituto
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>
</x-admin-layout> --}}

<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                    Panel de Administración
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    Resumen general del instituto, métricas y alertas.
                </p>
            </div>


            <div class="flex gap-2">
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                    bg-sky-600 hover:bg-sky-700 text-white shadow-sm transition">
                    + Nuevo estudiante
                </a>

                <a href="#"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                    bg-white dark:bg-[#111827]
                    border border-slate-200 dark:border-slate-800
                    text-slate-700 dark:text-slate-200
                    hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Ver reportes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        @livewire('administration.dashboard-principal')
        {{-- <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            <div class="xl:col-span-2 space-y-6">


                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">


                    <div
                        class="rounded-2xl p-5 bg-white dark:bg-[#0B1220]
                        border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-300">Estudiantes</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">1,250</p>
                            </div>

                            <div class="h-10 w-10 rounded-xl bg-sky-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 fill-sky-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512">
                                    <path
                                        d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM609.3 512H471.7c5.6-9.5 8.3-20.6 8.3-32c0-54.3-21.1-103.6-55.5-140.5c6.1-1.5 12.5-2.3 19.2-2.3h61.4C575.2 337.2 640 402 640 482.3c0 16.4-13.3 29.7-29.7 29.7zM432 128a96 96 0 1 1 192 0a96 96 0 1 1 -192 0z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                            ▲ +12% este mes
                        </p>
                    </div>


                    <div
                        class="rounded-2xl p-5 bg-white dark:bg-[#0B1220]
                        border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-300">Docentes</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">68</p>
                            </div>

                            <div class="h-10 w-10 rounded-xl bg-violet-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 fill-violet-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512">
                                    <path
                                        d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM609.3 512H471.7c5.6-9.5 8.3-20.6 8.3-32c0-54.3-21.1-103.6-55.5-140.5c6.1-1.5 12.5-2.3 19.2-2.3h61.4C575.2 337.2 640 402 640 482.3c0 16.4-13.3 29.7-29.7 29.7zM432 128a96 96 0 1 1 192 0a96 96 0 1 1 -192 0z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            Activos en el periodo actual
                        </p>
                    </div>


                    <div
                        class="rounded-2xl p-5 bg-white dark:bg-[#0B1220]
                        border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-300">Carreras</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">12</p>
                            </div>

                            <div class="h-10 w-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 fill-amber-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512">
                                    <path
                                        d="M184 48H328c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zM0 128H512v48H0V128zM64 208H448V464c0 26.5-21.5 48-48 48H112c-26.5 0-48-21.5-48-48V208z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            Planes disponibles
                        </p>
                    </div>


                    <div
                        class="rounded-2xl p-5 bg-white dark:bg-[#0B1220]
                        border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-300">Materias</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">210</p>
                            </div>

                            <div class="h-10 w-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 fill-emerald-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512">
                                    <path
                                        d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H416c53 0 96-43 96-96V96c0-53-43-96-96-96H96zM96 128c0-17.7 14.3-32 32-32H384c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32-14.3-32-32zm0 96c0-17.7 14.3-32 32-32H384c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32-14.3-32-32zm0 96c0-17.7 14.3-32 32-32H256c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32-14.3-32-32z" />
                                </svg>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            Incluye todas las carreras
                        </p>
                    </div>

                </div>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div
                        class="lg:col-span-2 rounded-2xl p-6 bg-gradient-to-br from-sky-600 to-indigo-600 text-white shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-white/80">Recaudación del mes</p>
                                <p class="mt-2 text-3xl font-extrabold">$ 8,540.36</p>
                                <p class="mt-2 text-sm text-white/80">
                                    Incluye matrículas, pagos parciales y pagos completos.
                                </p>
                            </div>

                            <div class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center">
                                <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512">
                                    <path
                                        d="M64 64C28.7 64 0 92.7 0 128v256c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm48 144c0-26.5 21.5-48 48-48H416c26.5 0 48 21.5 48 48s-21.5 48-48 48H160c-26.5 0-48-21.5-48-48z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-xl bg-white/10 p-3">
                                <p class="text-xs text-white/70">Pagos pendientes</p>
                                <p class="text-lg font-bold">20</p>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <p class="text-xs text-white/70">Pagos hoy</p>
                                <p class="text-lg font-bold">6</p>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <p class="text-xs text-white/70">Mora</p>
                                <p class="text-lg font-bold">$ 1,200</p>
                            </div>
                        </div>
                    </div>


                    <div
                        class="rounded-2xl p-6 bg-white dark:bg-[#0B1220]
                        border border-slate-200 dark:border-slate-800 shadow-sm">
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">
                            Alertas del sistema
                        </h3>

                        <div class="mt-4 space-y-3">
                            <div
                                class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-500/10 dark:border-amber-500/20 p-3">
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                                    ⚠️ 5 estudiantes con pagos vencidos
                                </p>
                                <p class="text-xs text-amber-700/80 dark:text-amber-200/70">
                                    Revisar módulo de pagos pendientes.
                                </p>
                            </div>

                            <div
                                class="rounded-xl border border-sky-200 bg-sky-50 dark:bg-sky-500/10 dark:border-sky-500/20 p-3">
                                <p class="text-sm font-semibold text-sky-800 dark:text-sky-300">
                                    ℹ️ 2 matrículas nuevas hoy
                                </p>
                                <p class="text-xs text-sky-700/80 dark:text-sky-200/70">
                                    Ver listado de estudiantes recientes.
                                </p>
                            </div>

                            <div
                                class="rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-500/10 dark:border-rose-500/20 p-3">
                                <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">
                                    ❗ 1 docente sin materias asignadas
                                </p>
                                <p class="text-xs text-rose-700/80 dark:text-rose-200/70">
                                    Asignar carga académica.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <div
                    class="rounded-2xl bg-white dark:bg-[#0B1220]
                    border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                Últimos estudiantes registrados
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-300">
                                Registro reciente para control administrativo.
                            </p>
                        </div>

                        <a href="#"
                            class="text-sm font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 transition">
                            Ver todos →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-[#111827] text-slate-600 dark:text-slate-300">
                                <tr>
                                    <th class="text-left px-6 py-3 font-semibold">Estudiante</th>
                                    <th class="text-left px-6 py-3 font-semibold">Carrera</th>
                                    <th class="text-left px-6 py-3 font-semibold">Estado</th>
                                    <th class="text-left px-6 py-3 font-semibold">Registro</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach ([1, 2, 3, 4, 5] as $i)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-9 w-9 rounded-xl bg-sky-500/10 flex items-center justify-center">
                                                    <span class="text-sky-600 dark:text-sky-400 font-bold">
                                                        A
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-slate-900 dark:text-white">
                                                        Alumno {{ $i }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                                        CI: 010203040{{ $i }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-700 dark:text-slate-200">
                                            Desarrollo de Software
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                Activo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                            Hoy
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            <div class="space-y-6">


                <div
                    class="rounded-2xl p-6 bg-white dark:bg-[#0B1220]
                    border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        Estado del Instituto
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500 dark:text-slate-300">Periodo activo</p>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                2026-A
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500 dark:text-slate-300">Matrículas abiertas</p>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                Sí
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500 dark:text-slate-300">Carreras activas</p>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                10
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500 dark:text-slate-300">Paralelos creados</p>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                35
                            </span>
                        </div>
                    </div>
                </div>


                <div
                    class="rounded-2xl p-6 bg-white dark:bg-[#0B1220]
                    border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        Acciones rápidas
                    </h3>

                    <div class="mt-4 grid grid-cols-1 gap-3">
                        <a href="#"
                            class="rounded-xl px-4 py-3 text-sm font-semibold
                            bg-slate-100 dark:bg-[#111827]
                            text-slate-700 dark:text-slate-200
                            hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            📌 Crear carrera
                        </a>

                        <a href="#"
                            class="rounded-xl px-4 py-3 text-sm font-semibold
                            bg-slate-100 dark:bg-[#111827]
                            text-slate-700 dark:text-slate-200
                            hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            📚 Registrar materia
                        </a>

                        <a href="#"
                            class="rounded-xl px-4 py-3 text-sm font-semibold
                            bg-slate-100 dark:bg-[#111827]
                            text-slate-700 dark:text-slate-200
                            hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            👨‍🏫 Asignar docente
                        </a>

                        <a href="#"
                            class="rounded-xl px-4 py-3 text-sm font-semibold
                            bg-slate-100 dark:bg-[#111827]
                            text-slate-700 dark:text-slate-200
                            hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                            💳 Ver pagos pendientes
                        </a>
                    </div>
                </div>


                <div
                    class="rounded-2xl p-6 bg-white dark:bg-[#0B1220]
                    border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        Actividad reciente
                    </h3>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">✓</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    Matrícula registrada
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Hace 10 minutos
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 rounded-xl bg-sky-500/10 flex items-center justify-center">
                                <span class="text-sky-600 dark:text-sky-400 font-bold">i</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    Nuevo docente agregado
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Hace 2 horas
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 rounded-xl bg-amber-500/10 flex items-center justify-center">
                                <span class="text-amber-600 dark:text-amber-400 font-bold">!</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    3 pagos marcados como pendientes
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Ayer
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div> --}}
    </div>
</x-admin-layout>
