<?php

namespace App\Observers;

use App\Models\AuditoriaGeneral;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    private static array $excludeFields = [
        'password', 'remember_token', 'two_factor_secret',
        'two_factor_recovery_codes', 'updated_at', 'deleted_at',
        'numero_comprobante',  // se actualiza TEMP→real en un segundo save; no aporta info relevante en el diff
    ];

    public function created(Model $model): void
    {
        $this->log($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->log($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted', $model->getAttributes(), []);
    }

    private function log(Model $model, string $evento, array $antes, array $despues): void
    {
        try {
            $exclude = array_flip(self::$excludeFields);
            $antes   = array_diff_key($antes, $exclude);
            $despues = array_diff_key($despues, $exclude);

            // Ignorar 'updated' donde el único cambio eran campos excluidos (p.ej. numero_comprobante, updated_at)
            if ($evento === 'updated' && empty($despues)) {
                return;
            }

            AuditoriaGeneral::create([
                'auditable_type'  => get_class($model),
                'auditable_id'    => $model->getKey(),
                'evento'          => $evento,
                'valores_antes'   => !empty($antes)   ? $antes   : null,
                'valores_despues' => !empty($despues) ? $despues : null,
                'user_id'         => Auth::id(),
                'ip_address'      => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // La auditoría nunca debe romper el flujo principal
        }
    }
}
