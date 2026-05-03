<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ----------------------------------------------------------------
            // INSTITUTO
            // ----------------------------------------------------------------
            ['group' => 'instituto', 'key' => 'instituto.nombre_largo',  'type' => 'text',     'label' => 'Nombre largo',            'value' => 'Instituto Superior Tecnológico Cumandá',            'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.nombre_corto',  'type' => 'text',     'label' => 'Nombre abreviado',        'value' => 'ISTC',                                              'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.ruc',           'type' => 'text',     'label' => 'RUC / Cod. SENESCYT',     'value' => '0660012345001',                                    'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.direccion',     'type' => 'text',     'label' => 'Dirección',               'value' => 'Av. Principal s/n, Cumandá, Chimborazo',           'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.telefono',      'type' => 'text',     'label' => 'Teléfono',                'value' => '+593 999 000 111',                                  'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.email',         'type' => 'email',    'label' => 'Email institucional',     'value' => 'institutosuperiortecnologicocu@gmail.com',          'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.web',           'type' => 'text',     'label' => 'Sitio web',               'value' => 'https://www.istcumanda.edu.ec',                     'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.logo_path',     'type' => 'image',    'label' => 'Logo principal',          'value' => null,                                                'is_encrypted' => false],
            ['group' => 'instituto', 'key' => 'instituto.favicon_path',  'type' => 'image',    'label' => 'Favicon',                 'value' => null,                                                'is_encrypted' => false],

            // ----------------------------------------------------------------
            // WHATSAPP
            // ----------------------------------------------------------------
            ['group' => 'whatsapp', 'key' => 'whatsapp.phone_number_id',       'type' => 'text',     'label' => 'Phone Number ID',              'value' => '593983942105',               'is_encrypted' => false],
            ['group' => 'whatsapp', 'key' => 'whatsapp.access_token',          'type' => 'password', 'label' => 'Access Token',                 'value' => 'EAAxxxxxxxxxxxxxxx',          'is_encrypted' => true],
            ['group' => 'whatsapp', 'key' => 'whatsapp.template_confirmacion', 'type' => 'text',     'label' => 'Template: confirmación',       'value' => 'matricula_confirmacion',      'is_encrypted' => false],
            ['group' => 'whatsapp', 'key' => 'whatsapp.template_bienvenida',   'type' => 'text',     'label' => 'Template: bienvenida',         'value' => 'matricula_bienvenida',        'is_encrypted' => false],
            ['group' => 'whatsapp', 'key' => 'whatsapp.activo',                'type' => 'boolean',  'label' => 'Envíos activos',               'value' => '0',                          'is_encrypted' => false],

            // ----------------------------------------------------------------
            // SMTP
            // ----------------------------------------------------------------
            ['group' => 'smtp', 'key' => 'smtp.driver',       'type' => 'text',     'label' => 'Driver',            'value' => 'smtp',                              'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.host',         'type' => 'text',     'label' => 'Host',              'value' => 'smtp.gmail.com',                    'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.port',         'type' => 'text',     'label' => 'Puerto',            'value' => '587',                               'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.username',     'type' => 'email',    'label' => 'Usuario',           'value' => 'institutosuperiortecnologicocu@gmail.com', 'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.password',     'type' => 'password', 'label' => 'Contraseña',        'value' => 'app-password-aqui',                 'is_encrypted' => true],
            ['group' => 'smtp', 'key' => 'smtp.encryption',   'type' => 'text',     'label' => 'Encriptación',      'value' => 'tls',                               'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.from_name',    'type' => 'text',     'label' => 'Nombre remitente',  'value' => 'Instituto Superior Tecnológico Cumandá', 'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.from_address', 'type' => 'email',    'label' => 'Email remitente',   'value' => 'institutosuperiortecnologicocu@gmail.com', 'is_encrypted' => false],
            ['group' => 'smtp', 'key' => 'smtp.activo',       'type' => 'boolean',  'label' => 'Envíos activos',    'value' => '0',                               'is_encrypted' => false],

            // ----------------------------------------------------------------
            // DOCUMENTOS
            // ----------------------------------------------------------------
            ['group' => 'documentos', 'key' => 'documentos.rector',      'type' => 'text',     'label' => 'Rector / Director',          'value' => 'Ing. Juan Pérez Sánchez, MSc.',         'is_encrypted' => false],
            ['group' => 'documentos', 'key' => 'documentos.secretario',  'type' => 'text',     'label' => 'Secretario/a Académico/a',   'value' => 'Lcda. María López Guerrero',             'is_encrypted' => false],
            ['group' => 'documentos', 'key' => 'documentos.coordinador', 'type' => 'text',     'label' => 'Coordinación Académica',     'value' => 'Ing. Carlos Ramírez Flores, Mg.',        'is_encrypted' => false],
            ['group' => 'documentos', 'key' => 'documentos.ciudad',      'type' => 'text',     'label' => 'Ciudad',                     'value' => 'Cumandá, Ecuador',                       'is_encrypted' => false],
            ['group' => 'documentos', 'key' => 'documentos.pie_pagina',  'type' => 'textarea', 'label' => 'Pie de página en PDFs',      'value' => 'Documento generado por el Sistema Académico del ISTC. Válido solo con firma y sello institucional.', 'is_encrypted' => false],

            // ----------------------------------------------------------------
            // NOTIFICACIONES
            // ----------------------------------------------------------------
            ['group' => 'notificaciones', 'key' => 'notificaciones.matricula_whatsapp',  'type' => 'boolean', 'label' => 'Matrícula → WhatsApp',   'value' => '0', 'is_encrypted' => false],
            ['group' => 'notificaciones', 'key' => 'notificaciones.matricula_email',     'type' => 'boolean', 'label' => 'Matrícula → Email',      'value' => '0', 'is_encrypted' => false],
            ['group' => 'notificaciones', 'key' => 'notificaciones.pago_whatsapp',       'type' => 'boolean', 'label' => 'Pago → WhatsApp',        'value' => '0', 'is_encrypted' => false],
            ['group' => 'notificaciones', 'key' => 'notificaciones.pago_email',          'type' => 'boolean', 'label' => 'Pago → Email',           'value' => '0', 'is_encrypted' => false],
            ['group' => 'notificaciones', 'key' => 'notificaciones.titulacion_whatsapp', 'type' => 'boolean', 'label' => 'Titulación → WhatsApp',  'value' => '0', 'is_encrypted' => false],
            ['group' => 'notificaciones', 'key' => 'notificaciones.titulacion_email',    'type' => 'boolean', 'label' => 'Titulación → Email',     'value' => '0', 'is_encrypted' => false],
        ];

        foreach ($settings as $data) {
            $setting = Setting::firstOrNew(['key' => $data['key']]);

            // Solo insertar si es nuevo (no sobreescribir datos reales)
            if (! $setting->exists) {
                $setting->group        = $data['group'];
                $setting->key          = $data['key'];
                $setting->type         = $data['type'];
                $setting->label        = $data['label'];
                $setting->is_encrypted = $data['is_encrypted'];

                $setting->value = $data['value'];

                $setting->save();
            }
        }

        SettingService::flush();
    }
}
