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
            'gestionar_usuarios',     // CRUD de personal administrativo (UserController)
            'gestionar_docentes',     // CRUD de docentes + asignación de horarios
            'gestionar_estudiantes',  // CRUD de estudiantes + importación masiva

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
            'gestionar_matriculas',             // MatriculacionController + PagoMatricula (Livewire)
            'gestionar_pagos',                  // PagosController
            'gestionar_obligaciones_financieras', // ObligacionesEstudiante (Livewire)
            'ver_reportes_financieros',         // ReportesFinancierosController

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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // =====================================================================
        // ROLES
        // =====================================================================
        $admin      = Role::firstOrCreate(['name' => 'Administrador']);
        $secretaria = Role::firstOrCreate(['name' => 'Secretaria']);
        $docente    = Role::firstOrCreate(['name' => 'Docente']);
        $estudiante = Role::firstOrCreate(['name' => 'Estudiante']);
        $admision   = Role::firstOrCreate(['name' => 'Admision']);

        // =====================================================================
        // ADMINISTRADOR — acceso total al sistema
        // =====================================================================
        $admin->syncPermissions([
            // Porteros
            'acceso_administrativo',

            // Roles y usuarios
            'asignar_roles',
            'crear_roles',
            'gestionar_usuarios',
            'gestionar_docentes',
            'gestionar_estudiantes',

            // Estructura académica
            'gestionar_periodos',
            'gestionar_carreras',
            'gestionar_semestres',
            'gestionar_materias',
            'gestionar_paralelos',
            'gestionar_horarios',
            'gestionar_modulos_academicos',

            // Matrícula y finanzas
            'gestionar_matriculas',
            'gestionar_pagos',
            'gestionar_obligaciones_financieras',
            'ver_reportes_financieros',

            // Procesos académicos
            'gestionar_practicas_preprofesionales',
            'gestionar_practicas_comunitarias',
            'gestionar_titulacion',
            'ver_reportes_academicos',
            'ver_consolidado_cohortes',

            // Documentación institucional
            'gestionar_actas_colegiado',
            'gestionar_normas',
            'gestionar_documentacion_personal',

            // Tickets (todos)
            'ver_tickets',
            'ver_todos_tickets',
            'crear_tickets',
            'responder_tickets',
            'asignar_tickets',
            'cambiar_estado_tickets',
            'cerrar_tickets',

            // Docencia (puede revisar)
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
            'gestionar_asistencias',
        ]);

        // =====================================================================
        // SECRETARIA — gestión operativa completa, sin gestión de roles
        // =====================================================================
        $secretaria->syncPermissions([
            // Porteros
            'acceso_administrativo',

            // Usuarios (sin roles)
            'gestionar_usuarios',
            'gestionar_docentes',
            'gestionar_estudiantes',

            // Estructura académica
            'gestionar_periodos',
            'gestionar_carreras',
            'gestionar_semestres',
            'gestionar_materias',
            'gestionar_paralelos',
            'gestionar_horarios',
            'gestionar_modulos_academicos',

            // Matrícula y finanzas
            'gestionar_matriculas',
            'gestionar_pagos',
            'gestionar_obligaciones_financieras',
            'ver_reportes_financieros',

            // Procesos académicos
            'gestionar_practicas_preprofesionales',
            'gestionar_practicas_comunitarias',
            'gestionar_titulacion',
            'ver_reportes_academicos',
            'ver_consolidado_cohortes',

            // Documentación institucional
            'gestionar_actas_colegiado',
            'gestionar_normas',
            'gestionar_documentacion_personal',

            // Tickets (todos excepto asignar)
            'ver_tickets',
            'ver_todos_tickets',
            'crear_tickets',
            'responder_tickets',
            'cambiar_estado_tickets',
            'cerrar_tickets',

            // Docencia (solo lectura)
            'ver_notas_estudiantes',
        ]);

        // =====================================================================
        // ADMISION — enfocado en matrícula, pagos y atención al estudiante
        // =====================================================================
        $admision->syncPermissions([
            // Porteros
            'acceso_admision',

            // Estudiantes y docentes (consulta/registro básico)
            /* 'gestionar_estudiantes', */

            // Matrícula y finanzas
            /* 'gestionar_matriculas',
            'gestionar_pagos',
            'gestionar_obligaciones_financieras',
            'ver_reportes_financieros', */

            // Consultas académicas
            /* 'ver_reportes_academicos',
            'ver_notas_estudiantes', */

            // Tickets (atención y seguimiento)
            /* 'ver_tickets',
            'ver_todos_tickets',
            'crear_tickets',
            'responder_tickets',
            'cambiar_estado_tickets',
            'cerrar_tickets', */
        ]);

        // =====================================================================
        // DOCENTE — portal de docencia
        // =====================================================================
        $docente->syncPermissions([
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
            'gestionar_asistencias',
        ]);

        // =====================================================================
        // ESTUDIANTE — portal estudiantil
        // =====================================================================
        $estudiante->syncPermissions([
            'acceso_estudiantil',
            'ver_calificaciones',
            'matricularse',
            'ver_horarios',
            'ver_obligaciones_financieras',
            'ver_acta_calificaciones',
        ]);
    }
}
