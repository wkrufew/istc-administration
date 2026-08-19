<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EstudiantesReporteExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Estudiantes ' . now()->format('Y');
    }

    public function collection()
    {
        return User::role('Estudiante')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->values()
            ->map(function (User $user, int $i) {
                $edad = $user->fecha_nacimiento
                    ? Carbon::parse($user->fecha_nacimiento)->age
                    : '';

                return [
                    $i + 1,
                    strtoupper($user->last_name ?? ''),
                    $user->first_name ?? '',
                    $user->cedula ?? '',
                    $user->email ?? '',
                    $user->genero ?? '',
                    $edad,
                    $user->fecha_nacimiento ? $user->fecha_nacimiento->format('d/m/Y') : '',
                    $user->phone ?? '',
                    $user->estado_civil ?? '',
                    $user->tipo_sangre ?? '',
                    $user->nacionalidad ?? '',
                    $user->etnia ?? '',
                    $user->matricula_numero ?? '',
                    $user->tiene_discapacidad ? 'Sí' : 'No',
                    $user->bono_dh ? 'Sí' : 'No',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Apellidos',
            'Nombres',
            'Cédula',
            'Correo electrónico',
            'Género',
            'Edad',
            'Fecha de nacimiento',
            'Teléfono',
            'Estado civil',
            'Tipo de sangre',
            'Nacionalidad',
            'Etnia',
            'N° Matrícula',
            'Discapacidad',
            'Bono DH',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 22,
            'C' => 20,
            'D' => 14,
            'E' => 32,
            'F' => 12,
            'G' => 7,
            'H' => 18,
            'I' => 14,
            'J' => 14,
            'K' => 13,
            'L' => 16,
            'M' => 14,
            'N' => 16,
            'O' => 13,
            'P' => 10,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'P';

        // Bordes para toda la tabla
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        // Filas alternas
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0FDF4'],
                    ],
                ]);
            }
        }

        // Centrar columnas N°, Edad, Tipo sangre, Discapacidad, Bono
        foreach (['A', 'G', 'K', 'O', 'P'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [
            // Cabecera
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 10,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF14532D'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
