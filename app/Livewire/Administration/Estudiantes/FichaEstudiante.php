<?php

namespace App\Livewire\Administration\Estudiantes;

use App\Livewire\Concerns\GestionaFotoPerfil;
use App\Livewire\Concerns\ReenviaCredenciales;
use App\Models\AuditoriaGeneral;
use App\Models\ExpedienteDocumento;
use App\Models\Matricula;
use App\Models\User;
use App\Support\FichaPersona;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Ficha completa del estudiante (sección Estudiantes), por pestañas: datos personales,
 * socioeconómicos, familia y emergencia, facturación, académico, documentos e historial.
 *
 * - Todos los campos son editables por secretaría; cada cambio queda en auditoría.
 * - Secretaría revisa la ficha: aprobada, con observaciones o reabierta. "Incompleta" se calcula.
 * - La cuenta (rol, acceso) se maneja en Usuarios.
 */
class FichaEstudiante extends Component
{
    use WithFileUploads, WithAuthorization, GestionaFotoPerfil, ReenviaCredenciales;

    public User $user;
    public string $tab = 'personales';

    /** Valores del formulario, por nombre de campo (FichaPersona::SECCIONES). */
    public array $f = [];
    public ?string $password = null, $password_confirmation = null;
    public bool $is_active = true;

    // Revisión de la ficha
    public bool $showObservar = false;
    public string $observacionFicha = '';

    const TABS = [
        'personales'      => 'Datos personales',
        'socioeconomicos' => 'Socioeconómicos',
        'familia'         => 'Familia y emergencia',
        'facturacion'     => 'Facturación',
        'academico'       => 'Académico',
        'documentos'      => 'Documentos',
        'historial'       => 'Historial',
    ];

    /** Texto libre que se guarda en mayúsculas, igual que en el portal de admisión. */
    private const MAYUSCULAS = [
        'address', 'pueblo_nacionalidad', 'nro_conadis', 'provincia_nacimiento', 'canton_nacimiento',
        'provincia_residencia', 'canton_residencia', 'nombre_colegio', 'ocupacion', 'empleo_ingresos',
        'ingresos_hogar', 'padre', 'madre', 'tutor', 'contacto_emergencia', 'fact_nombre', 'fact_direccion',
    ];

    public function mount($estudiante): void
    {
        $this->requierePermiso('gestionar_estudiantes');

        $this->user = User::findOrFail($estudiante->id ?? $estudiante);
        $this->cargar();
    }

    private function campos(): array
    {
        return array_merge(...array_values(FichaPersona::SECCIONES));
    }

    private function cargar(): void
    {
        foreach ($this->campos() as $campo) {
            $valor = $this->user->$campo;
            $this->f[$campo] = match (true) {
                $campo === 'fecha_nacimiento' => $valor?->format('Y-m-d'),
                in_array($campo, ['tiene_discapacidad', 'is_facturador'], true) => (bool) $valor,
                default => $valor === null ? null : (string) $valor,
            };
        }
        $this->is_active            = (bool) $this->user->is_active;
        $this->profile_photo_actual = $this->user->profile_photo_path;
    }

    protected function permisoEdicion(): string
    {
        return 'editar_usuarios';
    }

    public function cambiarTab(string $tab): void
    {
        if (array_key_exists($tab, self::TABS)) $this->tab = $tab;
    }

    // ── Guardar ───────────────────────────────────────────────────────────────

    protected function rules(): array
    {
        $id = $this->user->id;
        $texto = fn(int $max) => "nullable|string|max:{$max}";

        return [
            'f.first_name' => 'required|string|max:255',
            'f.last_name'  => 'required|string|max:255',
            'f.cedula'     => ['required', 'string', 'max:20', Rule::unique('users', 'cedula')->ignore($id)],
            'f.email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'f.phone' => $texto(20), 'f.fecha_nacimiento' => 'nullable|date',
            'f.sexo' => $texto(20), 'f.genero' => $texto(50), 'f.estado_civil' => $texto(30), 'f.tipo_sangre' => $texto(10),
            'f.etnia' => $texto(60), 'f.pueblo_nacionalidad' => $texto(80), 'f.nacionalidad' => $texto(80),
            'f.tiene_discapacidad' => 'boolean', 'f.tipo_discapacidad' => $texto(60),
            'f.porcentaje_discapacidad' => 'nullable|numeric|min:0|max:100', 'f.nro_conadis' => $texto(50),
            'f.observaciones_medicas' => 'nullable|string|max:2000', 'f.matricula_numero' => $texto(50),
            'f.provincia_nacimiento' => $texto(80), 'f.canton_nacimiento' => $texto(80), 'f.pais_residencia' => $texto(80),
            'f.provincia_residencia' => $texto(80), 'f.canton_residencia' => $texto(80), 'f.address' => $texto(255),
            'f.tipo_colegio' => $texto(60), 'f.nombre_colegio' => $texto(150), 'f.ocupacion' => $texto(100),
            'f.empleo_ingresos' => $texto(200), 'f.bono_dh' => 'nullable|in:SI,NO', 'f.ingresos_hogar' => $texto(100),
            'f.miembros_hogar' => $texto(10),
            'f.padre' => $texto(100), 'f.formacion_padre' => $texto(80), 'f.madre' => $texto(100), 'f.formacion_madre' => $texto(80),
            'f.tutor' => $texto(100), 'f.contacto_emergencia' => $texto(100), 'f.telefono_emergencia' => $texto(20),
            'f.parentesco_emergencia' => $texto(50),
            'f.is_facturador' => 'boolean', 'f.fact_nombre' => $texto(255), 'f.fact_documento' => $texto(50),
            'f.fact_correo' => 'nullable|email', 'f.fact_direccion' => $texto(255), 'f.fact_telefono' => $texto(20),
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean',
        ] + $this->reglasFoto();
    }

    protected function messages(): array
    {
        return [
            'f.first_name.required' => 'El nombre es obligatorio.',
            'f.last_name.required'  => 'El apellido es obligatorio.',
            'f.cedula.required'     => 'La cédula es obligatoria.',
            'f.cedula.unique'       => 'Esta cédula ya está registrada.',
            'f.email.required'      => 'El correo es obligatorio.',
            'f.email.unique'        => 'Este correo ya está registrado.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
        ];
    }

    public function guardar(): void
    {
        if ($this->sinPermiso($this->permisoEdicion())) return;

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Abre la pestaña del primer error para que se vea
            $campo = str_replace('f.', '', array_key_first($e->errors()));
            if ($seccion = FichaPersona::seccionDe($campo)) $this->tab = $seccion;
            throw $e;
        }

        $datos = [];
        foreach ($this->campos() as $campo) {
            $v = $this->f[$campo] ?? null;
            if (is_string($v)) {
                $v = trim($v);
                if ($v !== '' && in_array($campo, self::MAYUSCULAS, true)) $v = mb_strtoupper($v);
            }
            $datos[$campo] = $v === '' ? null : $v;
        }
        // Los dependientes se vacían si ya no aplican
        if ($datos['etnia'] !== 'Indígena') $datos['pueblo_nacionalidad'] = null;
        if (! $datos['tiene_discapacidad']) $datos['tipo_discapacidad'] = $datos['porcentaje_discapacidad'] = $datos['nro_conadis'] = null;
        if ($datos['is_facturador']) {
            foreach (['fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono'] as $c) $datos[$c] = null;
        }
        $datos['tiene_discapacidad'] = (bool) $datos['tiene_discapacidad'];
        $datos['is_facturador']      = (bool) $datos['is_facturador'];
        $datos['is_active']          = $this->is_active;

        // Solo se guarda (y se audita) lo que de verdad cambió. Los espacios al inicio o al final
        // no cuentan como cambio: muchos datos anteriores los tienen y no se tocan sin motivo.
        $comparable = fn($v) => trim((string) (is_bool($v) ? (int) $v : ($v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v)));
        $antes = $despues = [];
        foreach ($datos as $campo => $nuevo) {
            $anterior = $this->user->$campo;
            // En los campos que se guardan en mayúsculas, solo pasar a mayúsculas no es un cambio
            $iguales = in_array($campo, self::MAYUSCULAS, true)
                ? mb_strtoupper($comparable($anterior)) === mb_strtoupper($comparable($nuevo))
                : $comparable($anterior) === $comparable($nuevo);
            if ($iguales) {
                unset($datos[$campo]);
                continue;
            }
            $anterior = $anterior instanceof \DateTimeInterface ? $anterior->format('Y-m-d') : $anterior;
            $antes[$campo]   = is_bool($anterior) ? ($anterior ? 'Sí' : 'No') : $anterior;
            $despues[$campo] = is_bool($nuevo) ? ($nuevo ? 'Sí' : 'No') : $nuevo;
        }
        if (isset($datos['first_name']) || isset($datos['last_name'])) {
            $datos['name'] = trim(($datos['first_name'] ?? trim((string) $this->user->first_name)) . ' '
                . ($datos['last_name'] ?? trim((string) $this->user->last_name)));
        }

        if ($this->password) {
            $datos['password']      = Hash::make($this->password);
            $despues['contraseña']  = 'Cambiada a mano';
        }
        if ($this->profile_photo) $despues['foto'] = 'Reemplazada';
        $this->aplicarFotoNueva($datos);

        if ($datos) $this->user->update($datos);
        $this->reset(['password', 'password_confirmation']);

        if ($despues) {
            $this->auditar('updated', $antes, $despues);
        }

        $this->user->refresh();
        $this->cargar();
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => $despues ? 'Ficha guardada.' : 'Sin cambios.',
            'text'  => $despues ? count($despues) . ' dato(s) actualizado(s).' : 'No había nada que guardar.',
            'timer' => 2500,
        ]);
    }

    // ── Revisión de la ficha ──────────────────────────────────────────────────

    public function aprobarFicha(): void
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;

        $this->user->refresh();
        if ($faltan = FichaPersona::faltantes($this->user)) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Aún no se puede aprobar',
                'text' => 'Faltan ' . count($faltan) . ' dato(s) obligatorio(s): ' . implode(', ', array_slice($faltan, 0, 6)) . (count($faltan) > 6 ? '…' : '.')]);
            return;
        }
        $this->marcarFicha('aprobada', null);
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Ficha aprobada.', 'timer' => 2500]);
    }

    public function observarFicha(): void
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;

        $this->validate(['observacionFicha' => 'required|string|min:5|max:1000'], [
            'observacionFicha.required' => 'Escribe qué debe corregir o completar.',
            'observacionFicha.min'      => 'Explícalo con un poco más de detalle.',
        ]);
        $this->marcarFicha('con_observaciones', trim($this->observacionFicha));
        $this->reset(['showObservar', 'observacionFicha']);
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Observación registrada.', 'timer' => 2500]);
    }

    public function reabrirFicha(): void
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;
        $this->marcarFicha(null, null);
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Ficha reabierta.', 'text' => 'Vuelve a quedar sin revisar.', 'timer' => 2500]);
    }

    private function marcarFicha(?string $estado, ?string $observacion): void
    {
        $antes = FichaPersona::ESTADOS_FICHA[FichaPersona::estado($this->user)];
        $this->user->update([
            'ficha_estado'       => $estado,
            'ficha_observacion'  => $observacion,
            'ficha_revisada_por' => auth()->id(),
            'ficha_revisada_at'  => now(),
        ]);
        $this->user->refresh();
        $this->auditar('updated', ['ficha' => $antes],
            ['ficha' => FichaPersona::ESTADOS_FICHA[FichaPersona::estado($this->user)]] + ($observacion ? ['observacion' => $observacion] : []));
    }

    private function auditar(string $evento, ?array $antes, array $despues): void
    {
        AuditoriaGeneral::create([
            'auditable_type'  => User::class,
            'auditable_id'    => $this->user->id,
            'evento'          => $evento,
            'valores_antes'   => $antes ?: null,
            'valores_despues' => $despues,
            'user_id'         => auth()->id(),
            'ip_address'      => request()?->ip(),
        ]);
    }

    // ── Datos de las pestañas ─────────────────────────────────────────────────

    private function historial()
    {
        $docs = ExpedienteDocumento::where('user_id', $this->user->id)->pluck('id');

        return AuditoriaGeneral::with('usuario:id,name')
            ->where(fn($q) => $q
                ->where(fn($u) => $u->where('auditable_type', User::class)->where('auditable_id', $this->user->id))
                ->orWhere(fn($d) => $d->where('auditable_type', ExpedienteDocumento::class)->whereIn('auditable_id', $docs)))
            ->latest('id')->limit(100)->get();
    }

    public function render()
    {
        $matriculas = Matricula::with(['carrera:id,name', 'periodo:id,code'])
            ->where('user_id', $this->user->id)->latest('fecha_matricula')->get();

        return view('livewire.administration.estudiantes.ficha-estudiante', [
            'estadoFicha' => FichaPersona::estado($this->user),
            'faltantes'   => FichaPersona::faltantes($this->user),
            'matriculas'  => $matriculas,
            'carrera'     => $matriculas->first()?->carrera?->name,
            'historial'   => $this->tab === 'historial' ? $this->historial() : collect(),
            'revisadaPor' => $this->user->ficha_revisada_por ? User::find($this->user->ficha_revisada_por)?->name : null,
        ]);
    }
}
