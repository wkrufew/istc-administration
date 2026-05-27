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
        // ROLES
        // =====================================================================
        $admin               = Role::firstOrCreate(['name' => 'Administrador',          'guard_name' => 'web']);
        $secretaria          = Role::firstOrCreate(['name' => 'Secretaria',              'guard_name' => 'web']);
        $docente             = Role::firstOrCreate(['name' => 'Docente',                 'guard_name' => 'web']);
        $estudiante          = Role::firstOrCreate(['name' => 'Estudiante',             'guard_name' => 'web']);
        $admision            = Role::firstOrCreate(['name' => 'Admision',               'guard_name' => 'web']);
        $rector              = Role::firstOrCreate(['name' => 'Rector',                  'guard_name' => 'web']);
        $vicerrectora        = Role::firstOrCreate(['name' => 'Vicerrectora',            'guard_name' => 'web']);
        $coordAcademica      = Role::firstOrCreate(['name' => 'Coordinadora Academica',  'guard_name' => 'web']);
        $coordGeneral        = Role::firstOrCreate(['name' => 'Coordinador General',     'guard_name' => 'web']);
        $secretariaGeneral   = Role::firstOrCreate(['name' => 'Secretaria General',      'guard_name' => 'web']);
        $procuraduria        = Role::firstOrCreate(['name' => 'Procuraduria',            'guard_name' => 'web']);

        // =====================================================================
        // ADMINISTRADOR — acceso total: recibe TODOS los permisos
        // Los demás roles se configuran desde la UI de Roles y Permisos
        // =====================================================================
        $admin->syncPermissions($permissions);

        // =====================================================================
        // DOCENTE — solo portal de docencia (no se gestiona por UI de roles)
        // =====================================================================
        $docente->syncPermissions([
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
            'gestionar_asistencias',
        ]);

        // =====================================================================
        // ESTUDIANTE — solo portal estudiantil (no se gestiona por UI de roles)
        // =====================================================================
        $estudiante->syncPermissions([
            'acceso_estudiantil',
            'ver_calificaciones',
            'matricularse',
            'ver_horarios',
            'ver_obligaciones_financieras',
            'ver_acta_calificaciones',
        ]);

        // =====================================================================
        // TODOS LOS DEMÁS ROLES ADMINISTRATIVOS
        // Solo tienen acceso al portal; los permisos específicos los asigna
        // el Administrador desde la pantalla de Roles y Permisos.
        // =====================================================================
        foreach ([$secretaria, $admision, $rector, $vicerrectora,
                  $coordAcademica, $coordGeneral, $secretariaGeneral, $procuraduria] as $role) {
            $role->syncPermissions(['acceso_administrativo']);
        }
    }
}
