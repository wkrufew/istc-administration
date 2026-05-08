<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaGeneral extends Model
{
    const UPDATED_AT = null;

    protected $table = 'auditoria_general';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'evento',
        'valores_antes',
        'valores_despues',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'valores_antes'   => 'array',
        'valores_despues' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
