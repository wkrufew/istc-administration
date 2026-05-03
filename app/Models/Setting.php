<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    // =========================================================================
    // GRUPOS
    // =========================================================================
    const GROUP_INSTITUTO      = 'instituto';
    const GROUP_WHATSAPP       = 'whatsapp';
    const GROUP_SMTP           = 'smtp';
    const GROUP_DOCUMENTOS     = 'documentos';
    const GROUP_NOTIFICACIONES = 'notificaciones';

    // =========================================================================
    // TIPOS
    // =========================================================================
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_PASSWORD = 'password';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_IMAGE    = 'image';
    const TYPE_EMAIL    = 'email';

    // =========================================================================
    // ACCESSOR — desencripta al leer si corresponde
    // =========================================================================
    public function getValueAttribute(?string $raw): ?string
    {
        if ($this->is_encrypted && $raw !== null) {
            try {
                return Crypt::decryptString($raw);
            } catch (\Exception) {
                return $raw;
            }
        }
        return $raw;
    }

    // =========================================================================
    // MUTATOR — encripta al guardar si corresponde
    // =========================================================================
    public function setValueAttribute(?string $val): void
    {
        $this->attributes['value'] = ($this->is_encrypted && $val !== null)
            ? Crypt::encryptString($val)
            : $val;
    }
}
