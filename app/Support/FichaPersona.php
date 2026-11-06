<?php

namespace App\Support;

use App\Models\User;

/**
 * La ficha de una persona: campos, opciones de cada lista y campos obligatorios.
 * Un solo lugar para el portal de admisión, la gestión de aspirantes y la ficha del estudiante,
 * para que todos guarden los mismos valores.
 */
class FichaPersona
{
    // ── Opciones (las mismas del portal de admisión) ─────────────────────────
    const SEXO           = ['Hombre', 'Mujer'];
    const GENERO         = ['Masculino', 'Femenino', 'Transgénero', 'Fluido', 'Cisgénero', 'Prefiero no decirlo'];
    const ESTADO_CIVIL   = ['Soltero/a', 'Casado/a', 'Divorciado/a', 'Unión libre', 'Viudo/a'];
    const TIPO_SANGRE    = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    const ETNIA          = ['Indígena', 'Afroecuatoriano', 'Negro', 'Mulato', 'Montuvio', 'Mestizo', 'Blanco', 'Otro'];
    const DISCAPACIDAD   = ['Intelectual', 'Física', 'Visual', 'Auditiva', 'Mental', 'Otra'];
    const PAISES         = ['Ecuador', 'Colombia', 'Perú', 'Venezuela', 'Bolivia', 'Argentina', 'Brasil', 'Chile', 'México', 'España',
                            'Estados Unidos', 'Cuba', 'Haití', 'China', 'Alemania', 'Francia', 'Italia', 'Otros'];
    const TIPO_COLEGIO   = ['Fiscal', 'Fiscomisional', 'Particular', 'Municipal', 'Extranjero'];
    const FORMACION      = ['Centro de Alfabetización', 'Jardín de infantes', 'Primaria', 'Educación Básica', 'Secundaria',
                            'Educación Media', 'Superior no Universitaria', 'Superior Universitaria', 'Posgrado'];
    const PARENTESCO     = ['Padre', 'Madre', 'Cónyuge', 'Hermano/a', 'Tío/a', 'Otro'];

    /** Nombre visible de cada campo (también para la auditoría de cambios). */
    const ETIQUETAS = [
        'first_name' => 'Nombres', 'last_name' => 'Apellidos', 'cedula' => 'Cédula', 'email' => 'Correo',
        'phone' => 'Celular', 'fecha_nacimiento' => 'Fecha de nacimiento', 'sexo' => 'Sexo', 'genero' => 'Género',
        'estado_civil' => 'Estado civil', 'tipo_sangre' => 'Tipo de sangre', 'etnia' => 'Etnia',
        'pueblo_nacionalidad' => 'Pueblo o nacionalidad', 'nacionalidad' => 'País de nacionalidad',
        'tiene_discapacidad' => 'Tiene discapacidad', 'tipo_discapacidad' => 'Tipo de discapacidad',
        'porcentaje_discapacidad' => 'Porcentaje de discapacidad', 'nro_conadis' => 'N.º carné CONADIS',
        'observaciones_medicas' => 'Observaciones médicas', 'matricula_numero' => 'Número de matrícula',
        'provincia_nacimiento' => 'Provincia de nacimiento', 'canton_nacimiento' => 'Cantón de nacimiento',
        'pais_residencia' => 'País de residencia', 'provincia_residencia' => 'Provincia de residencia',
        'canton_residencia' => 'Cantón de residencia', 'address' => 'Dirección de domicilio',
        'tipo_colegio' => 'Tipo de colegio', 'nombre_colegio' => 'Nombre del colegio', 'ocupacion' => 'Ocupación',
        'empleo_ingresos' => 'Empleo / ingresos', 'bono_dh' => 'Bono de Desarrollo Humano',
        'ingresos_hogar' => 'Ingresos del hogar', 'miembros_hogar' => 'Miembros del hogar',
        'padre' => 'Padre', 'formacion_padre' => 'Formación del padre', 'madre' => 'Madre',
        'formacion_madre' => 'Formación de la madre', 'tutor' => 'Tutor',
        'contacto_emergencia' => 'Contacto de emergencia', 'telefono_emergencia' => 'Teléfono de emergencia',
        'parentesco_emergencia' => 'Parentesco del contacto',
        'is_facturador' => 'Factura a su nombre', 'fact_nombre' => 'Facturar a (nombre)',
        'fact_documento' => 'Facturar a (cédula/RUC)', 'fact_correo' => 'Correo de facturación',
        'fact_direccion' => 'Dirección de facturación', 'fact_telefono' => 'Teléfono de facturación',
    ];

    /** Pestañas de la ficha y sus campos. */
    const SECCIONES = [
        'personales' => ['first_name', 'last_name', 'cedula', 'email', 'phone', 'fecha_nacimiento', 'sexo', 'genero',
                         'estado_civil', 'tipo_sangre', 'etnia', 'pueblo_nacionalidad', 'nacionalidad',
                         'tiene_discapacidad', 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis',
                         'observaciones_medicas', 'matricula_numero', 'provincia_nacimiento', 'canton_nacimiento',
                         'pais_residencia', 'provincia_residencia', 'canton_residencia', 'address'],
        'socioeconomicos' => ['tipo_colegio', 'nombre_colegio', 'ocupacion', 'empleo_ingresos', 'bono_dh',
                              'ingresos_hogar', 'miembros_hogar'],
        'familia' => ['padre', 'formacion_padre', 'madre', 'formacion_madre', 'tutor',
                      'contacto_emergencia', 'telefono_emergencia', 'parentesco_emergencia'],
        'facturacion' => ['is_facturador', 'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono'],
    ];

    const ESTADOS_FICHA = [
        'incompleta'        => 'Incompleta',
        'sin_revisar'       => 'Sin revisar',
        'en_revision'       => 'En revisión',
        'con_observaciones' => 'Con observaciones',
        'aprobada'          => 'Aprobada',
    ];

    /** Campos obligatorios de la ficha (los mismos que exige admisión para aprobar). */
    public static function camposRequeridos(?User $u): array
    {
        $campos = [
            'phone', 'sexo', 'genero', 'estado_civil', 'tipo_sangre', 'etnia',
            'fecha_nacimiento', 'nacionalidad',
            'address', 'pais_residencia', 'provincia_residencia', 'canton_residencia',
            'provincia_nacimiento', 'canton_nacimiento',
            'tipo_colegio', 'nombre_colegio', 'ocupacion', 'empleo_ingresos',
            'bono_dh', 'ingresos_hogar', 'miembros_hogar',
            'padre', 'formacion_padre', 'madre', 'formacion_madre',
            'contacto_emergencia', 'telefono_emergencia', 'parentesco_emergencia',
        ];
        if ($u?->etnia === 'Indígena')
            $campos[] = 'pueblo_nacionalidad';
        if ($u?->tiene_discapacidad)
            array_push($campos, 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis');
        if (! $u?->is_facturador)
            array_push($campos, 'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono');

        return $campos;
    }

    /** Campos obligatorios que le faltan, por nombre visible. */
    public static function faltantes(User $u): array
    {
        return collect(self::camposRequeridos($u))
            ->filter(fn($c) => blank($u->$c))
            ->map(fn($c) => self::ETIQUETAS[$c] ?? $c)
            ->values()->all();
    }

    /** Estado que se muestra: "incompleta" se calcula; los demás los marca secretaría (o el estudiante al enviar). */
    public static function estado(User $u): string
    {
        if ($u->ficha_estado === 'aprobada') return 'aprobada';
        if (self::faltantes($u)) return 'incompleta';
        return $u->ficha_estado ?: 'sin_revisar';
    }

    /** Sección (pestaña) a la que pertenece un campo. */
    public static function seccionDe(string $campo): ?string
    {
        foreach (self::SECCIONES as $seccion => $campos) {
            if (in_array($campo, $campos, true)) return $seccion;
        }
        return null;
    }
}
