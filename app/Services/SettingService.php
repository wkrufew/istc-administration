<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    const CACHE_KEY = 'app_settings';

    // =========================================================================
    // OBTENER UN VALOR (desde caché → BD → default)
    // =========================================================================
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all();
        return $all[$key] ?? $default;
    }

    // =========================================================================
    // CARGAR TODOS LOS SETTINGS (con caché permanente)
    // =========================================================================
    public static function all(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return Setting::all()
                ->mapWithKeys(fn($s) => [$s->key => $s->value])
                ->toArray();
        });
    }

    // =========================================================================
    // GUARDAR UN VALOR Y REFRESCAR CACHÉ
    // =========================================================================
    public static function set(string $key, mixed $value): void
    {
        Setting::where('key', $key)->update(['value' => $value]);
        static::flush();
    }

    // =========================================================================
    // INVALIDA LA CACHÉ (llama después de cualquier actualización masiva)
    // =========================================================================
    public static function flush(): void
    {
        Cache::forget(static::CACHE_KEY);
    }

    // =========================================================================
    // CONSTRUIR UN MAILER LISTO PARA USAR CON LA CONFIG SMTP DE BD
    // Crea el transporte Symfony directamente para poder deshabilitar la
    // verificación del peer SSL (necesario con Brevo en servidores regionales).
    // =========================================================================
    public static function buildMailer(): \Illuminate\Mail\Mailer
    {
        $encryption = static::get('smtp.encryption', 'tls');

        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            static::get('smtp.host', 'localhost'),
            (int) static::get('smtp.port', 587),
            $encryption === 'ssl'
        );
        $transport->setUsername(static::get('smtp.username', ''));
        $transport->setPassword(static::get('smtp.password', ''));

        $stream = $transport->getStream();
        if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
            $stream->setStreamOptions([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                ],
            ]);
        }

        $mailer = new \Illuminate\Mail\Mailer(
            'smtp',
            app('view'),
            $transport,
            app('events')
        );
        $mailer->alwaysFrom(
            static::get('smtp.from_address', 'no-reply@localhost'),
            static::get('smtp.from_name', 'Sistema')
        );

        return $mailer;
    }

    // =========================================================================
    // TODOS LOS SETTINGS DE UN GRUPO
    // =========================================================================
    public static function group(string $group): array
    {
        $prefix = $group . '.';
        return collect(static::all())
            ->filter(fn($v, $k) => str_starts_with($k, $prefix))
            ->mapWithKeys(fn($v, $k) => [str_replace($prefix, '', $k) => $v])
            ->toArray();
    }
}
