<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Comunitaria;
use App\Models\NotaTitulacion;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Traits\WithAuthorization;

class PracticasComunitarias extends Component
{
    use WithPagination, WithFileUploads, WithAuthorization;

    // =========================================================================
    // FILTROS INDEX
    // =========================================================================
    public string $busqueda  = '';
    public string $filtroEstado = '';

    // =========================================================================
    // MODAL
    // =========================================================================
    public bool   $showModal    = false;
    public bool   $modoEdicion  = false;
    public ?int   $comunitariaId   = null;

    // Buscador predictivo de estudiante (solo en crear)
    public string $busquedaEstudiante   = '';
    public bool   $showDropdownEstudiante = false;
    public ?int   $estudianteId         = null;
    public string $estudianteNombre     = '';
    public ?int   $carreraId            = null;
    public string $carreraNombre        = '';

    // Campos del formulario
    public string $empresa           = '';
    public string $programa_vinculacion           = '';
    public string $sector            = '';
    public string $direccion         = '';
    public string $tutorEmpresa      = '';
    public string $cargoTutor        = '';
    public string $telefono          = '';
    public string $email             = '';
    public string $cargoEstudiante   = '';
    public string $actividades       = '';
    public string $fechaInicio       = '';
    public string $fechaFin          = '';
    public string $totalHoras        = '';
    public string $nota              = '';
    public string $estado            = 'En_Curso';
    public string $observaciones     = '';

    // Documentos (nuevos archivos subidos)
    public $cartaAceptacion  = null;
    public $informeFinal     = null;
    public $certificado      = null;

    // Paths actuales (en edición, para mostrar los ya subidos)
    public ?string $cartaAceptacionPath = null;
    public ?string $informeFinalPath    = null;
    public ?string $certificadoPath     = null;

    // =========================================================================
    // RESET PAGINACIÓN AL BUSCAR
    // =========================================================================
    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }
    public function updatedFiltroEstado(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // COMPUTED — TABLA INDEX
    // =========================================================================
    #[Computed]
    public function comunitarias()
    {
        return Comunitaria::with(['estudiante', 'carrera'])
            ->when($this->busqueda, function ($q) {
                $q->whereHas(
                    'estudiante',
                    fn($s) =>
                    $s->where('name',    'like', '%' . $this->busqueda . '%')
                        ->orWhere('cedula', 'like', '%' . $this->busqueda . '%')
                );
            })
            ->when(
                $this->filtroEstado,
                fn($q) =>
                $q->where('estado', $this->filtroEstado)
            )
            ->orderByDesc('created_at')
            ->paginate(12);
    }

    // =========================================================================
    // BUSCADOR PREDICTIVO DE ESTUDIANTE
    // =========================================================================
    #[Computed]
    public function estudiantesSugeridos()
    {
        if (strlen($this->busquedaEstudiante) < 3) return collect();

        // Solo estudiantes con malla completa
        return User::role('estudiante')
            ->where(
                fn($q) =>
                $q->where('name',    'like', '%' . $this->busquedaEstudiante . '%')
                    ->orWhere('cedula', 'like', '%' . $this->busquedaEstudiante . '%')
            )
            ->with(['matriculas' => fn($q) => $q->where('estado', 'Habilitada')->with('carrera')->latest()])
            ->get()
            ->filter(function ($user) {
                $matricula = $user->matriculas->first();
                if (! $matricula) return false;
                return NotaTitulacion::mallaCurricular_Completada($user->id, $matricula->carrera_id);
            })
            ->take(6);
    }

    public function seleccionarEstudiante(int $userId): void
    {
        $estudiante = User::with([
            'matriculas' => fn($q) => $q->where('estado', 'Habilitada')->with('carrera')->latest(),
        ])->find($userId);

        if (! $estudiante) return;

        $matricula = $estudiante->matriculas->first();

        $this->estudianteId         = $userId;
        $this->estudianteNombre     = $estudiante->name;
        $this->carreraId            = $matricula?->carrera_id;
        $this->carreraNombre        = $matricula?->carrera?->name ?? '';
        $this->busquedaEstudiante   = $estudiante->name;
        $this->showDropdownEstudiante = false;
    }

    public function updatedBusquedaEstudiante(): void
    {
        // Si el usuario borra el campo, limpiar la selección
        if (empty($this->busquedaEstudiante)) {
            $this->estudianteId   = null;
            $this->estudianteNombre = '';
            $this->carreraId      = null;
            $this->carreraNombre  = '';
        }
        $this->showDropdownEstudiante = strlen($this->busquedaEstudiante) >= 3;
    }

    // =========================================================================
    // ABRIR MODAL CREAR
    // =========================================================================
    public function abrirModalCrear(): void
    {
        $this->resetFormulario();
        $this->modoEdicion = false;
        $this->showModal   = true;
        $this->dispatch('modal-opened');
    }

    // =========================================================================
    // ABRIR MODAL EDITAR
    // =========================================================================
    public function abrirModalEditar(int $comunitariaId): void
    {
        $comuntaria = Comunitaria::with(['estudiante', 'carrera'])->find($comunitariaId);
        if (! $comuntaria) return;

        $this->resetFormulario();
        $this->modoEdicion  = true;
        $this->comunitariaId   = $comunitariaId;

        // Datos del estudiante (fijos en edición)
        $this->estudianteId     = $comuntaria->user_id;
        $this->estudianteNombre = $comuntaria->estudiante?->name ?? '';
        $this->carreraId        = $comuntaria->carrera_id;
        $this->carreraNombre    = $comuntaria->carrera?->name ?? '';

        // Campos

        $this->programa_vinculacion = $comuntaria->programa_vinculacion;
        $this->empresa         = $comuntaria->empresa;
        $this->sector          = $comuntaria->sector ?? '';
        $this->direccion       = $comuntaria->direccion_empresa ?? '';
        $this->tutorEmpresa    = $comuntaria->tutor_empresa;
        $this->cargoTutor      = $comuntaria->cargo_tutor_empresa ?? '';
        $this->telefono        = $comuntaria->telefono_empresa ?? '';
        $this->email           = $comuntaria->email_empresa ?? '';
        $this->cargoEstudiante = $comuntaria->cargo_estudiante;
        $this->actividades     = $comuntaria->actividades_realizadas ?? '';
        $this->fechaInicio     = $comuntaria->fecha_inicio?->format('Y-m-d') ?? '';
        $this->fechaFin        = $comuntaria->fecha_fin?->format('Y-m-d') ?? '';
        $this->totalHoras      = (string) ($comuntaria->total_horas ?? '');
        $this->nota            = (string) ($comuntaria->nota ?? '');
        $this->estado          = $comuntaria->estado;
        $this->observaciones   = $comuntaria->observaciones ?? '';

        // Paths actuales de documentos
        $this->cartaAceptacionPath = $comuntaria->carta_aceptacion_path;
        $this->informeFinalPath    = $comuntaria->informe_final_path;
        $this->certificadoPath     = $comuntaria->certificado_empresa_path;

        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    // =========================================================================
    // GUARDAR (crear o editar)
    // =========================================================================
    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_practicas_comunitarias')) return;

        $this->validate([
            'estudianteId'        => 'required|exists:users,id',
            'carreraId'           => 'required|exists:carreras,id',
            'programa_vinculacion' => 'required|string',
            'empresa'             => 'required|string|max:255',
            'tutorEmpresa'        => 'required|string|max:255',
            'cargoEstudiante'     => 'required|string|max:255',
            'fechaInicio'         => 'required|date',
            'fechaFin'            => 'nullable|date|after_or_equal:fechaInicio',
            'totalHoras'          => 'nullable|integer|min:1',
            'nota'                => 'nullable|numeric|min:0|max:10',
            'estado'              => 'required|in:En_Curso,Completada,Reprobada',
            'sector'              => 'nullable|string|max:255',
            'direccion'           => 'nullable|string|max:255',
            'cargoTutor'          => 'nullable|string|max:255',
            'telefono'            => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:255',
            'cartaAceptacion'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'informeFinal'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificado'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'estudianteId.required'         => 'Debes seleccionar un estudiante.',
            'programa_vinculacion.required'  => 'El nombre del programa de vinculación es requerido.',
            'empresa.required'              => 'El nombre de la empresa es requerido.',
            'tutorEmpresa.required'         => 'El tutor de empresa es requerido.',
            'cargoEstudiante.required'      => 'El cargo del estudiante es requerido.',
            'fechaInicio.required'          => 'La fecha de inicio es requerida.',
            'fechaFin.after_or_equal'       => 'La fecha de fin debe ser igual o posterior al inicio.',
        ]);

        // Validación de horas mínimas y determinación de estado según nota
        $carrera  = Carrera::find($this->carreraId);
        $horasMin = $carrera ? $carrera->horasMinComunitaria() : 120;
        $nota     = $this->nota !== '' ? (float) $this->nota : null;
        $horas    = (int) ($this->totalHoras ?: 0);

        if ($nota !== null && $nota >= 7 && $horas < $horasMin) {
            $this->addError('totalHoras', "Se requieren mínimo {$horasMin} horas para {$carrera->tipo_label} ({$horas} registradas).");
            return;
        }

        // Auto-determinar estado basado en nota
        $estadoCalculado = $this->estado;
        if ($nota !== null) {
            if ($nota >= 7 && $horas >= $horasMin) {
                $estadoCalculado = 'Completada';
            } elseif ($nota < 7) {
                $estadoCalculado = 'Reprobada';
            }
        }

        try {
            DB::beginTransaction();

            $datos = [
                'user_id'                => $this->estudianteId,
                'carrera_id'             => $this->carreraId,
                'programa_vinculacion'   => $this->programa_vinculacion,
                'empresa'                => $this->empresa,
                'sector'                 => $this->sector ?: null,
                'direccion_empresa'      => $this->direccion ?: null,
                'tutor_empresa'          => $this->tutorEmpresa,
                'cargo_tutor_empresa'    => $this->cargoTutor ?: null,
                'telefono_empresa'       => $this->telefono ?: null,
                'email_empresa'          => $this->email ?: null,
                'cargo_estudiante'       => $this->cargoEstudiante,
                'actividades_realizadas' => $this->actividades ?: null,
                'fecha_inicio'           => $this->fechaInicio,
                'fecha_fin'              => $this->fechaFin ?: null,
                'total_horas'            => $horas,
                'nota'                   => $nota,
                'estado'                 => $estadoCalculado,
                'observaciones'          => $this->observaciones ?: null,
            ];

            // Subir documentos si se seleccionaron nuevos
            $estudiante = User::find($this->estudianteId);
            $cedula     = $estudiante->cedula ?? $this->estudianteId;
            $fecha      = now()->format('Ymd');

            if ($this->cartaAceptacion) {
                if ($this->cartaAceptacionPath) Storage::disk('public')->delete($this->cartaAceptacionPath);
                $ext = $this->cartaAceptacion->getClientOriginalExtension();
                $datos['carta_aceptacion_path'] = $this->cartaAceptacion->storeAs(
                    'comunitarias/cartas',
                    "CARTA_{$cedula}_{$fecha}.{$ext}",
                    'public'
                );
            }

            if ($this->informeFinal) {
                if ($this->informeFinalPath) Storage::disk('public')->delete($this->informeFinalPath);
                $ext = $this->informeFinal->getClientOriginalExtension();
                $datos['informe_final_path'] = $this->informeFinal->storeAs(
                    'comunitarias/informes',
                    "INFORME_{$cedula}_{$fecha}.{$ext}",
                    'public'
                );
            }

            if ($this->certificado) {
                if ($this->certificadoPath) Storage::disk('public')->delete($this->certificadoPath);
                $ext = $this->certificado->getClientOriginalExtension();
                $datos['certificado_empresa_path'] = $this->certificado->storeAs(
                    'comunita/certificados',
                    "CERTIFICADO_{$cedula}_{$fecha}.{$ext}",
                    'public'
                );
            }

            if ($this->modoEdicion) {
                Comunitaria::find($this->comunitariaId)?->update($datos);
                $comunitariaGuardada = Comunitaria::find($this->comunitariaId);
                $mensaje = 'Práctica actualizada correctamente.';
            } else {
                $comunitariaGuardada = Comunitaria::create($datos);
                $mensaje = 'Práctica registrada correctamente.';
            }

            // Sincronizar comunitaria_id en notas_titulacion y recalcular nota final
            if ($comunitariaGuardada) {
                $titulacion = NotaTitulacion::where('user_id', $this->estudianteId)
                    ->where('carrera_id', $this->carreraId)
                    ->latest('numero_intento')
                    ->first();

                if ($titulacion) {
                    $titulacion->comunitaria_id = $comunitariaGuardada->id;
                    $titulacion->save();
                    $titulacion->recalcularNotaFinal();
                }
            }

            DB::commit();

            $this->cerrarModal();
            $this->dispatch('swal', ['tipo' => 'success', 'mensaje' => $mensaje]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // ELIMINAR
    // =========================================================================
    public function eliminar(int $comunitariaId): void
    {
        try {
            $comunitaria = Comunitaria::findOrFail($comunitariaId);

            // Eliminar archivos del storage
            if ($comunitaria->carta_aceptacion_path)
                Storage::disk('public')->delete($comunitaria->carta_aceptacion_path);
            if ($comunitaria->informe_final_path)
                Storage::disk('public')->delete($comunitaria->informe_final_path);
            if ($comunitaria->certificado_empresa_path)
                Storage::disk('public')->delete($comunitaria->certificado_empresa_path);

            $comunitaria->delete();
            $this->dispatch('swal', ['tipo' => 'success', 'mensaje' => 'Práctica eliminada correctamente.']);
        } catch (\Exception $e) {
            $this->dispatch('swal', ['tipo' => 'error', 'mensaje' => 'No se pudo eliminar la práctica.']);
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    public function cerrarModal(): void
    {
        $this->dispatch('modal-closed');
        $this->showModal = false;
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->reset([
            'comunitariaId',
            'modoEdicion',
            'busquedaEstudiante',
            'showDropdownEstudiante',
            'estudianteId',
            'estudianteNombre',
            'carreraId',
            'carreraNombre',
            'programa_vinculacion',
            'empresa',
            'sector',
            'direccion',
            'tutorEmpresa',
            'cargoTutor',
            'telefono',
            'email',
            'cargoEstudiante',
            'actividades',
            'fechaInicio',
            'fechaFin',
            'totalHoras',
            'nota',
            'observaciones',
            'cartaAceptacion',
            'informeFinal',
            'certificado',
            'cartaAceptacionPath',
            'informeFinalPath',
            'certificadoPath',
        ]);
        $this->estado = 'En_Curso';
        $this->resetErrorBag();
    }


    public function render()
    {
        return view('livewire.administration.practicas-comunitarias', [
            'comunitarias' => $this->comunitarias,
        ]);
    }
}
