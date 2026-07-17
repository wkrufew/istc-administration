<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HorariosExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Collection $horarios,
        private readonly array      $conflictos,
        private readonly array      $stats
    ) {}

    public function sheets(): array
    {
        return [
            new HorariosHorariosSheet($this->horarios, $this->conflictos),
            new HorariosConflictosSheet($this->conflictos),
            new HorariosResumenSheet($this->stats),
        ];
    }
}

// ─────────────────────────────────────────────────────────────
// Hoja 1: listado completo de horarios
// ─────────────────────────────────────────────────────────────
class HorariosHorariosSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(
        private readonly Collection $horarios,
        private readonly array      $conflictos
    ) {}

    public function title(): string { return 'Horarios'; }

    public function headings(): array
    {
        return [
            'Día', 'Hora Inicio', 'Hora Fin', 'Duración (h)',
            'Materia', 'Código', 'Horas Teóricas', 'Horas Prácticas', 'Créditos',
            'Paralelo', 'Docente', 'Aula', 'Modalidad',
            'Carrera', 'Semestre',
            'Conflicto',
        ];
    }

    public function array(): array
    {
        $conflictIds = collect($this->conflictos)
            ->flatMap(fn($c) => [$c['horario_a']['id'], $c['horario_b']['id']])
            ->unique()->flip()->toArray();

        $diasOrden = ['Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6];

        return $this->horarios
            ->sortBy([
                fn($a, $b) => ($diasOrden[$a->dia_semana] ?? 7) <=> ($diasOrden[$b->dia_semana] ?? 7),
                fn($a, $b) => strtotime($a->hora_inicio) <=> strtotime($b->hora_inicio),
            ])
            ->map(function ($h) use ($conflictIds) {
                $dur = round((strtotime($h->hora_fin) - strtotime($h->hora_inicio)) / 3600, 2);
                $ht  = $h->materia?->horas_teoricas ?? 0;
                $hp  = $h->materia?->horas_practicas ?? 0;

                return [
                    $h->dia_semana,
                    $h->hora_inicio->format('H:i'),
                    $h->hora_fin->format('H:i'),
                    $dur,
                    $h->materia?->name,
                    $h->materia?->code,
                    $ht,
                    $hp,
                    round(($ht + $hp) / 48, 2),
                    $h->paralelo?->name,
                    $h->asignacionDocente?->docente?->name,
                    $h->aula,
                    $h->modalidad_clase,
                    $h->materia?->semestre?->carrera?->name,
                    $h->materia?->semestre?->name,
                    isset($conflictIds[$h->id]) ? 'SÍ' : 'No',
                ];
            })
            ->values()
            ->toArray();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, 'B' => 11, 'C' => 11, 'D' => 12,
            'E' => 32, 'F' => 10, 'G' => 13, 'H' => 13, 'I' => 10,
            'J' => 12, 'K' => 28, 'L' => 12, 'M' => 15,
            'N' => 28, 'O' => 22,
            'P' => 11,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header row
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '14451A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '22c55e']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Data rows
        for ($i = 2; $i <= $lastRow; $i++) {
            $isConflict = $sheet->getCell("P{$i}")->getValue() === 'SÍ';
            $isEven     = $i % 2 === 0;

            $sheet->getStyle("A{$i}:P{$i}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $isConflict ? 'FEE2E2' : ($isEven ? 'F8FAFC' : 'FFFFFF')]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            if ($isConflict) {
                $sheet->getStyle("P{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'B91C1C']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FCA5A5']],
                ]);
            }
        }

        // Freeze header
        $sheet->freezePane('A2');

        return [];
    }
}

// ─────────────────────────────────────────────────────────────
// Hoja 2: conflictos
// ─────────────────────────────────────────────────────────────
class HorariosConflictosSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private readonly array $conflictos) {}

    public function title(): string { return 'Conflictos'; }

    public function headings(): array
    {
        return ['Tipo', 'Día', 'Descripción', 'Materia A', 'Hora A', 'Materia B', 'Hora B'];
    }

    public function array(): array
    {
        if (empty($this->conflictos)) {
            return [['—', 'Sin conflictos detectados', '—', '—', '—', '—', '—']];
        }

        return array_map(fn($c) => [
            strtoupper($c['tipo']),
            $c['dia'],
            $c['descripcion'],
            $c['horario_a']['materia'],
            $c['horario_a']['hora_ini'] . ' – ' . $c['horario_a']['hora_fin'],
            $c['horario_b']['materia'],
            $c['horario_b']['hora_ini'] . ' – ' . $c['horario_b']['hora_fin'],
        ], $this->conflictos);
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 12, 'C' => 28, 'D' => 30, 'E' => 15, 'F' => 30, 'G' => 15];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FCA5A5']]],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
        }

        $sheet->freezePane('A2');

        return [];
    }
}

// ─────────────────────────────────────────────────────────────
// Hoja 3: resumen estadístico
// ─────────────────────────────────────────────────────────────
class HorariosResumenSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private readonly array $stats) {}

    public function title(): string { return 'Resumen'; }

    public function array(): array
    {
        return [
            ['RESUMEN ESTADÍSTICO', ''],
            ['', ''],
            ['Indicador', 'Valor'],
            ['Clases por semana',    $this->stats['total_clases']],
            ['Docentes activos',     $this->stats['docentes_unicos']],
            ['Paralelos con clases', $this->stats['paralelos_unicos']],
            ['Horas semanales',      $this->stats['horas_semana'] . ' h'],
            ['Créditos totales',     $this->stats['total_creditos']],
            ['Conflictos detectados',$this->stats['total_conflictos']],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 28, 'B' => 18];
    }

    public function styles(Worksheet $sheet): array
    {
        // Title
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '14451A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Column headers row
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '14451A']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '16A34A']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Data rows
        for ($i = 4; $i <= 9; $i++) {
            $isLast = ($i === 9);
            $isConf = ($i === 9 && (int) $sheet->getCell("B{$i}")->getValue() > 0);
            $sheet->getStyle("A{$i}:B{$i}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $isConf ? 'FEE2E2' : ($i % 2 === 0 ? 'F8FAFC' : 'FFFFFF')]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("B{$i}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $isConf ? 'B91C1C' : '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($i)->setRowHeight(22);
        }

        return [];
    }
}
