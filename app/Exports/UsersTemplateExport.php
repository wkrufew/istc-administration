<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Plantilla';
    }

    public function headings(): array
    {
        return [
            'first_name', 'last_name', 'cedula', 'rol', 'email', 'password',
            'phone', 'address', 'fecha_nacimiento', 'matricula_numero',
            'padre', 'madre', 'tutor', 'nacionalidad', 'genero', 'estado_civil',
            'telefono_emergencia', 'contacto_emergencia', 'tipo_sangre', 'observaciones_medicas',
            'is_facturador',
            'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono',
        ];
    }

    public function array(): array
    {
        return [[
            'Juan', 'Pérez', '0601234567', 'Estudiante', 'juan.perez@email.com', '12345678',
            '0999123456', 'Av. Principal 123', '1990-01-15', 'MAT-2024-001',
            'Pedro Pérez', 'María López', '', 'Ecuatoriano', 'Masculino', 'Soltero',
            '0988123456', 'María López', 'O+', '',
            'no',
            '', '', '', '', '',
        ]];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1B4332']],
            ],
            2 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 15, 'C' => 14, 'D' => 14, 'E' => 26,
            'F' => 14, 'G' => 14, 'H' => 24, 'I' => 18, 'J' => 18,
            'K' => 16, 'L' => 16, 'M' => 16, 'N' => 14, 'O' => 14,
            'P' => 14, 'Q' => 20, 'R' => 20, 'S' => 12, 'T' => 24,
            'U' => 14, 'V' => 20, 'W' => 16, 'X' => 24, 'Y' => 24, 'Z' => 16,
        ];
    }
}
