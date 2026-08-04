<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'numero',
        'titulo',
        'descripcion',
        'prioridad',
        'estado',
        'created_by',
        'assigned_to',
        'assigned_at',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'fecha_limite',
        'observaciones',
    ];

    protected $casts = [
        'assigned_at'       => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at'       => 'datetime',
        'closed_at'         => 'datetime',
        'fecha_limite'      => 'date',
        'es_nota_interna'   => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(TicketAsignacion::class);
    }

    public function asignados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_asignaciones', 'ticket_id', 'user_id')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->orderBy('name');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Genera el siguiente número correlativo: TICK-0001 */
    public static function generarNumero(): string
    {
        $ultimo = static::max('id') ?? 0;
        return 'TICK-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    public function estaAbierto(): bool
    {
        return $this->estado !== 'cerrado';
    }

    // ── Labels para vistas ────────────────────────────────────────────────────

    public static function prioridadColor(string $prioridad): string
    {
        return match ($prioridad) {
            'baja'    => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'media'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'alta'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
            'urgente' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    public static function estadoColor(string $estado): string
    {
        return match ($estado) {
            'pendiente'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
            'en_proceso' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'cerrado'    => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    public static function estadoLabel(string $estado): string
    {
        return match ($estado) {
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En proceso',
            'cerrado'    => 'Cerrado',
            default      => ucfirst($estado),
        };
    }

    public static function prioridadLabel(string $prioridad): string
    {
        return match ($prioridad) {
            'baja'    => 'Baja',
            'media'   => 'Media',
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
            default   => ucfirst($prioridad),
        };
    }
}
