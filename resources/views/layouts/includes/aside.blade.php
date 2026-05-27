@php
    $nombreCorto = \App\Services\SettingService::get('instituto.nombre_corto') ?: config('app.name', 'ISTCumandá');
    $asideLogo   = \App\Services\SettingService::get('instituto.logo_path');
    $asideLogoSrc = $asideLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($asideLogo)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($asideLogo)
        : asset('../imagenes/icono.webp');

    $solicitudesPendientes   = \App\Models\Solicitud::where('estado', 'pendiente')->count();
    $pagosPendientesVerif    = \App\Models\Pago::where('estado', \App\Models\Pago::ESTADO_PENDIENTE)->count();

    $navItems = [
        ['type' => 'section', 'label' => 'Sistema'],

        ['route' => 'administracion.administrativa.dashboard',      'label' => 'Dashboard',
         'active' => 'administracion.administrativa.dashboard',     'viewBox' => '0 0 512 512',
         'svg' => '<path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64L0 400c0 44.2 35.8 80 80 80l400 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 416c-8.8 0-16-7.2-16-16L64 64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 210.7l-57.4-57.4c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L240 221.3l57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l128-128z"/>'],

        ['route' => 'administracion.administrativa.settings',       'label' => 'Configuración',
         'active' => 'administracion.administrativa.settings',      'permission' => 'gestionar_configuracion', 'viewBox' => '0 0 24 24',
         'svg' => '<path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd"/>'],

        ['route' => 'administracion.administrativa.auditoria.index', 'label' => 'Auditoría',
         'active' => 'administracion.administrativa.auditoria.*',    'permission' => 'gestionar_auditorias', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M267 48C230.6 48 209.2 106.3 198.7 160L168 160C154.7 160 144 170.7 144 184C144 197.3 154.7 208 168 208L192 208L192 240C192 257 195.3 273.2 201.3 288L192 288L171.5 288C156.3 288 144 300.3 144 315.5C144 318.5 144.5 321.4 145.4 324.2L174.3 410.8C136.2 443.6 112 492.1 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 492.1 503.8 443.6 465.7 410.9L494.6 324.3C495.5 321.5 496 318.6 496 315.6C496 300.4 483.7 288.1 468.5 288.1L448 288.1L438.7 288.1C444.7 273.3 448 257.1 448 240.1L448 208.1L472 208.1C485.3 208.1 496 197.4 496 184.1C496 170.8 485.3 160.1 472 160.1L441.3 160.1C430.9 106.4 409.4 48.1 373 48.1C363.4 48.1 354 52 345.5 56.3C337.3 60.4 327.1 64.1 320 64.1C312.9 64.1 302.7 60.4 294.5 56.3C286 51.9 276.6 48 267 48zM360.7 532.4L335.9 461.5L363.8 429C366.5 425.8 368 421.8 368 417.6C368 407.9 360.2 400.1 350.5 400.1L289.5 400.1C279.8 400.1 272 407.9 272 417.6C272 421.8 273.5 425.8 276.2 429L304.1 461.5L279.3 532.4L222.3 352L258 352C276.4 362.2 297.5 368 320 368C342.5 368 363.6 362.2 382 352L417.7 352L360.7 532.4zM320 320C285.3 320 255.8 297.9 244.7 267C250.4 270.2 257 272 264 272L276.4 272C292.9 272 307.5 261.4 312.7 245.8C315 238.8 324.9 238.8 327.2 245.8C332.4 261.4 347.1 272 363.5 272L375.9 272C382.9 272 389.5 270.2 395.2 267C384.1 297.9 354.6 320 319.9 320z"/>'],

        ['route' => 'administracion.administrativa.tickets.index',  'label' => 'Tickets',
         'active' => 'administracion.administrativa.tickets.*',     'permission' => 'ver_tickets', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M96 128C60.7 128 32 156.7 32 192L32 256C32 264.8 39.4 271.7 47.7 274.6C66.5 281.1 80 299 80 320C80 341 66.5 358.9 47.7 365.4C39.4 368.3 32 375.2 32 384L32 448C32 483.3 60.7 512 96 512L544 512C579.3 512 608 483.3 608 448L608 384C608 375.2 600.6 368.3 592.3 365.4C573.5 358.9 560 341 560 320C560 299 573.5 281.1 592.3 274.6C600.6 271.7 608 264.8 608 256L608 192C608 156.7 579.3 128 544 128L96 128zM448 400L448 240L192 240L192 400L448 400zM144 224C144 206.3 158.3 192 176 192L464 192C481.7 192 496 206.3 496 224L496 416C496 433.7 481.7 448 464 448L176 448C158.3 448 144 433.7 144 416L144 224z"/>'],

        ['type' => 'section', 'label' => 'Usuarios'],

        ['route' => 'administracion.administrativa.roles.index',    'label' => 'Roles y Permisos',
         'active' => 'administracion.administrativa.roles.*',       'permission' => 'asignar_roles', 'viewBox' => '0 0 640 512',
         'svg' => '<path d="M256 64l128 0 0 64-128 0 0-64zM240 0c-26.5 0-48 21.5-48 48l0 96c0 26.5 21.5 48 48 48l48 0 0 32L32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l96 0 0 32-48 0c-26.5 0-48 21.5-48 48l0 96c0 26.5 21.5 48 48 48l160 0c26.5 0 48-21.5 48-48l0-96c0-26.5-21.5-48-48-48l-48 0 0-32 256 0 0 32-48 0c-26.5 0-48 21.5-48 48l0 96c0 26.5 21.5 48 48 48l160 0c26.5 0 48-21.5 48-48l0-96c0-26.5-21.5-48-48-48l-48 0 0-32 96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-256 0 0-32 48 0c26.5 0 48-21.5 48-48l0-96c0-26.5-21.5-48-48-48L240 0zM96 448l0-64 128 0 0 64L96 448zm320-64l128 0 0 64-128 0 0-64z"/>'],

        ['route' => 'administracion.administrativa.users.index',    'label' => 'Usuarios',
         'active' => 'administracion.administrativa.users.*',       'permission' => 'gestionar_usuarios', 'viewBox' => '0 0 640 512',
         'svg' => '<path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z"/>'],

        ['route' => 'administracion.administrativa.docentes.index', 'label' => 'Docentes',
         'active' => 'administracion.administrativa.docentes.*',    'permission' => 'gestionar_docentes', 'viewBox' => '0 0 640 512',
         'svg' => '<path d="M192 96a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm-8 384l0-128 16 0 0 128c0 17.7 14.3 32 32 32s32-14.3 32-32l0-288 56 0 64 0 16 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-16 0 0-64 192 0 0 192-192 0 0-32-64 0 0 48c0 26.5 21.5 48 48 48l224 0c26.5 0 48-21.5 48-48l0-224c0-26.5-21.5-48-48-48L368 0c-26.5 0-48 21.5-48 48l0 80-76.9 0-65.9 0c-33.7 0-64.9 17.7-82.3 46.6l-58.3 97c-9.1 15.1-4.2 34.8 10.9 43.9s34.8 4.2 43.9-10.9L120 256.9 120 480c0 17.7 14.3 32 32 32s32-14.3 32-32z"/>'],

        ['route' => 'administracion.administrativa.estudiantes.index', 'label' => 'Estudiantes',
         'active' => 'administracion.administrativa.estudiantes.*',    'permission' => 'gestionar_estudiantes', 'viewBox' => '0 0 448 512',
         'svg' => '<path d="M219.3 .5c3.1-.6 6.3-.6 9.4 0l200 40C439.9 42.7 448 52.6 448 64s-8.1 21.3-19.3 23.5L352 102.9l0 57.1c0 70.7-57.3 128-128 128s-128-57.3-128-128l0-57.1L48 93.3l0 65.1 15.7 78.4c.9 4.7-.3 9.6-3.3 13.3s-7.6 5.9-12.4 5.9l-32 0c-4.8 0-9.3-2.1-12.4-5.9s-4.3-8.6-3.3-13.3L16 158.4l0-71.8C6.5 83.3 0 74.3 0 64C0 52.6 8.1 42.7 19.3 40.5l200-40zM111.9 327.7c10.5-3.4 21.8 .4 29.4 8.5l71 75.5c6.3 6.7 17 6.7 23.3 0l71-75.5c7.6-8.1 18.9-11.9 29.4-8.5C401 348.6 448 409.4 448 481.3c0 17-13.8 30.7-30.7 30.7L30.7 512C13.8 512 0 498.2 0 481.3c0-71.9 47-132.7 111.9-153.6z"/>'],

        ['type' => 'section', 'label' => 'Académico'],

        ['route' => 'administracion.administrativa.periodos.index', 'label' => 'Periodos',
         'active' => 'administracion.administrativa.periodos.*',    'permission' => 'gestionar_periodos', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M264 112L376 112C380.4 112 384 115.6 384 120L384 160L256 160L256 120C256 115.6 259.6 112 264 112zM208 120L208 160L128 160C92.7 160 64 188.7 64 224L64 320L369 320C402.8 290.1 447.3 272 496 272C524.6 272 551.6 278.2 576 289.4L576 224C576 188.7 547.3 160 512 160L432 160L432 120C432 89.1 406.9 64 376 64L264 64C233.1 64 208 89.1 208 120zM288 416C270.3 416 256 401.7 256 384L256 368L64 368L64 480C64 515.3 92.7 544 128 544L321.4 544C310.2 519.6 304 492.6 304 464C304 447.4 306.1 431.3 310 416L288 416zM640 464C640 384.5 575.5 320 496 320C416.5 320 352 384.5 352 464C352 543.5 416.5 608 496 608C575.5 608 640 543.5 640 464zM496 384C504.8 384 512 391.2 512 400L512 448L544 448C552.8 448 560 455.2 560 464C560 472.8 552.8 480 544 480L496 480C487.2 480 480 472.8 480 464L480 400C480 391.2 487.2 384 496 384z"/>'],

        ['route' => 'administracion.administrativa.carreras.index', 'label' => 'Carreras',
         'active' => 'administracion.administrativa.carreras.*',    'permission' => 'gestionar_carreras', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M296.5 69.2C311.4 62.3 328.6 62.3 343.5 69.2L562.1 170.2C570.6 174.1 576 182.6 576 192C576 201.4 570.6 209.9 562.1 213.8L343.5 314.8C328.6 321.7 311.4 321.7 296.5 314.8L77.9 213.8C69.4 209.8 64 201.3 64 192C64 182.7 69.4 174.1 77.9 170.2L296.5 69.2zM112.1 282.4L276.4 358.3C304.1 371.1 336 371.1 363.7 358.3L528 282.4L562.1 298.2C570.6 302.1 576 310.6 576 320C576 329.4 570.6 337.9 562.1 341.8L343.5 442.8C328.6 449.7 311.4 449.7 296.5 442.8L77.9 341.8C69.4 337.8 64 329.3 64 320C64 310.7 69.4 302.1 77.9 298.2L112 282.4zM77.9 426.2L112 410.4L276.3 486.3C304 499.1 335.9 499.1 363.6 486.3L527.9 410.4L562 426.2C570.5 430.1 575.9 438.6 575.9 448C575.9 457.4 570.5 465.9 562 469.8L343.4 570.8C328.5 577.7 311.3 577.7 296.4 570.8L77.9 469.8C69.4 465.8 64 457.3 64 448C64 438.7 69.4 430.1 77.9 426.2z"/>'],

        ['route' => 'administracion.administrativa.semestres.index', 'label' => 'Semestres',
         'active' => 'administracion.administrativa.semestres.*',   'permission' => 'gestionar_semestres', 'viewBox' => '0 0 576 512',
         'svg' => '<path d="M384 64c0-17.7 14.3-32 32-32l128 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-96 0 0 96c0 17.7-14.3 32-32 32l-96 0 0 96c0 17.7-14.3 32-32 32l-96 0 0 96c0 17.7-14.3 32-32 32L32 480c-17.7 0-32-14.3-32-32s14.3-32 32-32l96 0 0-96c0-17.7 14.3-32 32-32l96 0 0-96c0-17.7 14.3-32 32-32l96 0 0-96z"/>'],

        ['route' => 'administracion.administrativa.materias.index', 'label' => 'Materias',
         'active' => 'administracion.administrativa.materias.*',    'permission' => 'gestionar_materias', 'viewBox' => '0 0 576 512',
         'svg' => '<path d="M249.6 471.5c10.8 3.8 22.4-4.1 22.4-15.5l0-377.4c0-4.2-1.6-8.4-5-11C247.4 52 202.4 32 144 32C93.5 32 46.3 45.3 18.1 56.1C6.8 60.5 0 71.7 0 83.8L0 454.1c0 11.9 12.8 20.2 24.1 16.5C55.6 460.1 105.5 448 144 448c33.9 0 79 14 105.6 23.5zm76.8 0C353 462 398.1 448 432 448c38.5 0 88.4 12.1 119.9 22.6c11.3 3.8 24.1-4.6 24.1-16.5l0-370.3c0-12.1-6.8-23.3-18.1-27.6C529.7 45.3 482.5 32 432 32c-58.4 0-103.4 20-123 35.6c-3.3 2.6-5 6.8-5 11L304 456c0 11.4 11.7 19.3 22.4 15.5z"/>'],

        ['route' => 'administracion.administrativa.paralelos.index', 'label' => 'Paralelos',
         'active' => 'administracion.administrativa.paralelos.*',   'permission' => 'gestionar_paralelos', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M349.5 115.7C344.6 103.8 332.9 96 320 96C307.1 96 295.4 103.8 290.5 115.7C197.2 339.7 143.8 467.7 130.5 499.7C123.7 516 131.4 534.7 147.7 541.5C164 548.3 182.7 540.6 189.5 524.3L221.3 448L418.6 448L450.4 524.3C457.2 540.6 475.9 548.3 492.2 541.5C508.5 534.7 516.2 516 509.4 499.7C496.1 467.7 442.7 339.7 349.4 115.7zM392 384L248 384L320 211.2L392 384z"/>'],

        ['route' => 'administracion.administrativa.horarios.index', 'label' => 'Horarios',
         'active' => 'administracion.administrativa.horarios.*',    'permission' => 'gestionar_horarios', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64zM296 184L296 320C296 328 300 335.5 306.7 340L402.7 404C413.7 411.4 428.6 408.4 436 397.3C443.4 386.2 440.4 371.4 429.3 364L344 307.2L344 184C344 170.7 333.3 160 320 160C306.7 160 296 170.7 296 184z"/>'],

        ['route' => 'administracion.administrativa.materia_periodo_paralelo.index', 'label' => 'Módulos · Periodos',
         'active' => 'administracion.administrativa.materia_periodo_paralelo.*', 'permission' => 'gestionar_modulos_academicos', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M64 144C64 117.5 85.5 96 112 96L208 96C234.5 96 256 117.5 256 144L256 160L384 160L384 144C384 117.5 405.5 96 432 96L528 96C554.5 96 576 117.5 576 144L576 240C576 266.5 554.5 288 528 288L432 288C405.5 288 384 266.5 384 240L384 224L256 224L256 240C256 247.3 254.3 254.3 251.4 260.5L320 352L400 352C426.5 352 448 373.5 448 400L448 496C448 522.5 426.5 544 400 544L304 544C277.5 544 256 522.5 256 496L256 400C256 392.7 257.7 385.7 260.6 379.5L192 288L112 288C85.5 288 64 266.5 64 240L64 144z"/>'],

        ['type' => 'section', 'label' => 'Gestión'],

        ['route' => 'administracion.administrativa.solicitudes.index',       'label' => 'Solicitudes',
         'active' => 'administracion.administrativa.solicitudes.*',            'permission' => 'gestionar_solicitudes', 'viewBox' => '0 0 24 24',
         'badge' => $solicitudesPendientes,
         'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" fill="none" stroke="currentColor" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],

        ['route' => 'administracion.administrativa.tipos-solicitudes.index', 'label' => 'Tipos Solicitud',
         'active' => 'administracion.administrativa.tipos-solicitudes.*',      'permission' => 'gestionar_tipos_solicitudes', 'viewBox' => '0 0 24 24',
         'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" fill="none" stroke="currentColor" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],

        ['route' => 'administracion.administrativa.matriculacion.index', 'label' => 'Matrículas',
         'active' => 'administracion.administrativa.matriculacion.*',    'permission' => 'gestionar_matriculas', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M288 32L352 32C369.7 32 384 46.3 384 64L384 128L256 128L256 64C256 46.3 270.3 32 288 32zM96 96L208 96L208 128C208 154.5 229.5 176 256 176L384 176C410.5 176 432 154.5 432 128L432 96L544 96C579.3 96 608 124.7 608 160L608 480C608 515.3 579.3 544 544 544L96 544C60.7 544 32 515.3 32 480L32 160C32 124.7 60.7 96 96 96zM208 464C208 472.8 215.2 480 224 480L416 480C424.8 480 432 472.8 432 464C432 419.8 396.2 384 352 384L288 384C243.8 384 208 419.8 208 464zM320 344C350.9 344 376 318.9 376 288C376 257.1 350.9 232 320 232C289.1 232 264 257.1 264 288C264 318.9 289.1 344 320 344z"/>'],

        ['route' => 'administracion.administrativa.obligaciones.index', 'label' => 'Obligaciones',
         'active' => 'administracion.administrativa.obligaciones.*',     'permission' => 'gestionar_obligaciones_financieras', 'badge' => $pagosPendientesVerif,  'viewBox' => '0 0 640 640',
         'svg' => '<path d="M128 128C128 92.7 156.7 64 192 64L341.5 64C358.5 64 374.8 70.7 386.8 82.7L493.3 189.3C505.3 201.3 512 217.6 512 234.6L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 128zM336 122.5L336 216C336 229.3 346.7 240 360 240L453.5 240L336 122.5zM192 152C192 165.3 202.7 176 216 176L264 176C277.3 176 288 165.3 288 152C288 138.7 277.3 128 264 128L216 128C202.7 128 192 138.7 192 152zM192 248C192 261.3 202.7 272 216 272L264 272C277.3 272 288 261.3 288 248C288 234.7 277.3 224 264 224L216 224C202.7 224 192 234.7 192 248zM304 324L304 328C275.2 328.3 252 351.7 252 380.5C252 406.2 270.5 428.1 295.9 432.3L337.6 439.3C343.6 440.3 348 445.5 348 451.6C348 458.5 342.4 464.1 335.5 464.1L280 464C269 464 260 473 260 484C260 495 269 504 280 504L304 504L304 508C304 519 313 528 324 528C335 528 344 519 344 508L344 503.3C369 499.2 388 477.6 388 451.5C388 425.8 369.5 403.9 344.1 399.7L302.4 392.7C296.4 391.7 292 386.5 292 380.4C292 373.5 297.6 367.9 304.5 367.9L352 367.9C363 367.9 372 358.9 372 347.9C372 336.9 363 327.9 352 327.9L344 327.9L344 323.9C344 312.9 335 303.9 324 303.9C313 303.9 304 312.9 304 323.9z"/>'],

        ['route' => 'administracion.administrativa.consolidado-cohortes', 'label' => 'Cohortes',
         'active' => 'administracion.administrativa.consolidado-cohortes', 'permission' => 'ver_consolidado_cohortes', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M160 64C142.3 64 128 78.3 128 96C128 113.7 142.3 128 160 128L160 139C160 181.4 176.9 222.1 206.9 252.1L274.8 320L206.9 387.9C176.9 417.9 160 458.6 160 501L160 512C142.3 512 128 526.3 128 544C128 561.7 142.3 576 160 576L480 576C497.7 576 512 561.7 512 544C512 526.3 497.7 512 480 512L480 501C480 458.6 463.1 417.9 433.1 387.9L365.2 320L433.1 252.1C463.1 222.1 480 181.4 480 139L480 128C497.7 128 512 113.7 512 96C512 78.3 497.7 64 480 64L160 64zM224 139L224 128L416 128L416 139C416 158 410.4 176.4 400 192L240 192C229.7 176.4 224 158 224 139zM240 448C243.5 442.7 247.6 437.7 252.1 433.1L320 365.2L387.9 433.1C392.5 437.7 396.5 442.7 400.1 448L240 448z"/>'],

        ['route' => 'administracion.administrativa.practicas-pre-profesionales', 'label' => 'Prácticas Pre-Prof.',
         'active' => 'administracion.administrativa.practicas-pre-profesionales', 'permission' => 'gestionar_practicas_preprofesionales', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M64 128C64 92.7 92.7 64 128 64L384 64C419.3 64 448 92.7 448 128L448 249.3C401.1 268.3 368 314.3 368 368C368 395.7 376.8 421.4 391.8 442.4C340.3 463.4 304 514 304 573.1C304 574.1 304 575 304 576L128 576C92.7 576 64 547.3 64 512L64 128zM208 464L208 528L261.4 528C268.6 498.6 282.7 471.9 301.8 449.7C295.7 430.2 277.5 416 256 416C229.5 416 208 437.5 208 464zM339 288.3C338 288.1 337 288 336 288L304 288C295.2 288 288 295.2 288 304L288 336C288 344.8 295.2 352 304 352L320.7 352C322.8 329.2 329.1 307.7 339 288.3zM176 160C167.2 160 160 167.2 160 176L160 208C160 216.8 167.2 224 176 224L208 224C216.8 224 224 216.8 224 208L224 176C224 167.2 216.8 160 208 160L176 160zM288 176L288 208C288 216.8 295.2 224 304 224L336 224C344.8 224 352 216.8 352 208L352 176C352 167.2 344.8 160 336 160L304 160C295.2 160 288 167.2 288 176zM176 288C167.2 288 160 295.2 160 304L160 336C160 344.8 167.2 352 176 352L208 352C216.8 352 224 344.8 224 336L224 304C224 295.2 216.8 288 208 288L176 288zM416 368C416 323.8 451.8 288 496 288C540.2 288 576 323.8 576 368C576 412.2 540.2 448 496 448C451.8 448 416 412.2 416 368zM352 576C352 523 395 480 448 480L544 480C597 480 640 523 640 576C640 593.7 625.7 608 608 608L384 608C366.3 608 352 593.7 352 576z"/>'],

        ['route' => 'administracion.administrativa.practicas-comunitarias', 'label' => 'Prácticas Com.',
         'active' => 'administracion.administrativa.practicas-comunitarias', 'permission' => 'gestionar_practicas_comunitarias', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M320 64C390.7 64 448 121.3 448 192C448 262.7 390.7 320 320 320C249.3 320 192 262.7 192 192C192 121.3 249.3 64 320 64zM40 128C62.1 128 80 145.9 80 168L80 328.2C80 345.2 86.7 361.5 98.7 373.5L149.8 424.6C158.1 432.9 171.1 434.2 180.8 427.7C193.7 419.1 195.5 400.8 184.5 389.9C177.2 382.6 161.4 366.8 137.3 342.7C124.8 330.2 124.8 309.9 137.3 297.4C149.8 284.9 170.1 284.9 182.6 297.4C206.7 321.5 222.5 337.3 229.8 344.6L255.1 369.9C276.1 390.9 287.9 419.4 287.9 449.1L287.9 528C287.9 554.5 266.4 576 239.9 576L173.2 576C156.2 576 139.9 569.3 127.9 557.3L28.1 457.4C10.1 439.4 0 415 0 389.5L0 168C0 145.9 17.9 128 40 128zM600 128C622.1 128 640 145.9 640 168L640 389.5C640 415 629.9 439.4 611.9 457.4L512 557.3C500 569.3 483.7 576 466.7 576L400 576C373.5 576 352 554.5 352 528L352 449.1C352 419.4 363.8 390.9 384.8 369.9L410.1 344.6C417.4 337.3 433.2 321.5 457.3 297.4C469.8 284.9 490.1 284.9 502.6 297.4C515.1 309.9 515.1 330.2 502.6 342.7C478.5 366.8 462.7 382.6 455.4 389.9C444.4 400.9 446.2 419.1 459.1 427.7C468.8 434.2 481.8 432.9 490.1 424.6L541.2 373.5C553.2 361.5 559.9 345.2 559.9 328.2L560 168C560 145.9 577.9 128 600 128z"/>'],

        ['route' => 'administracion.administrativa.proceso-titulacion.index', 'label' => 'Titulación',
         'active' => 'administracion.administrativa.proceso-titulacion.*', 'permission' => 'gestionar_titulacion', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>'],

        ['type' => 'section', 'label' => 'Reportes y Docs.'],

        ['route' => 'administracion.administrativa.reportes.carrera-materia', 'label' => 'Reportes Carreras',
         'active' => 'administracion.administrativa.reportes.*',              'permission' => 'ver_reportes_academicos', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M96 96C113.7 96 128 110.3 128 128L128 464C128 472.8 135.2 480 144 480L544 480C561.7 480 576 494.3 576 512C576 529.7 561.7 544 544 544L144 544C99.8 544 64 508.2 64 464L64 128C64 110.3 78.3 96 96 96zM304 160C310.7 160 317.1 162.8 321.7 167.8L392.8 245.3L439 199C448.4 189.6 463.6 189.6 472.9 199L536.9 263C541.4 267.5 543.9 273.6 543.9 280L543.9 392C543.9 405.3 533.2 416 519.9 416L215.9 416C202.6 416 191.9 405.3 191.9 392L191.9 280C191.9 274 194.2 268.2 198.2 263.8L286.2 167.8C290.7 162.8 297.2 160 303.9 160z"/>'],

        ['route' => 'administracion.administrativa.reportes-financieros', 'label' => 'Reportes Financieros',
         'active' => 'administracion.administrativa.reportes-financieros',  'permission' => 'ver_reportes_financieros', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M64 128C51.1 128 39.4 135.8 34.4 147.8C29.4 159.8 32.2 173.5 41.4 182.6L224 365.3L224 480C224 488.5 227.4 496.6 233.4 502.6L297.4 566.6C299.9 569.1 302.7 571.1 305.7 572.6C284.5 541.7 272.1 504.3 272.1 464C272.1 364.6 347.6 282.9 444.4 273L534.8 182.6C544 173.4 546.7 159.7 541.7 147.7C536.7 135.7 524.9 128 512 128L64 128zM608 464C608 384.5 543.5 320 464 320C384.5 320 320 384.5 320 464C320 543.5 384.5 608 464 608C543.5 608 608 543.5 608 464zM448 384C448 375.2 455.2 368 464 368C472.8 368 480 375.2 480 384L480 392L496 392C504.8 392 512 399.2 512 408C512 416.8 504.8 424 496 424L450.2 424C444.6 424 440 428.6 440 434.2C440 439.1 443.5 443.3 448.3 444.2L493.3 452.4C513.3 456 527.9 473.5 527.9 493.9C527.9 517.2 509 536.1 485.7 536.1L479.9 536.1L479.9 544.1C479.9 552.9 472.7 560.1 463.9 560.1C455.1 560.1 447.9 552.9 447.9 544.1L447.9 536.1L431.9 536.1C423.1 536.1 415.9 528.9 415.9 520.1C415.9 511.3 423.1 504.1 431.9 504.1L485.7 504.1C491.3 504.1 495.9 499.5 495.9 493.9C495.9 489 492.4 484.8 487.6 483.9L442.6 475.7C422.6 472.1 408 454.6 408 434.2C408 411.6 425.7 393.2 448 392.1L448 384z"/>'],

        ['route' => 'administracion.administrativa.actas-colegiado.index', 'label' => 'Actas OCS',
         'active' => 'administracion.administrativa.actas-colegiado.*',    'permission' => 'gestionar_actas_colegiado', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M80 88C80 74.7 69.3 64 56 64C42.7 64 32 74.7 32 88L32 456C32 486.9 57.1 512 88 512L272 512L272 464L88 464C83.6 464 80 460.4 80 456L80 224L272 224L272 176L80 176L80 88zM368 288L560 288C586.5 288 608 266.5 608 240L608 144C608 117.5 586.5 96 560 96L477.3 96C468.8 96 460.7 92.6 454.7 86.6L446.1 78C437.1 69 424.9 63.9 412.2 63.9L368 64C341.5 64 320 85.5 320 112L320 240C320 266.5 341.5 288 368 288zM368 576L560 576C586.5 576 608 554.5 608 528L608 432C608 405.5 586.5 384 560 384L477.3 384C468.8 384 460.7 380.6 454.7 374.6L446.1 366C437.1 357 424.9 351.9 412.2 351.9L368 352C341.5 352 320 373.5 320 400L320 528C320 554.5 341.5 576 368 576z"/>'],

        ['route' => 'administracion.administrativa.normas-aprobadas.index', 'label' => 'Normas Aprobadas',
         'active' => 'administracion.administrativa.normas-aprobadas.*',    'permission' => 'gestionar_normas', 'viewBox' => '0 0 640 640',
         'svg' => '<path d="M80 88C80 74.7 69.3 64 56 64C42.7 64 32 74.7 32 88L32 456C32 486.9 57.1 512 88 512L272 512L272 464L88 464C83.6 464 80 460.4 80 456L80 224L272 224L272 176L80 176L80 88zM368 288L560 288C586.5 288 608 266.5 608 240L608 144C608 117.5 586.5 96 560 96L477.3 96C468.8 96 460.7 92.6 454.7 86.6L446.1 78C437.1 69 424.9 63.9 412.2 63.9L368 64C341.5 64 320 85.5 320 112L320 240C320 266.5 341.5 288 368 288zM368 576L560 576C586.5 576 608 554.5 608 528L608 432C608 405.5 586.5 384 560 384L477.3 384C468.8 384 460.7 380.6 454.7 374.6L446.1 366C437.1 357 424.9 351.9 412.2 351.9L368 352C341.5 352 320 373.5 320 400L320 528C320 554.5 341.5 576 368 576z"/>'],
    ];
@endphp

<style>
    [x-cloak] { display: none !important; }
    .sidebar-scrollbar::-webkit-scrollbar { width: 3px; }
    .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scrollbar::-webkit-scrollbar-thumb { background: rgba(100,116,139,.25); border-radius: 10px; }
    .sidebar-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(100,116,139,.45); }
</style>

{{-- ══════════════════════════════════════
     MOBILE BACKDROP
══════════════════════════════════════ --}}
<div x-data
     x-show="$store.sidebar.mobileOpen"
     x-cloak
     @click="$store.sidebar.mobileOpen = false"
     x-transition:enter="transition-opacity ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm md:hidden">
</div>

{{-- ══════════════════════════════════════
     ASIDE
══════════════════════════════════════ --}}
<aside
    x-data
    x-cloak
    :class="{
        'w-64':          $store.sidebar.open,
        'w-14':          !$store.sidebar.open,
        'translate-x-0': !$store.sidebar.mobile || $store.sidebar.mobileOpen,
        '-translate-x-full': $store.sidebar.mobile && !$store.sidebar.mobileOpen
    }"
    class="fixed left-0 top-0 z-50 flex flex-col h-screen
           bg-white dark:bg-[#0B1220]
           border-r border-slate-200 dark:border-slate-800
           transition-all duration-300 ease-in-out
           shadow-sm dark:shadow-2xl dark:shadow-black/60 overflow-hidden">

    {{-- ── HEADER / LOGO TOGGLE ─────────────────────────── --}}
    <div class="h-16 flex items-center flex-shrink-0 border-b border-slate-200 dark:border-slate-800 px-2"
         :class="$store.sidebar.open ? 'gap-2.5' : 'justify-center'">

        <button @click="$store.sidebar.toggle()"
                class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                       hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150
                       ring-1 ring-slate-200/70 dark:ring-white/[0.06]">
            <img class="w-8 h-8 object-cover rounded-lg" src="{{ $asideLogoSrc }}" alt="logo">
        </button>

        <span x-show="$store.sidebar.open" x-cloak
              x-transition:enter="transition ease-out duration-200 delay-75"
              x-transition:enter-start="opacity-0 -translate-x-2"
              x-transition:enter-end="opacity-100 translate-x-0"
              x-transition:leave="transition ease-in duration-100"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0 -translate-x-2"
              class="font-semibold text-sm text-slate-800 dark:text-white/90 whitespace-nowrap truncate leading-none">
            {{ $nombreCorto }}
        </span>
    </div>

    {{-- ── NAVIGATION ───────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2 px-2 sidebar-scrollbar">

        @foreach($navItems as $item)

            @php $perm = $item['permission'] ?? null; @endphp
            @if($perm && !auth()->user()?->can($perm))
                @continue
            @endif

            @if(($item['type'] ?? 'link') === 'section')
                {{-- Section header --}}
                <div class="relative flex items-center mt-3 mb-1">
                    <span x-show="$store.sidebar.open" x-cloak
                          x-transition:enter="transition ease-out duration-150"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="px-2 text-[0.6rem] font-semibold uppercase tracking-[0.15em]
                                 text-slate-400 dark:text-slate-600 whitespace-nowrap">
                        {{ $item['label'] }}
                    </span>
                    <span x-show="!$store.sidebar.open" x-cloak
                          class="w-full mx-1 h-px bg-slate-200 dark:bg-slate-700/50 block"></span>
                </div>

            @else
                @php
                    $isActive    = request()->routeIs($item['active']);
                    $badgeCount  = $item['badge'] ?? 0;
                @endphp
                <a href="{{ route($item['route']) }}"
                   :title="!$store.sidebar.open ? '{{ addslashes($item['label']) }}' : ''"
                   class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl
                          transition-all duration-150 cursor-pointer
                          {{ $isActive
                              ? 'bg-lime-500/[0.08] dark:bg-lime-500/[0.06] text-lime-700 dark:text-lime-400 ring-1 ring-inset ring-lime-500/20'
                              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">

                    {{-- Icon (with collapsed-mode badge dot) --}}
                    <span class="relative flex-shrink-0">
                        <svg class="w-5 h-5 transition-colors duration-150
                                    {{ $isActive
                                        ? 'fill-lime-600 dark:fill-lime-400'
                                        : 'fill-slate-500 dark:fill-slate-500 group-hover:fill-slate-800 dark:group-hover:fill-white' }}"
                             viewBox="{{ $item['viewBox'] }}" xmlns="http://www.w3.org/2000/svg">
                            {!! $item['svg'] !!}
                        </svg>
                        @if($badgeCount > 0)
                            <span x-show="!$store.sidebar.open" x-cloak
                                  class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white dark:ring-[#0B1220]"></span>
                        @endif
                    </span>

                    {{-- Label --}}
                    <span x-show="$store.sidebar.open" x-cloak
                          x-transition:enter="transition ease-out duration-150 delay-50"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          x-transition:leave="transition ease-in duration-100"
                          x-transition:leave-start="opacity-100"
                          x-transition:leave-end="opacity-0"
                          class="text-sm font-medium whitespace-nowrap overflow-hidden leading-none flex-1 min-w-0 truncate">
                        {{ $item['label'] }}
                    </span>

                    {{-- Badge pill (expanded) or active dot --}}
                    @if($badgeCount > 0)
                        <span x-show="$store.sidebar.open" x-cloak
                              class="flex-shrink-0 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full
                                     bg-amber-500 text-white text-[0.6rem] font-bold leading-none">
                            {{ $badgeCount > 99 ? '99+' : $badgeCount }}
                        </span>
                    @elseif($isActive)
                        <span x-show="$store.sidebar.open" x-cloak
                              class="w-1.5 h-1.5 rounded-full bg-lime-500 flex-shrink-0"></span>
                    @endif
                </a>
            @endif

        @endforeach

    </nav>

    {{-- ── FOOTER: TEMA + FULLSCREEN ───────────────────── --}}
    <div class="flex-shrink-0 border-t border-slate-200 dark:border-slate-800 p-2">
        <div class="flex items-center gap-1"
             :class="$store.sidebar.open ? 'justify-between px-1' : 'flex-col justify-center'">

            {{-- Theme toggle --}}
            <div class="flex items-center">
                <button onclick="setDark('dark')"
                        class="moon p-1.5 rounded-lg transition-colors duration-150
                               text-slate-500 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-800
                               hover:text-amber-500 dark:hover:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>
                <button onclick="setDark('light')"
                        class="sun hidden p-1.5 rounded-lg transition-colors duration-150
                               text-slate-500 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-800
                               hover:text-sky-500 dark:hover:text-sky-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Fullscreen --}}
            <span id="full-screen"
                  class="hidden md:flex cursor-pointer p-1.5 rounded-lg transition-colors duration-150
                         text-slate-500 dark:text-slate-400
                         hover:bg-slate-100 dark:hover:bg-slate-800
                         hover:text-slate-700 dark:hover:text-white">
                <i id="full-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
                    </svg>
                </i>
                <i id="collapse" class="hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                    </svg>
                </i>
            </span>

            {{-- Version badge (only expanded) --}}
            <span x-show="$store.sidebar.open" x-cloak
                  class="text-[0.6rem] text-slate-400 dark:text-slate-600 font-mono tabular-nums whitespace-nowrap">
                v2.0
            </span>

        </div>
    </div>

</aside>
