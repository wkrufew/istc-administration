<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAsignacion extends Model
{
    protected $table = 'ticket_asignaciones';

    protected $fillable = ['ticket_id', 'user_id', 'assigned_by', 'assigned_at'];

    protected $casts = ['assigned_at' => 'datetime'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
