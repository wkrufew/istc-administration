<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API — Meta
    |--------------------------------------------------------------------------
    | Configura estas variables en tu .env
    */

    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
    'access_token'    => env('WHATSAPP_ACCESS_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Nombres de los templates aprobados en Meta
    |--------------------------------------------------------------------------
    | Deben coincidir EXACTAMENTE con el nombre en Meta Business Manager
    */
    'templates' => [
        // Para estudiantes que ya existían en el sistema
        'matricula_confirmacion' => env('WHATSAPP_TEMPLATE_CONFIRMACION', 'matricula_confirmacion'),

        // Para primer ingreso al sistema (incluye credenciales)
        'matricula_bienvenida'   => env('WHATSAPP_TEMPLATE_BIENVENIDA', 'matricula_bienvenida'),
    ],

];
