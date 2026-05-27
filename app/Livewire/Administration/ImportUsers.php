<?php

namespace App\Livewire\Administration;

use App\Exports\UsersTemplateExport;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use App\Traits\WithAuthorization;

class ImportUsers extends Component
{
    use WithFileUploads, WithAuthorization;

    public $file;
    public int $importedCount = 0;
    public array $importErrors = [];
    public string $successMessage = '';

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ];

    protected $messages = [
        'file.required'  => 'Debe seleccionar un archivo',
        'file.mimes'     => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
        'file.max'       => 'El archivo no debe pesar más de 10MB',
    ];

    public function resetResults(): void
    {
        $this->importedCount = 0;
        $this->importErrors  = [];
        $this->successMessage = '';
    }

    public function import(): void
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;

        $this->validate();
        $this->resetResults();

        try {
            $data = Excel::toArray([], $this->file)[0];
            array_shift($data);

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                try {
                    if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                        $this->importErrors[] = "Fila {$rowNumber}: Faltan campos obligatorios (nombre, apellido, cédula)";
                        continue;
                    }

                    if (User::where('cedula', $row[2])->first()) {
                        $this->importErrors[] = "Fila {$rowNumber}: La cédula {$row[2]} ya está registrada";
                        continue;
                    }

                    $roleName = !empty($row[3]) ? $row[3] : 'Estudiante';
                    $role     = Role::where('name', $roleName)->first();
                    if (!$role) {
                        $this->importErrors[] = "Fila {$rowNumber}: El rol '{$roleName}' no existe";
                        continue;
                    }

                    $email = !empty($row[4]) ? $row[4] : $this->generateEmail($row[0], $row[1]);

                    if (User::where('email', $email)->exists()) {
                        $this->importErrors[] = "Fila {$rowNumber}: El email {$email} ya está registrado";
                        continue;
                    }

                    $passwordValue = !empty($row[5]) ? (string) $row[5] : '12345678';

                    $user = User::create([
                        'first_name'               => $row[0],
                        'last_name'                => $row[1],
                        'name'                     => $row[0] . ' ' . $row[1],
                        'cedula'                   => $row[2],
                        'email'                    => $email,
                        'password'                 => Hash::make($passwordValue),
                        'phone'                    => $row[6] ?? null,
                        'address'                  => $row[7] ?? null,
                        'fecha_nacimiento'         => !empty($row[8]) ? $row[8] : null,
                        'matricula_numero'         => $row[9] ?? null,
                        'padre'                    => $row[10] ?? null,
                        'madre'                    => $row[11] ?? null,
                        'tutor'                    => $row[12] ?? null,
                        'nacionalidad'             => $row[13] ?? null,
                        'genero'                   => !empty($row[14]) && in_array($row[14], ['Masculino', 'Femenino', 'Otro']) ? $row[14] : null,
                        'estado_civil'             => $row[15] ?? null,
                        'telefono_emergencia'      => $row[16] ?? null,
                        'contacto_emergencia'      => $row[17] ?? null,
                        'tipo_sangre'              => $row[18] ?? null,
                        'observaciones_medicas'    => $row[19] ?? null,
                        'discapacidad'             => !empty($row[20]) && strtolower((string) $row[20]) === 'si',
                        'discapacidad_descripcion' => $row[21] ?? null,
                        'is_facturador'            => !empty($row[22]) && strtolower((string) $row[22]) === 'si',
                        'fact_nombre'              => $row[23] ?? null,
                        'fact_documento'           => $row[24] ?? null,
                        'fact_correo'              => $row[25] ?? null,
                        'fact_direccion'           => $row[26] ?? null,
                        'fact_telefono'            => $row[27] ?? null,
                        'is_active'                => true,
                    ]);

                    $user->assignRole($role);
                    $this->importedCount++;
                } catch (\Exception $e) {
                    $this->importErrors[] = "Fila {$rowNumber}: Error - " . $e->getMessage();
                }
            }

            $this->file = null;

            $errCount = count($this->importErrors);

            if ($this->importedCount > 0) {
                $this->successMessage = "Se importaron {$this->importedCount} usuario(s) exitosamente.";
                $this->dispatch('swal', [
                    'icon'              => $errCount > 0 ? 'warning' : 'success',
                    'title'             => '¡Importación completada!',
                    'text'              => "Se importaron {$this->importedCount} usuario(s)" . ($errCount > 0 ? " con {$errCount} error(es)." : " correctamente."),
                    'timer'             => 4000,
                    'showConfirmButton' => false,
                ]);
            } else {
                $this->dispatch('swal', [
                    'icon'              => 'error',
                    'title'             => 'Sin usuarios importados',
                    'text'              => 'No se importó ningún usuario. Revisa los errores listados.',
                    'confirmButtonText' => 'Revisar',
                ]);
            }
        } catch (\Exception $e) {
            $this->importErrors[] = "Error general: " . $e->getMessage();
        }
    }

    private function generateEmail(string $firstName, string $lastName): string
    {
        $base    = strtolower(str_replace(' ', '', $firstName . '.' . $lastName));
        $email   = $base . '@institucion.edu';
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base . $counter . '@institucion.edu';
            $counter++;
        }
        return $email;
    }

    public function downloadTemplate()
    {
        return Excel::download(new UsersTemplateExport(), 'plantilla_usuarios.xlsx');
    }

    public function render()
    {
        return view('livewire.administration.import-users');
    }
}
