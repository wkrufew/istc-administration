<?php

namespace App\Http\Controllers;

use App\Models\ExpedienteDocumento;
use Illuminate\Support\Facades\Storage;

/**
 * Muestra un archivo del expediente solo a quien puede verlo: su dueño o el personal que
 * gestiona estudiantes o docentes. Los archivos privados no tienen otra forma de abrirse.
 */
class ExpedienteArchivoController extends Controller
{
    public function ver(ExpedienteDocumento $documento)
    {
        $user = auth()->user();

        abort_unless(
            $user && ($user->id === $documento->user_id || $user->can('gestionar_estudiantes') || $user->can('gestionar_docentes')),
            403,
            'No tienes permiso para ver este documento.'
        );

        $disco = Storage::disk($documento->disco);
        abort_unless($documento->archivo_path && $disco->exists($documento->archivo_path), 404, 'El archivo no se encuentra.');

        // inline: el navegador lo abre (PDF/imagen) en lugar de descargarlo
        return $disco->response($documento->archivo_path, $documento->nombre_original ?: basename($documento->archivo_path), [], 'inline');
    }
}
