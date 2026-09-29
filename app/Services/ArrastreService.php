<?php

namespace App\Services;

use App\Models\Materia;
use App\Models\MateriasArrastrada;
use Illuminate\Support\Facades\Log;

class ArrastreService
{
    const MAX_INTENTOS = 3;

    /**
     * Gestiona el registro de arrastre luego de guardar una calificación completa.
     *
     * Invariantes del llamador:
     *  - calificacionCompleta() == true
     *  - $numeroIntento ya fue calculado con calcularNumeroIntento()
     */
    public function gestionar(
        int   $userId,
        int   $materiaId,
        int   $periodoId,
        float $notaFinal,
        float $notaMinima,
        int   $numeroIntento
    ): void {
        $registro = MateriasArrastrada::where('user_id', $userId)
            ->where('materia_id', $materiaId)
            ->latest()
            ->first();

        if ($notaFinal >= $notaMinima) {
            if ($registro && $registro->estado !== 'Aprobada') {
                $registro->update([
                    'estado'        => 'Aprobada',
                    'nota_obtenida' => $notaFinal,
                ]);
            }
            return;
        }

        $nuevoEstado    = $numeroIntento >= self::MAX_INTENTOS ? 'Perdida_Definitiva' : 'Arrastrada';
        $porcentajeBase = (float) SettingService::get('matricula.porcentaje_arrastre', 10);
        $porcentajeReal = $numeroIntento >= 2 ? $porcentajeBase * 2 : $porcentajeBase;
        $costoAdicional = $this->calcularCostoAdicional($materiaId, $numeroIntento);

        $datos = [
            'user_id'                 => $userId,
            'materia_id'              => $materiaId,
            'periodo_reprobado_id'    => $periodoId,
            'nota_obtenida'           => $notaFinal,
            'nota_minima_requerida'   => $notaMinima,
            'porcentaje_penalizacion' => $porcentajeReal,
            'numero_intento'          => $numeroIntento,
            'estado'                  => $nuevoEstado,
            'costo_adicional'         => $costoAdicional,
        ];

        $registro
            ? $registro->update($datos)
            : MateriasArrastrada::create($datos);
    }

    /**
     * Determina el número de intento actual para el siguiente ciclo de calificación.
     * Consulta el registro activo más reciente y avanza uno.
     */
    public function calcularNumeroIntento(int $userId, int $materiaId): int
    {
        $registro = MateriasArrastrada::where('user_id', $userId)
            ->where('materia_id', $materiaId)
            ->latest()
            ->first();

        if (! $registro || $registro->estado === 'Aprobada') {
            return 1;
        }

        return min($registro->numero_intento + 1, self::MAX_INTENTOS);
    }

    /**
     * Calcula el costo adicional que genera un arrastre según el semestre y la carrera.
     */
    public function calcularCostoAdicional(int $materiaId, int $numeroIntento): float
    {
        try {
            $materia = Materia::with('semestre.carrera')->find($materiaId);
            if (! $materia) return 0;

            $semestre = $materia->semestre;
            if (! $semestre?->carrera_id) return 0;

            $carrera = $semestre->carrera;
            if (! $carrera) return 0;

            $porcentaje = (float) SettingService::get('matricula.porcentaje_arrastre', 10);
            if ($numeroIntento >= 2) {
                $porcentaje *= 2;
            }

            $creditos       = ($materia->horas_teoricas + $materia->horas_practicas) / 48;
            $costoNormal    = $creditos * (float) $carrera->costo_credito;
            $costoAdicional = $costoNormal * ($porcentaje / 100);

            return round($costoAdicional, 2);
        } catch (\Exception $e) {
            Log::error('ArrastreService.calcularCostoAdicional: ' . $e->getMessage());
            return 0;
        }
    }
}
