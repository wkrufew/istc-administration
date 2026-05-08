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
        'matricula_confirmacion'   => env('WHATSAPP_TEMPLATE_CONFIRMACION',        'matricula_confirmacion'),

        // Para primer ingreso al sistema (incluye credenciales de ambas plataformas)
        'matricula_bienvenida'     => env('WHATSAPP_TEMPLATE_BIENVENIDA',          'matricula_bienvenida'),

        // Confirmación genérica de pago (colegiatura, multa, arrastre, etc.)
        'pago_confirmacion'        => env('WHATSAPP_TEMPLATE_PAGO',                'pago_confirmacion'),

        // Pago de primera matrícula: detalla matrícula + inscripción auto-liquidada
        'pago_primera_matricula'   => env('WHATSAPP_TEMPLATE_PAGO_PRIMERA',        'pago_primera_matricula'),

        // Tickets de soporte
        'ticket_nuevo'             => env('WHATSAPP_TEMPLATE_TICKET_NUEVO',        'ticket_nuevo'),
        'ticket_respuesta'         => env('WHATSAPP_TEMPLATE_TICKET_RESPUESTA',    'ticket_respuesta'),
    ],

];
