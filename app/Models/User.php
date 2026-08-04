<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasRoles;
    use SoftDeletes;

    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'address',
        'cedula',
        'fecha_nacimiento',
        'matricula_numero',
        'padre',
        'madre',
        'tutor',
        'nacionalidad',
        'etnia',
        'genero',
        'estado_civil',
        'telefono_emergencia',
        'contacto_emergencia',
        'tipo_sangre',
        'observaciones_medicas',
        'is_active',
        'moodle_suspended',
        'discapacidad',
        'discapacidad_descripcion',
        /* 'certificado_discapacidad_path', */
        'is_facturador',
        'fact_nombre',
        'fact_documento',
        'fact_correo',
        'fact_direccion',
        'fact_telefono',
        'profile_photo_path',
        'cumpleanos_notificado_year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        /* 'profile_photo_path', */
        /* 'certificado_discapacidad_path' */
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
            'is_active'        => 'boolean',
            'moodle_suspended' => 'boolean',
        ];
    }

    // ============ RELACIONES COMO ESTUDIANTE ============

    /**
     * Matrículas del estudiante
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'user_id');
    }

    public function ultimaMatricula(): HasOne
    {
        return $this->hasOne(Matricula::class, 'user_id')->latestOfMany();
    }

    public function convalidaciones()
    {
        return $this->hasMany(\App\Models\Convalidacion::class, 'user_id');
    }

    /**
     * Matrícula principal
     */
    /* public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    } */

    /**
     * Detalles de matrícula del estudiante
     */
    public function detalleMatriculas()
    {
        return $this->hasMany(DetalleMatricula::class, 'user_id');
    }

    /**
     * Materias arrastradas por el estudiante
     */
    public function materiasArrastradas()
    {
        return $this->hasMany(MateriasArrastrada::class, 'user_id');
    }

    /**
     * Pagos realizados por el estudiante (vía obligaciones financieras)
     */
    public function pagos()
    {
        return $this->hasManyThrough(
            Pago::class,
            ObligacionesFinanciera::class,
            'user_id',       // FK en obligaciones_financieras
            'obligacion_id', // FK en pagos
            'id',
            'id'
        );
    }

    /**
     * Asistencias del estudiante (a través de detalle_matricula)
     */
    public function asistencias()
    {
        return $this->hasManyThrough(
            Asistencia::class,
            DetalleMatricula::class,
            'user_id', // FK en detalle_matriculas
            'detalle_matricula_id', // FK en asistencias
            'id', // PK en users
            'id' // PK en detalle_matriculas
        );
    }

    // ============ RELACIONES COMO DOCENTE ============

    /**
     * Asignaciones como docente
     */
    public function asignacionesDocente()
    {
        return $this->hasMany(AsignacionDocente::class, 'docente_id');
    }

    /**
     * Calificaciones registradas como docente
     */
    public function calificacionesRegistradas()
    {
        return $this->hasMany(Calificacion::class, 'docente_id');
    }

    /**
     * Auditorías de calificaciones como docente
     */
    public function auditoriasCalificaciones()
    {
        return $this->hasMany(AuditoriaCalificacion::class, 'docente_id');
    }

    /**
     * Asistencias registradas como docente
     */
    public function asistenciasRegistradas()
    {
        return $this->hasMany(Asistencia::class, 'docente_id');
    }

    // ============ MÉTODOS DE UTILIDAD ============

    /**
     * Verificar si es estudiante
     */
    public function esEstudiante(): bool
    {
        return $this->hasRole('estudiante');
    }

    /**
     * Verificar si es docente
     */
    public function esDocente(): bool
    {
        return $this->hasRole('docente');
    }

    /**
     * Verificar si es administrador
     */
    public function esAdministrador(): bool
    {
        return $this->hasRole(['admin', 'administrador']);
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->name;
    }

    public function document(): HasOne
    {
        return $this->hasOne(Document::class);
    }

    public function obligacionesFinancieras()
    {
        return $this->hasMany(ObligacionesFinanciera::class);
    }

    ///para practicas preprofesionales y titutlacion
    public function practicasPreprofesionales()
    {
        return $this->hasMany(PracticaPreprofesional::class, 'user_id');
    }

    public function notasTitulacion()
    {
        return $this->hasMany(NotaTitulacion::class, 'user_id');
    }
}
