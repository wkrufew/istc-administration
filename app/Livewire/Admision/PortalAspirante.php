<?php

namespace App\Livewire\Admision;

use App\Models\Aspirante;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortalAspirante extends Component
{
    use WithFileUploads;

    // ── Uploads documentos aspirante ─────────────────────────────────────
    public $archivoCedula           = null;
    public $archivoBachiller        = null;
    public $archivoHabilitante      = null;
    public $archivoPago             = null;
    public string $pagoMonto        = '';

    // Upload certificado discapacidad (va en users, no aspirantes)
    public $archivoCertDiscapacidad = null;

    // Uploads para validación de conocimientos
    public $archivoHojaVida       = null;
    public $archivoCertLaborales  = null;
    public $archivoCertCursos     = null;
    public $archivoMecanizadoIess = null;

    // ── Datos personales ──────────────────────────────────────────────────
    public string $phone               = '';
    public string $sexo                = '';
    public string $genero              = '';
    public string $estado_civil        = '';
    public string $tipo_sangre         = '';
    public string $etnia               = '';
    public string $pueblo_nacionalidad = '';  // Opcional — solo si etnia = Indígena
    public string $fecha_nacimiento    = '';
    public string $nacionalidad        = '';  // País de nacionalidad

    // Discapacidad
    public bool   $tiene_discapacidad         = false;
    public string $tipo_discapacidad          = '';
    public string $porcentaje_discapacidad    = '';
    public string $nro_conadis                = '';

    // Dirección
    public string $address = '';

    // ── Procedencia ───────────────────────────────────────────────────────
    public string $provincia_nacimiento  = '';
    public string $canton_nacimiento     = '';
    public string $pais_residencia       = '';
    public string $provincia_residencia  = '';
    public string $canton_residencia     = '';
    public string $tipo_colegio          = '';
    public string $nombre_colegio        = '';

    // ── Datos del hogar / socioeconómicos ─────────────────────────────────
    public string $ocupacion      = '';
    public string $empleo_ingresos = '';
    public string $bono_dh        = '';
    public string $ingresos_hogar = '';
    public string $miembros_hogar = '';

    // ── Familia ───────────────────────────────────────────────────────────
    public string $padre           = '';
    public string $formacion_padre = '';
    public string $madre           = '';
    public string $formacion_madre = '';
    public string $tutor           = '';

    // ── Emergencia ────────────────────────────────────────────────────────
    public string $contacto_emergencia   = '';
    public string $telefono_emergencia   = '';
    public string $parentesco_emergencia = '';

    // ── Facturación ───────────────────────────────────────────────────────
    public bool   $is_facturador  = true;
    public string $fact_nombre    = '';
    public string $fact_documento = '';
    public string $fact_correo    = '';
    public string $fact_direccion = '';
    public string $fact_telefono  = '';

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->can('acceso_admision')) {
            session()->flash('swal', ['icon' => 'error', 'title' => 'Acceso denegado', 'text' => 'No tienes acceso a este portal.']);
            $this->redirectRoute('login');
            return;
        }

        $u = auth()->user();

        $this->phone               = $u->phone               ?? '';
        $this->sexo                = $u->sexo                ?? '';
        $this->genero              = $u->genero              ?? '';
        $this->estado_civil        = $u->estado_civil        ?? '';
        $this->tipo_sangre         = $u->tipo_sangre         ?? '';
        $this->etnia               = $u->etnia               ?? '';
        $this->pueblo_nacionalidad = $u->pueblo_nacionalidad ?? '';
        $this->fecha_nacimiento    = $u->fecha_nacimiento ? $u->fecha_nacimiento->format('Y-m-d') : '';
        $this->nacionalidad        = $u->nacionalidad        ?? '';
        $this->tiene_discapacidad  = (bool) ($u->tiene_discapacidad ?? false);
        $this->tipo_discapacidad   = $u->tipo_discapacidad   ?? '';
        $this->porcentaje_discapacidad = $u->porcentaje_discapacidad ? (string) $u->porcentaje_discapacidad : '';
        $this->nro_conadis         = $u->nro_conadis         ?? '';
        $this->address             = $u->address             ?? '';

        $this->provincia_nacimiento  = $u->provincia_nacimiento  ?? '';
        $this->canton_nacimiento     = $u->canton_nacimiento     ?? '';
        $this->pais_residencia       = $u->pais_residencia       ?? '';
        $this->provincia_residencia  = $u->provincia_residencia  ?? '';
        $this->canton_residencia     = $u->canton_residencia     ?? '';
        $this->tipo_colegio          = $u->tipo_colegio          ?? '';
        $this->nombre_colegio        = $u->nombre_colegio        ?? '';

        $this->ocupacion       = $u->ocupacion       ?? '';
        $this->empleo_ingresos = $u->empleo_ingresos ?? '';
        $this->bono_dh         = $u->bono_dh         ?? '';
        $this->ingresos_hogar  = $u->ingresos_hogar  ?? '';
        $this->miembros_hogar  = $u->miembros_hogar  ?? '';

        $this->padre           = $u->padre           ?? '';
        $this->formacion_padre = $u->formacion_padre ?? '';
        $this->madre           = $u->madre           ?? '';
        $this->formacion_madre = $u->formacion_madre ?? '';
        $this->tutor           = $u->tutor           ?? '';

        $this->contacto_emergencia   = $u->contacto_emergencia   ?? '';
        $this->telefono_emergencia   = $u->telefono_emergencia   ?? '';
        $this->parentesco_emergencia = $u->parentesco_emergencia ?? '';

        $this->is_facturador  = $u->is_facturador  ?? true;
        $this->fact_nombre    = $u->fact_nombre    ?? '';
        $this->fact_documento = $u->fact_documento ?? '';
        $this->fact_correo    = $u->fact_correo    ?? '';
        $this->fact_direccion = $u->fact_direccion ?? '';
        $this->fact_telefono  = $u->fact_telefono  ?? '';
    }

    public function updatedIsFacturador(bool $value): void
    {
        if ($value) {
            $this->fact_nombre = $this->fact_documento = $this->fact_correo = $this->fact_direccion = $this->fact_telefono = '';
        }
    }

    public function render()
    {
        $aspirante      = auth()->user()?->aspirante?->load(['cohorte.carrera.semestres.materias']);
        $primerSemestre = $aspirante?->cohorte?->carrera?->semestres?->first();
        $certPath       = auth()->user()?->certificado_discapacidad_path;

        return view('livewire.admision.portal-aspirante', compact('aspirante', 'primerSemestre', 'certPath'))
            ->layout('layouts.admision');
    }

    // ── Guardar ficha personal ────────────────────────────────────────────

    public function guardarFicha(): void
    {
        $this->validate([
            'phone'              => 'nullable|string|max:20',
            'sexo'               => 'nullable|string|max:20',
            'genero'             => 'nullable|string|max:50',
            'estado_civil'       => 'nullable|string|max:30',
            'tipo_sangre'        => 'nullable|string|max:10',
            'etnia'              => 'nullable|string|max:60',
            'pueblo_nacionalidad'=> 'nullable|string|max:80',
            'fecha_nacimiento'   => 'nullable|date',
            'nacionalidad'       => 'nullable|string|max:80',
            'tiene_discapacidad' => 'boolean',
            'tipo_discapacidad'  => 'nullable|string|max:60',
            'porcentaje_discapacidad' => 'nullable|numeric|min:0|max:100',
            'nro_conadis'        => 'nullable|string|max:50',
            'address'            => 'nullable|string|max:255',
            'provincia_nacimiento' => 'nullable|string|max:80',
            'canton_nacimiento'    => 'nullable|string|max:80',
            'pais_residencia'      => 'nullable|string|max:80',
            'provincia_residencia' => 'nullable|string|max:80',
            'canton_residencia'    => 'nullable|string|max:80',
            'tipo_colegio'         => 'nullable|string|max:60',
            'nombre_colegio'       => 'nullable|string|max:150',
            'ocupacion'            => 'nullable|string|max:100',
            'empleo_ingresos'      => 'nullable|string|max:200',
            'bono_dh'              => 'nullable|in:SI,NO',
            'ingresos_hogar'       => 'nullable|string|max:100',
            'miembros_hogar'       => 'nullable|string|max:10',
            'padre'                => 'nullable|string|max:100',
            'formacion_padre'      => 'nullable|string|max:80',
            'madre'                => 'nullable|string|max:100',
            'formacion_madre'      => 'nullable|string|max:80',
            'tutor'                => 'nullable|string|max:100',
            'contacto_emergencia'  => 'nullable|string|max:100',
            'telefono_emergencia'  => 'nullable|string|max:20',
            'parentesco_emergencia'=> 'nullable|string|max:50',
            'is_facturador'        => 'boolean',
            'fact_nombre'          => 'nullable|string|max:255',
            'fact_documento'       => 'nullable|string|max:50',
            'fact_correo'          => 'nullable|email',
            'fact_direccion'       => 'nullable|string|max:255',
            'fact_telefono'        => 'nullable|string|max:20',
        ]);

        // Todos los campos de texto libre se guardan en mayúsculas
        $camposTexto = [
            'address', 'pueblo_nacionalidad', 'nro_conadis',
            'provincia_nacimiento', 'canton_nacimiento',
            'provincia_residencia', 'canton_residencia',
            'nombre_colegio', 'ocupacion', 'empleo_ingresos', 'ingresos_hogar',
            'padre', 'madre', 'tutor', 'contacto_emergencia',
            'fact_nombre', 'fact_direccion',
        ];
        foreach ($camposTexto as $campo) {
            if ($this->$campo) {
                $this->$campo = strtoupper($this->$campo);
            }
        }

        auth()->user()->update([
            'phone'               => $this->phone               ?: null,
            'sexo'                => $this->sexo                ?: null,
            'genero'              => $this->genero              ?: null,
            'estado_civil'        => $this->estado_civil        ?: null,
            'tipo_sangre'         => $this->tipo_sangre         ?: null,
            'etnia'               => $this->etnia               ?: null,
            'pueblo_nacionalidad' => $this->tiene_discapacidad || $this->etnia === 'Indígena' ? ($this->pueblo_nacionalidad ?: null) : null,
            'fecha_nacimiento'    => $this->fecha_nacimiento    ?: null,
            'nacionalidad'        => $this->nacionalidad        ?: null,
            'tiene_discapacidad'  => $this->tiene_discapacidad,
            'tipo_discapacidad'   => $this->tiene_discapacidad ? ($this->tipo_discapacidad ?: null) : null,
            'porcentaje_discapacidad' => $this->tiene_discapacidad ? ($this->porcentaje_discapacidad ?: null) : null,
            'nro_conadis'         => $this->tiene_discapacidad ? ($this->nro_conadis ?: null) : null,
            'address'             => $this->address             ?: null,
            'provincia_nacimiento'=> $this->provincia_nacimiento ?: null,
            'canton_nacimiento'   => $this->canton_nacimiento   ?: null,
            'pais_residencia'     => $this->pais_residencia     ?: null,
            'provincia_residencia'=> $this->provincia_residencia ?: null,
            'canton_residencia'   => $this->canton_residencia   ?: null,
            'tipo_colegio'        => $this->tipo_colegio        ?: null,
            'nombre_colegio'      => $this->nombre_colegio      ?: null,
            'ocupacion'           => $this->ocupacion           ?: null,
            'empleo_ingresos'     => $this->empleo_ingresos     ?: null,
            'bono_dh'             => $this->bono_dh             ?: null,
            'ingresos_hogar'      => $this->ingresos_hogar      ?: null,
            'miembros_hogar'      => $this->miembros_hogar      ?: null,
            'padre'               => $this->padre               ?: null,
            'formacion_padre'     => $this->formacion_padre     ?: null,
            'madre'               => $this->madre               ?: null,
            'formacion_madre'     => $this->formacion_madre     ?: null,
            'tutor'               => $this->tutor               ?: null,
            'contacto_emergencia' => $this->contacto_emergencia ?: null,
            'telefono_emergencia' => $this->telefono_emergencia ?: null,
            'parentesco_emergencia' => $this->parentesco_emergencia ?: null,
            'is_facturador'       => $this->is_facturador,
            'fact_nombre'         => ! $this->is_facturador ? ($this->fact_nombre    ?: null) : null,
            'fact_documento'      => ! $this->is_facturador ? ($this->fact_documento ?: null) : null,
            'fact_correo'         => ! $this->is_facturador ? ($this->fact_correo    ?: null) : null,
            'fact_direccion'      => ! $this->is_facturador ? ($this->fact_direccion ?: null) : null,
            'fact_telefono'       => ! $this->is_facturador ? ($this->fact_telefono  ?: null) : null,
        ]);

        $this->dispatch('swal', [
            'icon'     => 'success',
            'title'    => 'Información guardada',
            'text'     => 'Tu ficha personal fue actualizada correctamente.',
            'toast'    => true,
            'position' => 'top-end',
            'timer'    => 3000,
        ]);
    }

    // ── Enviar a verificación ─────────────────────────────────────────────

    public function enviarRevision(): void
    {
        $aspirante = $this->aspirante();

        if ($aspirante->estado !== 'proceso') {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'No disponible', 'text' => 'Esta acción solo está disponible cuando tu solicitud está en etapa de proceso.', 'timer' => 4000]);
            return;
        }

        // Validar campos obligatorios desde BD (datos ya guardados)
        $user = auth()->user()->fresh();

        $requeridos = [
            'phone'               => 'Número de celular',
            'sexo'                => 'Sexo',
            'genero'              => 'Género',
            'estado_civil'        => 'Estado civil',
            'etnia'               => 'Etnia',
            'fecha_nacimiento'    => 'Fecha de nacimiento',
            'nacionalidad'        => 'País de nacionalidad',
            'provincia_nacimiento'=> 'Provincia de nacimiento',
            'canton_nacimiento'   => 'Cantón de nacimiento',
            'pais_residencia'     => 'País de residencia',
            'provincia_residencia'=> 'Provincia de residencia',
            'canton_residencia'   => 'Cantón de residencia',
            'address'             => 'Dirección de domicilio',
            'tipo_colegio'        => 'Tipo de colegio',
            'nombre_colegio'      => 'Nombre del colegio',
            'ocupacion'           => 'Ocupación',
            'bono_dh'             => 'Bono de Desarrollo Humano (SI o NO)',
            'ingresos_hogar'      => 'Total de ingresos del hogar',
            'miembros_hogar'      => 'Número de miembros del hogar',
            'formacion_padre'     => 'Formación del padre',
            'formacion_madre'     => 'Formación de la madre',
            'contacto_emergencia' => 'Contacto de emergencia',
            'telefono_emergencia' => 'Teléfono de emergencia',
            'parentesco_emergencia' => 'Parentesco del contacto',
        ];

        $faltantes = [];
        foreach ($requeridos as $campo => $label) {
            if (empty($user->$campo)) {
                $faltantes[] = $label;
            }
        }

        if (! empty($faltantes)) {
            $lista = implode('', array_map(fn ($f) => "<li style='margin:2px 0'>· {$f}</li>", $faltantes));
            $this->dispatch('swal', [
                'icon'             => 'warning',
                'title'            => 'Ficha incompleta',
                'html'             => "<p style='text-align:left;font-size:13px;margin-bottom:6px'>Guarda primero estos campos:</p><ul style='text-align:left;font-size:12px;max-height:200px;overflow-y:auto'>{$lista}</ul>",
                'confirmButtonText'=> 'Entendido',
                'timer'            => 0,
                'showCloseButton'  => true,
            ]);
            return;
        }

        if (! $aspirante->puedeEnviarRevision()) {
            $texto = $aspirante->esValidacionConocimientos()
                ? 'Debes subir la cédula, el documento habilitante, el comprobante de pago, la hoja de vida, los certificados laborales y los certificados de cursos antes de enviar.'
                : 'Debes subir la cédula, el título de bachiller (o documento habilitante) y el comprobante de pago antes de enviar.';
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Documentos incompletos',
                'text'  => $texto,
                'timer' => 7000,
            ]);
            return;
        }

        $aspirante->update(['estado' => 'verificacion']);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => '¡Enviado para revisión!',
            'text'  => 'Tu ficha y documentos fueron enviados. Te notificaremos cuando hayan sido revisados.',
            'timer' => 6000,
        ]);
    }

    // ── Subir documentos aspirante ────────────────────────────────────────

    public function subirCedula(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->cedula_estado === 'rechazado'))) return;
        $this->validate(['archivoCedula' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoCedula.required' => 'Selecciona un archivo.', 'archivoCedula.mimes' => 'Solo PDF, JPG o PNG.', 'archivoCedula.max' => 'Máx. 5 MB.']);
        $path = $this->archivoCedula->store("aspirantes/{$aspirante->id}/cedula", 'public');
        $aspirante->update(['cedula_path' => $path, 'cedula_estado' => 'pendiente']);
        $this->archivoCedula = null;
        $this->notificarSubida('Cédula subida correctamente.');
    }

    public function subirBachiller(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->bachiller_estado === 'rechazado'))) return;
        $this->validate(['archivoBachiller' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoBachiller.required' => 'Selecciona un archivo.', 'archivoBachiller.mimes' => 'Solo PDF, JPG o PNG.', 'archivoBachiller.max' => 'Máx. 5 MB.']);
        $path = $this->archivoBachiller->store("aspirantes/{$aspirante->id}/bachiller", 'public');
        $aspirante->update(['bachiller_path' => $path, 'bachiller_estado' => 'pendiente']);
        $this->archivoBachiller = null;
        $this->notificarSubida('Título de bachiller subido correctamente.');
    }

    public function subirHabilitante(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->habilitante_estado === 'rechazado'))) return;
        $this->validate(['archivoHabilitante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoHabilitante.required' => 'Selecciona un archivo.', 'archivoHabilitante.mimes' => 'Solo PDF, JPG o PNG.', 'archivoHabilitante.max' => 'Máx. 5 MB.']);
        $path = $this->archivoHabilitante->store("aspirantes/{$aspirante->id}/habilitante", 'public');
        $aspirante->update(['habilitante_path' => $path, 'habilitante_estado' => 'pendiente']);
        $this->archivoHabilitante = null;
        $this->notificarSubida('Documento habilitante subido correctamente.');
    }

    public function subirPago(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->pago_estado === 'rechazado'))) return;
        $this->validate([
            'archivoPago' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pagoMonto'   => 'nullable|numeric|min:0',
        ], ['archivoPago.required' => 'Selecciona el comprobante.', 'archivoPago.mimes' => 'Solo PDF, JPG o PNG.', 'archivoPago.max' => 'Máx. 5 MB.']);
        $path = $this->archivoPago->store("aspirantes/{$aspirante->id}/pago", 'public');
        $aspirante->update(['pago_comprobante_path' => $path, 'pago_estado' => 'pendiente', 'pago_monto' => $this->pagoMonto ?: null]);
        $this->archivoPago = null;
        $this->pagoMonto   = '';
        $this->notificarSubida('Comprobante de pago subido correctamente.');
    }

    public function subirHojaVida(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->hoja_vida_estado === 'rechazado'))) return;
        $this->validate(['archivoHojaVida' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoHojaVida.required' => 'Selecciona un archivo.', 'archivoHojaVida.mimes' => 'Solo PDF, JPG o PNG.', 'archivoHojaVida.max' => 'Máx. 5 MB.']);
        $path = $this->archivoHojaVida->store("aspirantes/{$aspirante->id}/hoja_vida", 'public');
        $aspirante->update(['hoja_vida_path' => $path, 'hoja_vida_estado' => 'pendiente']);
        $this->archivoHojaVida = null;
        $this->notificarSubida('Hoja de vida subida correctamente.');
    }

    public function subirCertLaborales(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->cert_laborales_estado === 'rechazado'))) return;
        $this->validate(['archivoCertLaborales' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoCertLaborales.required' => 'Selecciona un archivo.', 'archivoCertLaborales.mimes' => 'Solo PDF, JPG o PNG.', 'archivoCertLaborales.max' => 'Máx. 5 MB.']);
        $path = $this->archivoCertLaborales->store("aspirantes/{$aspirante->id}/cert_laborales", 'public');
        $aspirante->update(['cert_laborales_path' => $path, 'cert_laborales_estado' => 'pendiente']);
        $this->archivoCertLaborales = null;
        $this->notificarSubida('Certificados laborales subidos correctamente.');
    }

    public function subirCertCursos(): void
    {
        $aspirante = $this->aspirante();
        if (! (in_array($aspirante->estado, ['pendiente', 'proceso'], true) ||
               ($aspirante->estado === 'verificacion' && $aspirante->cert_cursos_estado === 'rechazado'))) return;
        $this->validate(['archivoCertCursos' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoCertCursos.required' => 'Selecciona un archivo.', 'archivoCertCursos.mimes' => 'Solo PDF, JPG o PNG.', 'archivoCertCursos.max' => 'Máx. 5 MB.']);
        $path = $this->archivoCertCursos->store("aspirantes/{$aspirante->id}/cert_cursos", 'public');
        $aspirante->update(['cert_cursos_path' => $path, 'cert_cursos_estado' => 'pendiente']);
        $this->archivoCertCursos = null;
        $this->notificarSubida('Certificados de cursos subidos correctamente.');
    }

    public function subirMecanizadoIess(): void
    {
        $aspirante = $this->aspirante();
        if (! in_array($aspirante->estado, ['pendiente', 'proceso'], true)) return;
        $this->validate(['archivoMecanizadoIess' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoMecanizadoIess.required' => 'Selecciona un archivo.', 'archivoMecanizadoIess.mimes' => 'Solo PDF, JPG o PNG.', 'archivoMecanizadoIess.max' => 'Máx. 5 MB.']);
        $path = $this->archivoMecanizadoIess->store("aspirantes/{$aspirante->id}/mecanizado_iess", 'public');
        $aspirante->update(['mecanizado_iess_path' => $path]);
        $this->archivoMecanizadoIess = null;
        $this->notificarSubida('Mecanizado del IESS subido correctamente.');
    }

    public function subirCertDiscapacidad(): void
    {
        $this->validate(['archivoCertDiscapacidad' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'],
            ['archivoCertDiscapacidad.required' => 'Selecciona el certificado.', 'archivoCertDiscapacidad.mimes' => 'Solo PDF, JPG o PNG.', 'archivoCertDiscapacidad.max' => 'Máx. 5 MB.']);
        $user = auth()->user();
        $path = $this->archivoCertDiscapacidad->store("users/{$user->id}/conadis", 'public');
        $user->update(['certificado_discapacidad_path' => $path]);
        $this->archivoCertDiscapacidad = null;
        $this->notificarSubida('Certificado CONADIS subido correctamente.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function aspirante(): Aspirante
    {
        return auth()->user()->aspirante()->firstOrFail();
    }

    private function notificarSubida(string $mensaje): void
    {
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Documento subido', 'text' => $mensaje, 'toast' => true, 'position' => 'top-end', 'timer' => 3000]);
    }
}
