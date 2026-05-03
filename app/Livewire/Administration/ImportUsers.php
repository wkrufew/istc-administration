<?php

namespace App\Livewire\Administration;

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
    public $importedCount = 0;
    public $errors = [];
    public $successMessage = '';

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ];

    protected $messages = [
        'file.required' => 'Debe seleccionar un archivo',
        'file.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
        'file.max' => 'El archivo no debe pesar más de 10MB',
    ];

    public function import()
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;

        $this->validate();

        $this->errors = [];
        $this->importedCount = 0;

        try {
            $data = Excel::toArray([], $this->file)[0];

            // Eliminar la primera fila (encabezados)
            $headers = array_shift($data);

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 porque eliminamos headers y empezamos en 1

                try {
                    // Validar campos obligatorios
                    if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                        $this->errors[] = "Fila {$rowNumber}: Faltan campos obligatorios (nombre, apellido, cédula)";
                        continue;
                    }

                    // Verificar si el usuario ya existe
                    $existingUser = User::where('cedula', $row[2])->first();
                    if ($existingUser) {
                        $this->errors[] = "Fila {$rowNumber}: La cédula {$row[2]} ya está registrada";
                        continue;
                    }

                    // Buscar o crear el rol
                    $roleName = !empty($row[3]) ? $row[3] : 'Estudiante';
                    $role = Role::where('name', $roleName)->first();
                    if (!$role) {
                        $this->errors[] = "Fila {$rowNumber}: El rol '{$roleName}' no existe";
                        continue;
                    }

                    // Generar email si no existe
                    $email = !empty($row[4]) ? $row[4] : $this->generateEmail($row[0], $row[1]);

                    // Verificar email único
                    if (User::where('email', $email)->exists()) {
                        $this->errors[] = "Fila {$rowNumber}: El email {$email} ya está registrado";
                        continue;
                    }
                    $passwordValue = !empty($row[5]) ? (string) $row[5] : '12345678';
                    // Crear usuario con datos opcionales
                    $user = User::create([
                        'first_name' => $row[0],
                        'last_name' => $row[1],
                        'name' => $row[0] . ' ' . $row[1],
                        'cedula' => $row[2],
                        'email' => $email,
                        'password' => Hash::make($passwordValue),
                        'phone' => $row[6] ?? null,
                        'address' => $row[7] ?? null,
                        'fecha_nacimiento' => !empty($row[8]) ? $row[8] : null,
                        'matricula_numero' => $row[9] ?? null,
                        'padre' => $row[10] ?? null,
                        'madre' => $row[11] ?? null,
                        'tutor' => $row[12] ?? null,
                        'nacionalidad' => $row[13] ?? null,
                        'genero' => !empty($row[14]) && in_array($row[14], ['Masculino', 'Femenino', 'Otro']) ? $row[14] : null,
                        'estado_civil' => $row[15] ?? null,
                        'telefono_emergencia' => $row[16] ?? null,
                        'contacto_emergencia' => $row[17] ?? null,
                        'tipo_sangre' => $row[18] ?? null,
                        'observaciones_medicas' => $row[19] ?? null,
                        'discapacidad' => !empty($row[20]) && strtolower($row[20]) == 'si' ? true : false,
                        'discapacidad_descripcion' => $row[21] ?? null,
                        'is_facturador' => !empty($row[22]) && strtolower($row[22]) == 'si' ? true : false,
                        'fact_nombre' => $row[23] ?? null,
                        'fact_documento' => $row[24] ?? null,
                        'fact_correo' => $row[25] ?? null,
                        'fact_direccion' => $row[26] ?? null,
                        'fact_telefono' => $row[27] ?? null,
                        'is_active' => true,
                    ]);

                    $user->assignRole($role);
                    $this->importedCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Fila {$rowNumber}: Error al importar - " . $e->getMessage();
                }
            }

            if ($this->importedCount > 0) {
                $this->successMessage = "Se importaron {$this->importedCount} usuarios exitosamente.";
            }

            $this->file = null;
        } catch (\Exception $e) {
            $this->errors[] = "Error general: " . $e->getMessage();
        }
    }

    private function generateEmail($firstName, $lastName)
    {
        $base = strtolower(str_replace(' ', '', $firstName . '.' . $lastName));
        $email = $base . '@institucion.edu';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base . $counter . '@institucion.edu';
            $counter++;
        }

        return $email;
    }

    public function downloadTemplate()
    {
        $headers = [
            'first_name',
            'last_name',
            'cedula',
            'rol',
            'email',
            'password',
            'phone',
            'address',
            'fecha_nacimiento',
            'matricula_numero',
            'padre',
            'madre',
            'tutor',
            'nacionalidad',
            'genero',
            'estado_civil',
            'telefono_emergencia',
            'contacto_emergencia',
            'tipo_sangre',
            'observaciones_medicas',
            'discapacidad',
            'discapacidad_descripcion',
            'is_facturador',
            'fact_nombre',
            'fact_documento',
            'fact_correo',
            'fact_direccion',
            'fact_telefono'
        ];

        $csv = implode(',', $headers) . "\n";
        $csv .= "Juan,Pérez,1234567890,Estudiante,juan@email.com,12345678,0999999999,Av. Principal 123,1990-01-15,MAT001,Pedro Pérez,María López,,Ecuatoriano,Masculino,Soltero,0988888888,María López,O+,,no,,,,,,,\n";

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'plantilla_usuarios.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.administration.import-users');
    }
}
