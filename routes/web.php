<?php

use App\Http\Controllers\Administration\ActasOCSController;
use App\Http\Controllers\Administration\NormasAprobadasController;
use App\Http\Controllers\Administration\CarreraController;
use App\Http\Controllers\Administration\DocenteController;
use App\Http\Controllers\Administration\DocumentacionPersonalController;
use App\Http\Controllers\Administration\EstudianteController;
use App\Http\Controllers\Administration\HorarioController;
use App\Http\Controllers\Administration\MateriaController;
use App\Http\Controllers\Administration\MateriaPeriodoParaleloController;
use App\Http\Controllers\Administration\MatriculacionController;
use App\Http\Controllers\Administration\PagosController;
use App\Http\Controllers\Administration\ParaleloController;
use App\Http\Controllers\Administration\PeriodoController;
use App\Http\Controllers\Administration\PracticasPreProfesionalesController;
use App\Http\Controllers\Administration\ProcesoTitulacionController;
use App\Http\Controllers\Administration\ReporteCarreraMateriaController;
use App\Http\Controllers\Administration\ReportesFinancierosController;
use App\Http\Controllers\Administration\RoleController;
use App\Http\Controllers\Administration\SemestreController;
use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Docencia\AsistenciasController;
use App\Http\Controllers\Docencia\CalificacionesController;
use App\Http\Controllers\Estudiantil\ActaCalificacionesController;
use App\Http\Controllers\Estudiantil\CalificacionesController as EstudiantilCalificacionesController;
use App\Http\Controllers\Estudiantil\HorariosController;
use App\Http\Controllers\Estudiantil\PagosController as EstudiantilPagosController;
use App\Http\Controllers\Estudiantil\UserProfileController;
use App\Livewire\Administration\ActaCalificacionAdmin;
use App\Livewire\Administration\ObligacionesEstudiante;
use App\Livewire\Administration\PagoMatricula;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Artisan;

// Redirigir la raíz al login
Route::get('/', function () {
    if (Auth::check()) {
        $user = auth()->user();
        $welcomeMessage = "¡Bienvenido, " . $user->name . "!";
        // Prioridad de redirección basada en permisos
        if ($user->can('acceso_administrativo')) {
            session()->flash('success', $welcomeMessage);
            return redirect()->route('administracion.administrativa.dashboard');
        }

        if ($user->can('acceso_docencia')) {
            session()->flash('success', $welcomeMessage);
            return redirect()->route('administracion.docencia.dashboard');
        }

        if ($user->can('acceso_estudiantil')) {
            session()->flash('success', $welcomeMessage);
            return redirect()->route('administracion.estudiantil.dashboard');
        }

        // Si no tiene permisos, cerrar sesión
        Auth::logout();
        return redirect()->route('login')->with('error', 'No tienes permisos para acceder al sistema.');
    }

    return redirect()->route('login');
});

// Rutas para invitados (no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
});

// Rutas protegidas para usuarios autenticados con roles específicos
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Rutas específicas por rol
    Route::middleware('permisos:acceso_administrativo')->prefix('administracion/administrativa')->name('administracion.administrativa.')->group(function () {
        Route::get('/', function () {
            return view('administracion.administrativa');
        })->name('dashboard');
        // GESTIÓN DE ROLES - Solo para usuarios con permisos específicos
        Route::middleware('permission:asignar_roles')->group(function () {
            Route::resource('roles', RoleController::class)->names('roles')->except('show');
        });
        // GESTIÓN DE USUARIOS
        //Route::middleware('permission:gestionar_usuarios')->group(function () {
        Route::resource('users', UserController::class)->names('users')->only('index', 'edit', 'update');
        //});
        // Perfil de usuario (disponible para todos los administrativos)
        Route::get('users/profile', [UserController::class, 'profile'])->name('users.profile');
        /* Route::get('/docente-perfil', function () {
            return view('administracion.docente-perfil.index');
        })->name('docente-profile'); */
        // GESTIÓN DE DOCENTES
        //Route::middleware('permission:gestionar_docentes')->group(function () {
        Route::resource('docentes', DocenteController::class)->names('docentes')->except('show');
        Route::get('docentes/{docente}/asignar', [DocenteController::class, 'showAsignar'])->name('docentes.asignar.form');
        Route::post('docentes/{docente}/asignar', [DocenteController::class, 'storeAsignacion'])->name('docentes.asignar.store');
        Route::delete('/docentes/{id}', [DocenteController::class, 'destroy'])->name('docentes.asignar.destroy');


        //});
        // GESTIÓN DE ESTUDIANTES
        //Route::middleware('permission:gestionar_estudiantes')->group(function () {
        Route::resource('estudiantes', EstudianteController::class)->names('estudiantes')->except('show');
        //crear una ruta para importar estudiantes
        Route::get('estudiantes/import-users', [EstudianteController::class, 'import'])->name('estudiantes.import');
        //});
        //Crear un permiso para la parte de gestion academica
        // GESTIÓN ACADÉMICA
        //Route::middleware('permission:gestionar_estructura_academica')->group(function () {
        // Períodos
        Route::resource('periodos', PeriodoController::class)->names('periodos');
        Route::post('/periodos/{periodo}/cerrar', [PeriodoController::class, 'cerrar'])->name('periodos.cerrar');
        // Carreras
        Route::resource('carreras', CarreraController::class)->names('carreras');
        // Semestres
        Route::resource('semestres', SemestreController::class)->names('semestres');
        // Materias
        Route::resource('materias', MateriaController::class)->names('materias');
        // Paralelos
        Route::resource('paralelos', ParaleloController::class)->names('paralelos');
        // Horarios - Rutas adicionales para la gestión de horarios
        Route::resource('horarios', HorarioController::class)->names('horarios');
        //MODULOS POR MATERIA
        Route::resource('materia-periodo-paralelo', MateriaPeriodoParaleloController::class)
            ->names('materia_periodo_paralelo')
            ->except('show');
        // Matriculacion
        Route::resource('matriculacion', MatriculacionController::class)->names('matriculacion');
        Route::get('/matriculas/{matricula}/pago', PagoMatricula::class)->name('matriculas.pago');
        //obligaciones financiera
        Route::get('/obligaciones', ObligacionesEstudiante::class)->name('obligaciones.index');

        //Reportes Financieron
        Route::get('/reportes-financieros', [ReportesFinancierosController::class, 'index'])->name('reportes-financieros');
        //Procesos de Titulacion
        Route::get('/practicas-pre-profesionales', [PracticasPreProfesionalesController::class, 'index'])->name('practicas-pre-profesionales');
        // PRACTICAS COMUNITARIAS
        Route::get('/practicas-comunitarias', [PracticasPreProfesionalesController::class, 'comunitaria'])->name('practicas-comunitarias');

        Route::get('/procesos-titulacion', [ProcesoTitulacionController::class, 'index'])->name('proceso-titulacion.index');

        Route::get('titulacion/acta/{userId}', ActaCalificacionAdmin::class)->name('titulacion.acta');
        // Pagos
        Route::resource('pagos', PagosController::class)->names('pagos');
        // Actas de organo de colegiado superior
        Route::resource('actas-colegiado', ActasOCSController::class)->names('actas-colegiado');
        // Normas Aprobadas
        Route::resource('normas-aprobadas', NormasAprobadasController::class)->names('normas-aprobadas');
        // Documentos aun no de docentes y personal administrativo
        Route::get(
            'documentacion-personal/create/{user}',
            [DocumentacionPersonalController::class, 'create']
        )->name('documentacion-personal.create');
        Route::resource('documentacion-personal', DocumentacionPersonalController::class)->except(['create'])->names('documentacion-personal');

        Route::get('reportes-carrera-materia', [ReporteCarreraMateriaController::class, 'index'])->name('reportes.carrera-materia');
    });

    Route::middleware('permisos:acceso_docencia')->prefix('administracion/docencia')->name('administracion.docencia.')->group(function () {
        Route::get('/', function () {
            return view('docencia.dashboard-estudiantil');
        })->name('dashboard'); //administracion.docencia.dashboard

        // Calificaciones
        Route::resource('calificaciones', CalificacionesController::class)->names('calificaciones');
        Route::get('asistencias-correccion', [AsistenciasController::class, 'asistenciacorreccion'])->name('asistencias.correccion');
        Route::resource('asistencias', AsistenciasController::class)->names('asistencias');
        Route::get('/docente-perfil', function () {
            return view('docencia.docente-perfil.index');
        })->name('docente-profile');
    });

    Route::middleware('permisos:acceso_estudiantil')->prefix('administracion/estudiantil')->name('administracion.estudiantil.')->group(function () {
        Route::get('/', function () {
            return view('estudiantil.dashboard-estudiantil');
        })->name('dashboard');

        // Horarios
        Route::get('horarios', [HorariosController::class, 'index'])->name('horarios');
        Route::get('calificaciones', [EstudiantilCalificacionesController::class, 'index'])->name('calificaciones');
        Route::get('perfil-estudiante', [UserProfileController::class, 'index'])->name('estudiante-profile');
        Route::get('obligaciones-financieras', [EstudiantilPagosController::class, 'index'])->name('obligaciones-financieras');
        Route::get('acta-calificaciones', [ActaCalificacionesController::class, 'index'])->name('acta-calificaciones.index');
    });
});

// Ruta para manejar accesos no autorizados
Route::get('/acceso-denegado', function () {
    return view('errors.403')->with('message', 'No tienes permisos para acceder a esta sección.');
})->name('access.denied');


//Rutas para despliegue
Route::get('key-generate', function () {
    $exitCode = Artisan::call('key:generate');
    return 'Key generada';
});

Route::get('storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return 'Simbolic Link establecido';
});

Route::get('/optimize-clear', function () {
    $exitCode = Artisan::call('optimize:clear');
    return 'Depurada cache';
});
