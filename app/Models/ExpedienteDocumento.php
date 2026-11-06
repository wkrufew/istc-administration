<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un archivo del expediente de una persona (estudiante o docente). Al reemplazar un documento
 * la versión anterior queda con vigente = false (historial). Ver App\Services\ExpedienteService.
 */
class ExpedienteDocumento extends Model
{
    protected $table = 'expediente_documentos';

    protected $fillable = [
        'user_id', 'tipo', 'descripcion', 'asignacion_docente_id', 'variante',
        'archivo_path', 'disco', 'nombre_original',
        'estado', 'observacion', 'origen', 'fuente', 'vigente',
        'subido_por', 'subido_at', 'revisado_por', 'revisado_at',
    ];

    protected $casts = [
        'vigente'     => 'boolean',
        'subido_at'   => 'datetime',
        'revisado_at' => 'datetime',
    ];

    /** Tipos de documento y a qué perfil aplican. Ninguno es obligatorio. */
    const TIPOS = [
        'cedula'          => ['nombre' => 'Cédula de identidad',          'perfiles' => ['estudiante', 'docente']],
        'bachiller'       => ['nombre' => 'Título de bachiller',          'perfiles' => ['estudiante']],
        'habilitante'     => ['nombre' => 'Título habilitante',           'perfiles' => ['estudiante']],
        'pago_admision'   => ['nombre' => 'Comprobante de pago de admisión', 'perfiles' => ['estudiante']],
        'hoja_vida'       => ['nombre' => 'Hoja de vida',                 'perfiles' => ['estudiante', 'docente']],
        'cert_laborales'  => ['nombre' => 'Certificados laborales',       'perfiles' => ['estudiante']],
        'cert_cursos'     => ['nombre' => 'Certificados de cursos',       'perfiles' => ['estudiante']],
        'mecanizado_iess' => ['nombre' => 'Mecanizado del IESS',          'perfiles' => ['estudiante']],
        'conadis'         => ['nombre' => 'Certificado CONADIS',          'perfiles' => ['estudiante', 'docente']],
        'senescyt'        => ['nombre' => 'Título registrado en SENESCYT', 'perfiles' => ['docente']],
        'contrato'        => ['nombre' => 'Contrato',                     'perfiles' => ['docente']],
        'otro'            => ['nombre' => 'Otro documento',               'perfiles' => ['estudiante', 'docente']],
    ];

    /** Documentos que solo se piden en validación de conocimientos (estudiantes). */
    const SOLO_VALIDACION = ['hoja_vida', 'cert_laborales', 'cert_cursos', 'mecanizado_iess'];

    const ESTADOS = [
        'pendiente'  => 'Pendiente de revisión',
        'aprobado'   => 'Aprobado',
        'rechazado'  => 'Rechazado',
        'solicitado' => 'Solicitado',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asignacionDocente(): BelongsTo
    {
        return $this->belongsTo(AsignacionDocente::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    // ── Consultas ─────────────────────────────────────────────────────────────

    public function scopeVigentes(Builder $q): Builder
    {
        return $q->where('vigente', true);
    }

    // ── Ayudas ────────────────────────────────────────────────────────────────

    public function nombreTipo(): string
    {
        return $this->tipo === 'otro' && $this->descripcion
            ? $this->descripcion
            : (self::TIPOS[$this->tipo]['nombre'] ?? $this->tipo);
    }

    public function tieneArchivo(): bool
    {
        return (bool) $this->archivo_path;
    }

    /** Dirección para ver el archivo: siempre pasa por el control de acceso (también los públicos). */
    public function url(): ?string
    {
        return $this->archivo_path ? route('expediente.archivo', $this) : null;
    }
}
