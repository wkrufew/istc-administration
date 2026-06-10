<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================================
        // LISTA COMPLETA DE PERMISOS
        // =====================================================================
        $permissions = [

            // -----------------------------------------------------------------
            // PORTEROS — controlan el acceso a cada portal
            // NO eliminar: la lógica de redirección en web.php depende de ellos
            // -----------------------------------------------------------------
            'acceso_administrativo',
            'acceso_docencia',
            'acceso_estudiantil',
            'acceso_admision',

            // -----------------------------------------------------------------
            // ROLES Y USUARIOS
            // 'asignar_roles' está en uso en Route::middleware() de web.php
            // -----------------------------------------------------------------
            'asignar_roles',          // accede a /roles (RoleController)
            'crear_roles',            // reservado para creación explícita de roles
            'gestionar_usuarios',     // menú Usuarios + listado
            'gestionar_docentes',     // menú Docentes + listado
            'gestionar_estudiantes',  // menú Estudiantes + listado
            'gestionar_auditorias',   // menú Auditoría
            'moodle_gestion',         // Gestión de integración con Moodle
            'auditoria_ver',          // Ver registros de auditoría general

            // Sub-permisos usuarios
            'crear_usuarios',          // botón Nuevo usuario
            'editar_usuarios',         // botón editar (lápiz)
            'eliminar_usuarios',       // botón eliminar + restaurar desde papelera
            'reenviar_credenciales',   // botón Reenviar credenciales

            // -----------------------------------------------------------------
            // CONFIGURACIÓN — permiso único para todo el módulo de settings
            // -----------------------------------------------------------------
            'gestionar_configuracion',

            // -----------------------------------------------------------------
            // ESTRUCTURA ACADÉMICA
            // -----------------------------------------------------------------
            'gestionar_periodos',           // PeriodoController (CRUD + cerrar periodo)
            'gestionar_carreras',           // CarreraController
            'gestionar_semestres',          // SemestreController
            'gestionar_materias',           // MateriaController
            'gestionar_paralelos',          // ParaleloController
            'gestionar_horarios',           // HorarioController
            'gestionar_modulos_academicos', // MateriaPeriodoParaleloController

            // -----------------------------------------------------------------
            // MATRÍCULA Y FINANZAS
            // -----------------------------------------------------------------
            'gestionar_matriculas',               // menú Matrículas + listado
            'gestionar_pagos',                    // PagosController
            'gestionar_obligaciones_financieras', // menú Obligaciones + listado
            'ver_reportes_financieros',           // ReportesFinancierosController

            // Sub-permisos matrículas
            'crear_matriculas',    // botón Nueva matrícula
            'editar_matriculas',   // botón editar matrícula
            'cancelar_matriculas', // botón cancelar matrícula

            // Sub-permisos obligaciones
            'crear_obligaciones_manuales', // botón Nueva obligación manual
            'registrar_pagos',             // botón registrar / subir comprobante
            'verificar_pagos',             // botón verificar / confirmar pago

            // -----------------------------------------------------------------
            // PROCESOS ACADÉMICOS (administración)
            // -----------------------------------------------------------------
            'gestionar_practicas_preprofesionales', // PracticasPreProfesionalesController
            'gestionar_practicas_comunitarias',     // PracticasPreProfesionalesController::comunitaria
            'gestionar_titulacion',                 // ProcesoTitulacionController + ActaCalificacionAdmin
            'ver_reportes_academicos',              // ReporteCarreraMateriaController
            'ver_consolidado_cohortes',             // vista consolidado-cohortes

            // -----------------------------------------------------------------
            // DOCUMENTACIÓN INSTITUCIONAL
            // -----------------------------------------------------------------
            'gestionar_actas_colegiado',       // ActasOCSController
            'gestionar_normas',                // NormasAprobadasController
            'gestionar_documentacion_personal', // DocumentacionPersonalController

            // -----------------------------------------------------------------
            // TICKETS DE SOPORTE
            // -----------------------------------------------------------------
            'ver_tickets',             // ver propios tickets (TicketList)
            'ver_todos_tickets',       // ver todos los tickets del sistema
            'crear_tickets',           // crear nuevos tickets (TicketCreate)
            'responder_tickets',       // enviar mensajes en un ticket (TicketShow)
            'asignar_tickets',         // asignar ticket a un usuario (TicketShow → asignar())
            'cambiar_estado_tickets',  // cambiar estado (TicketShow → cambiarEstado())
            'cerrar_tickets',          // cerrar/resolver ticket definitivamente

            // -----------------------------------------------------------------
            // SOLICITUDES — módulo completo
            // -----------------------------------------------------------------
            'gestionar_solicitudes',        // menú Solicitudes + listado
            'aprobar_solicitudes',          // botón Aprobar
            'rechazar_solicitudes',         // botón Rechazar
            'avanzar_solicitudes',          // botón Avanzar estado
            'gestionar_tipos_solicitudes',  // menú Tipos Solicitud + CRUD

            // -----------------------------------------------------------------
            // DOCENCIA
            // 'ver_notas_estudiantes' e 'ingresar_notas_estudiantes' están en
            //  uso en la lógica existente — no renombrar
            // -----------------------------------------------------------------
            'ver_notas_estudiantes',        // ver calificaciones de sus materias
            'ingresar_notas_estudiantes',   // registrar/editar calificaciones
            'gestionar_asistencias',        // AsistenciasController (CRUD + corrección)

            // -----------------------------------------------------------------
            // PORTAL ESTUDIANTIL
            // 'ver_calificaciones' y 'matricularse' están en uso — no renombrar
            // -----------------------------------------------------------------
            'ver_calificaciones',           // EstudiantilCalificacionesController
            'matricularse',                 // flujo de matrícula del estudiante
            'ver_horarios',                 // HorariosController (estudiantil)
            'ver_obligaciones_financieras', // EstudiantilPagosController
            'ver_acta_calificaciones',      // ActaCalificacionesController (estudiantil)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =====================================================================
        // CATEGORÍAS — solo afectan la columna 'category' (UI), no la lógica
        // de autorización. Seguro de re-ejecutar: solo hace UPDATE de display.
        // =====================================================================
        $categorias = [
            'Porteros' => [
                'acceso_administrativo', 'acceso_docencia',
                'acceso_estudiantil', 'acceso_admision',
            ],
            'Usuarios y Roles' => [
                'asignar_roles', 'crear_roles', 'gestionar_usuarios',
                'gestionar_docentes', 'gestionar_estudiantes', 'gestionar_auditorias',
                'moodle_gestion', 'auditoria_ver',
                'crear_usuarios', 'editar_usuarios', 'eliminar_usuarios', 'reenviar_credenciales',
            ],
            'Configuración' => [
                'gestionar_configuracion',
            ],
            'Estructura Académica' => [
                'gestionar_periodos', 'gestionar_carreras', 'gestionar_semestres',
                'gestionar_materias', 'gestionar_paralelos', 'gestionar_horarios',
                'gestionar_modulos_academicos',
            ],
            'Matrícula y Finanzas' => [
                'gestionar_matriculas', 'gestionar_pagos', 'gestionar_obligaciones_financieras',
                'ver_reportes_financieros',
                'crear_matriculas', 'editar_matriculas', 'cancelar_matriculas',
                'crear_obligaciones_manuales', 'registrar_pagos', 'verificar_pagos',
            ],
            'Procesos Académicos' => [
                'gestionar_practicas_preprofesionales', 'gestionar_practicas_comunitarias',
                'gestionar_titulacion', 'ver_reportes_academicos', 'ver_consolidado_cohortes',
            ],
            'Documentación Institucional' => [
                'gestionar_actas_colegiado', 'gestionar_normas', 'gestionar_documentacion_personal',
            ],
            'Tickets de Soporte' => [
                'ver_tickets', 'ver_todos_tickets', 'crear_tickets', 'responder_tickets',
                'asignar_tickets', 'cambiar_estado_tickets', 'cerrar_tickets',
            ],
            'Solicitudes' => [
                'gestionar_solicitudes', 'aprobar_solicitudes', 'rechazar_solicitudes',
                'avanzar_solicitudes', 'gestionar_tipos_solicitudes',
            ],
            'Docencia' => [
                'ver_notas_estudiantes', 'ingresar_notas_estudiantes', 'gestionar_asistencias',
            ],
            'Portal Estudiantil' => [
                'ver_calificaciones', 'matricularse', 'ver_horarios',
                'ver_obligaciones_financieras', 'ver_acta_calificaciones',
            ],
        ];

        foreach ($categorias as $categoria => $nombres) {
            Permission::whereIn('name', $nombres)->update(['category' => $categoria]);
        }

        // =====================================================================
        // ROLES — solo se crean si no existen (firstOrCreate).
        // No se asignan permisos a ningún rol desde aquí, excepto Administrador.
        // Los permisos de cada usuario se gestionan directamente desde la UI.
        // Re-ejecutar este seeder en producción es completamente seguro:
        // solo crea permisos nuevos, actualiza categorías, y garantiza que
        // el rol Administrador tenga acceso total.
        // =====================================================================
        $admin = Role::firstOrCreate(['name' => 'Administrador',         'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Secretaria',             'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Docente',                'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Estudiante',             'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admision',               'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Rector',                 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Vicerrectora',           'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Coordinadora Academica', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Coordinador General',    'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Secretaria General',     'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Procuraduria',           'guard_name' => 'web']);

        // El rol Administrador siempre recibe TODOS los permisos del sistema.
        // Todos los demás roles no reciben permisos por seeder — se asignan
        // directamente a los usuarios desde la UI de gestión de permisos.
        $admin->syncPermissions($permissions);
    }
}
