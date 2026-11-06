<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Aspirante;
use App\Models\User;
use App\Services\FichaPdfService;
use App\Support\PerfilUsuario;
use Illuminate\Http\Request;

/**
 * PDF de la ficha. Cada ruta está dentro del permiso de su sección (gestionar_estudiantes,
 * gestionar_aspirantes, gestionar_docentes). El resumen financiero va solo si se pide con
 * ?financiero=1 y quien descarga tiene ver_obligaciones_financieras.
 */
class FichaPdfController extends Controller
{
    public function estudiante(Request $request, User $estudiante)
    {
        abort_unless(PerfilUsuario::esEstudiante($estudiante), 404);

        $financiero = $this->conFinanciero($request);
        $ficha = FichaPdfService::estudiante($estudiante, $financiero);
        FichaPdfService::auditarDescarga($estudiante, $estudiante, $ficha['titulo'], $financiero);

        return FichaPdfService::respuesta($ficha, FichaPdfService::nombreArchivo('ficha-estudiante', $estudiante));
    }

    public function aspirante(Request $request, Aspirante $aspirante)
    {
        $financiero = $this->conFinanciero($request);
        $ficha = FichaPdfService::aspirante($aspirante, $financiero);
        FichaPdfService::auditarDescarga($aspirante, $aspirante->user, $ficha['titulo'], $financiero);

        return FichaPdfService::respuesta($ficha, FichaPdfService::nombreArchivo('ficha-aspirante', $aspirante->user));
    }

    public function docente(User $docente)
    {
        abort_unless(PerfilUsuario::esDocente($docente), 404);

        $ficha = FichaPdfService::docente($docente);
        FichaPdfService::auditarDescarga($docente, $docente, $ficha['titulo'], false);

        return FichaPdfService::respuesta($ficha, FichaPdfService::nombreArchivo('ficha-docente', $docente));
    }

    private function conFinanciero(Request $request): bool
    {
        return $request->boolean('financiero') && FichaPdfService::puedeVerFinanciero();
    }
}
