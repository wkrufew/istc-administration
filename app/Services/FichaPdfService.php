<?php

namespace App\Services;

use App\Models\Aspirante;
use App\Models\AuditoriaGeneral;
use App\Models\ExpedienteDocumento;
use App\Models\Matricula;
use App\Models\MateriasArrastrada;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\User;
use App\Support\FichaPersona;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Ficha en PDF del estudiante, del aspirante y del docente. Las tres usan la misma plantilla
 * (pdf/ficha): este servicio arma los bloques que lleva cada una. Los campos y sus nombres
 * salen de FichaPersona, el mismo catálogo de la ficha en pantalla y del portal de admisión.
 *
 * El resumen financiero solo se agrega si se pide y quien descarga tiene ver_obligaciones_financieras.
 * Cada descarga queda en la auditoría (son datos personales).
 */
class FichaPdfService
{
    /** Grupos de datos personales del estudiante y del aspirante (cubren todos los campos de FichaPersona::SECCIONES). */
    public const GRUPOS_PERSONA = [
        'Identificación'          => ['first_name', 'last_name', 'cedula', 'matricula_numero', 'email', 'phone'],
        'Datos personales'        => ['fecha_nacimiento', 'sexo', 'genero', 'estado_civil', 'tipo_sangre', 'etnia', 'pueblo_nacionalidad', 'nacionalidad'],
        'Salud y discapacidad'    => ['tiene_discapacidad', 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis', 'observaciones_medicas'],
        'Nacimiento y residencia' => ['provincia_nacimiento', 'canton_nacimiento', 'pais_residencia', 'provincia_residencia', 'canton_residencia', 'address'],
        'Datos socioeconómicos'   => ['tipo_colegio', 'nombre_colegio', 'ocupacion', 'empleo_ingresos', 'bono_dh', 'ingresos_hogar', 'miembros_hogar'],
        'Familia'                 => ['padre', 'formacion_padre', 'madre', 'formacion_madre', 'tutor'],
        'Contacto de emergencia'  => ['contacto_emergencia', 'telefono_emergencia', 'parentesco_emergencia'],
        'Facturación'             => ['is_facturador', 'fact_nombre', 'fact_documento', 'fact_correo', 'fact_direccion', 'fact_telefono'],
    ];

    /** Datos del docente: los que se registran en su formulario. */
    public const GRUPOS_DOCENTE = [
        'Identificación'         => ['first_name', 'last_name', 'cedula', 'email', 'phone'],
        'Datos personales'       => ['fecha_nacimiento', 'genero', 'estado_civil', 'nacionalidad', 'address'],
        'Discapacidad'           => ['tiene_discapacidad', 'tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis'],
        'Contacto de emergencia' => ['contacto_emergencia', 'telefono_emergencia'],
    ];

    /** Campos que ocupan toda la fila (textos largos). */
    private const ANCHOS = ['address', 'observaciones_medicas', 'fact_direccion', 'nombre_colegio'];

    public static function puedeVerFinanciero(?User $quien = null): bool
    {
        return (bool) ($quien ?? auth()->user())?->can('ver_obligaciones_financieras');
    }

    // ── Fichas ───────────────────────────────────────────────────────────────

    public static function estudiante(User $u, bool $financiero = false): array
    {
        $matriculas = Matricula::with(['periodo:id,code,fecha_inicio,fecha_fin', 'carrera:id,name', 'detalles.materia.semestre:id,name,order'])
            ->where('user_id', $u->id)->orderByDesc('fecha_matricula')->orderByDesc('id')->get();
        $ultima     = $matriculas->first();
        $aspirante  = $u->aspirante()->with('cohorte')->first();
        $esValidacion = ExpedienteService::esValidacion($u);
        $estadoFicha  = FichaPersona::estado($u);
        $faltantes    = FichaPersona::faltantes($u);

        $bloques = [];
        if ($faltantes) {
            $bloques[] = self::nota('Datos obligatorios pendientes', implode(' · ', $faltantes), 'aviso');
        }

        // Académico
        $malla    = $ultima?->carrera_id ? EstadoAcademicoService::estadoMalla($u->id, $ultima->carrera_id) : null;
        $semestre = $ultima?->carrera_id ? (new SemestreActualService())->obtenerSemestreActual($u->id, $ultima->carrera_id) : null;
        $bloques[] = self::campos('Resumen académico', [
            ['Carrera', $ultima?->carrera?->name],
            ['Semestre actual', $semestre?->name ?? $ultima?->detalles->map(fn($d) => $d->materia?->semestre)->filter()->sortByDesc('order')->first()?->name],
            ['Avance de la malla', $malla ? "{$malla['semestres_ok']} de {$malla['semestres_total']} semestres completos" . ($malla['completa'] ? ' (malla completa)' : '') : null],
            ['Estado académico', $u->estado_academico ? Str::headline($u->estado_academico) : null],
            ['Tipo de proceso', $esValidacion ? 'Validación de conocimientos' : 'Proceso regular'],
            ['Ingreso', $aspirante ? 'Por admisión' . ($aspirante->cohorte ? ' · cohorte ' . $aspirante->cohorte->nombre : '') : 'Matrícula directa'],
            ['Primera matrícula', $matriculas->last()?->fecha_matricula?->format('d/m/Y')],
            ['Matrículas registradas', (string) $matriculas->count()],
        ]);

        $bloques[] = self::tabla('Historial de matrículas',
            ['Código', 'Período', 'Tipo', 'Semestre', 'Materias', 'Estado', 'Fecha'],
            $matriculas->map(fn(Matricula $m) => [
                $m->code,
                $m->periodo?->code,
                $m->tipo === Matricula::TIPO_VALIDACION ? 'Validación' : $m->tipo,
                $m->detalles->map(fn($d) => $d->materia?->semestre)->filter()->unique('id')->sortBy('order')->pluck('name')->implode(', ') ?: null,
                (string) $m->detalles->count(),
                str_replace('_', ' ', (string) $m->estado),
                $m->fecha_matricula?->format('d/m/Y'),
            ])->all(),
            'No tiene matrículas registradas.'
        );

        $arrastres = MateriasArrastrada::with('materia.semestre:id,name')->where('user_id', $u->id)->where('estado', 'Arrastrada')->get();
        if ($arrastres->isNotEmpty()) {
            $bloques[] = self::tabla('Materias pendientes (arrastre)', ['Materia', 'Semestre'],
                $arrastres->map(fn($a) => [$a->materia?->name, $a->materia?->semestre?->name])->all());
        }

        $retiros = $u->retiros()->with('matricula:id,code')->orderByDesc('fecha_retiro')->get();
        if ($retiros->isNotEmpty()) {
            $bloques[] = self::tabla('Retiros', ['Fecha', 'Matrícula', 'Tipo', 'Motivo'],
                $retiros->map(fn($r) => [$r->fecha_retiro?->format('d/m/Y'), $r->matricula?->code, Str::headline((string) $r->tipo), $r->motivo])->all());
        }

        $practicas = $u->practicasPreprofesionales()->orderByDesc('fecha_inicio')->get();
        if ($practicas->isNotEmpty()) {
            $bloques[] = self::tabla('Prácticas', ['Empresa / institución', 'Desde', 'Hasta', 'Horas', 'Nota', 'Estado'],
                $practicas->map(fn($p) => [$p->empresa, $p->fecha_inicio?->format('d/m/Y'), $p->fecha_fin?->format('d/m/Y'),
                    $p->total_horas !== null ? (string) $p->total_horas : null, $p->nota !== null ? number_format((float) $p->nota, 2) : null,
                    str_replace('_', ' ', (string) $p->estado)])->all());
        }

        $titulacion = $u->notasTitulacion()->orderByDesc('id')->get();
        if ($titulacion->isNotEmpty()) {
            $bloques[] = self::tabla('Titulación', ['Modalidad', 'Intento', 'Nota de titulación', 'Nota final de egreso', 'Estado'],
                $titulacion->map(fn($t) => [str_replace('_', ' ', (string) $t->tipo_titulacion), (string) $t->numero_intento,
                    $t->nota_titulacion !== null ? number_format((float) $t->nota_titulacion, 2) : null,
                    $t->nota_final_egreso !== null ? number_format((float) $t->nota_final_egreso, 2) : null, $t->estado])->all());
        }

        array_push($bloques, ...self::bloquesPersona($u, self::GRUPOS_PERSONA));
        $bloques[] = self::documentosExpediente($u, 'estudiante');

        if ($financiero) {
            array_push($bloques, ...self::financieroEstudiante($u, $matriculas, $aspirante));
        }

        return self::ficha('Ficha del estudiante', $u, [
            ['C.I.', $u->cedula], ['N.º de matrícula', $u->matricula_numero], ['Correo', $u->email], ['Carrera', $ultima?->carrera?->name],
        ], [
            ['Ficha: ' . FichaPersona::ESTADOS_FICHA[$estadoFicha], $estadoFicha === 'aprobada' ? 'ok' : ($estadoFicha === 'con_observaciones' ? 'mal' : 'neutro')],
            [$esValidacion ? 'Validación de conocimientos' : 'Proceso regular', 'info'],
            [$u->is_active ? 'Cuenta activa' : 'Cuenta inactiva', $u->is_active ? 'ok' : 'mal'],
        ], $bloques, $financiero, $u->ficha_estado === 'con_observaciones' ? $u->ficha_observacion : null);
    }

    public static function aspirante(Aspirante $a, bool $financiero = false): array
    {
        $a->loadMissing(['user', 'cohorte', 'carrera', 'registradoPor']);
        $u = $a->user;
        $convenio = $u->conveniosAplicados()->where('is_active', true)->with('tipoConvenio')->latest('id')->first();
        $faltantes = FichaPersona::faltantes($u);

        $bloques = [];
        if ($faltantes) {
            $bloques[] = self::nota('Datos obligatorios pendientes', implode(' · ', $faltantes), 'aviso');
        }
        if ($razones = $a->estado === 'verificacion' ? $a->razonesNoPuedeAprobar() : []) {
            $bloques[] = self::nota('Falta para aprobar', implode(' · ', $razones), 'aviso');
        }

        $bloques[] = self::campos('Postulación', [
            ['Cohorte', $a->cohorte?->nombre],
            ['Carrera', $a->carrera?->name],
            ['Tipo de proceso', $a->esValidacionConocimientos() ? 'Validación de conocimientos' : 'Proceso regular'],
            ['Estado', Aspirante::ESTADOS[$a->estado] ?? $a->estado],
            ['Fecha de registro', $a->created_at?->format('d/m/Y')],
            ['Registrado por', $a->registradoPor?->name ?? 'El propio aspirante'],
            ['Credenciales enviadas', $a->credenciales_enviadas_at?->format('d/m/Y H:i')],
            ['Convenio', $convenio ? $convenio->tipoConvenio?->nombre . ' · ' . $convenio->porcentajeTexto() : 'Sin convenio'],
            ['Observación general', $a->observacion_general, true],
            ...($a->estado === 'rechazado' ? [['Motivo del rechazo', $a->motivo_rechazo, true]] : []),
        ]);

        array_push($bloques, ...self::bloquesPersona($u, self::GRUPOS_PERSONA));

        // Documentos de admisión (los mismos que revisa Aspirantes → Documentos)
        $docs = [
            ['Cédula de identidad y papeleta de votación', $a->cedula_path, $a->cedula_estado, $a->cedula_observacion],
            ['Título de bachiller', $a->bachiller_path, $a->bachiller_estado, $a->bachiller_observacion],
            ['Documento habilitante', $a->habilitante_path, $a->habilitante_estado, $a->habilitante_observacion],
            ['Comprobante de pago de matrícula', $a->pago_comprobante_path, $a->pago_estado, $a->pago_observacion],
        ];
        if ($a->esValidacionConocimientos()) {
            array_push($docs,
                ['Hoja de vida', $a->hoja_vida_path, $a->hoja_vida_estado, $a->hoja_vida_observacion],
                ['Certificados laborales', $a->cert_laborales_path, $a->cert_laborales_estado, $a->cert_laborales_observacion],
                ['Certificados de cursos', $a->cert_cursos_path, $a->cert_cursos_estado, $a->cert_cursos_observacion],
                ['Mecanizado IESS', $a->mecanizado_iess_path, $a->mecanizado_iess_path ? 'cargado' : null, null],
            );
        }
        $bloques[] = self::tabla('Documentos de admisión', ['Documento', 'Estado', 'Observación'],
            array_map(fn($d) => [$d[0], $d[1] ? Str::ucfirst((string) $d[2]) : 'Sin cargar', $d[3]], $docs));

        if ($financiero) {
            $bloques[] = self::pagoAdmision($a, true);
        }

        return self::ficha('Ficha del aspirante', $u, [
            ['C.I.', $u->cedula], ['Correo', $u->email], ['Carrera', $a->carrera?->name], ['', $a->cohorte?->nombre],
        ], [
            ['Aspirante: ' . (Aspirante::ESTADOS[$a->estado] ?? $a->estado), in_array($a->estado, ['aprobado', 'matriculado'], true) ? 'ok' : ($a->estado === 'rechazado' ? 'mal' : 'neutro')],
            [$a->esValidacionConocimientos() ? 'Validación de conocimientos' : 'Proceso regular', 'info'],
            [$faltantes ? 'Faltan ' . count($faltantes) . ' dato(s) obligatorio(s)' : 'Ficha completa', $faltantes ? 'aviso' : 'ok'],
        ], $bloques, $financiero);
    }

    public static function docente(User $u): array
    {
        $asignaciones = $u->asignacionesDocente()
            ->with(['materia.semestre.carrera:id,name', 'paralelo:id,name', 'periodo:id,code'])
            ->orderByDesc('periodo_id')->get();
        $filas = $asignaciones->map(function ($a) {
            $contrato = AsignacionDocenteService::contratoVigente($a);
            return [
                $a->periodo?->code,
                $a->materia?->semestre?->carrera?->name,
                $a->materia?->name,
                $a->materia?->semestre?->name,
                $a->paralelo?->name,
                AsignacionDocenteService::esVigente($a) ? 'Vigente' : 'Histórica',
                $contrato ? (ExpedienteDocumento::ESTADOS[$contrato->estado] ?? $contrato->estado) : 'Sin contrato',
            ];
        });
        $vigentes = $filas->where(5, 'Vigente')->count();

        $bloques = [];
        array_push($bloques, ...self::bloquesPersona($u, self::GRUPOS_DOCENTE));
        $bloques[] = self::campos('Cuenta y carga docente', [
            ['Estado de la cuenta', $u->is_active ? 'Activa' : 'Inactiva'],
            ['Registrado en el sistema', $u->created_at?->format('d/m/Y')],
            ['Asignaciones registradas', (string) $asignaciones->count()],
            ['Asignaciones vigentes', (string) $vigentes],
            ['Contratos aprobados', (string) $filas->where(6, ExpedienteDocumento::ESTADOS['aprobado'])->count()],
        ]);
        $bloques[] = self::documentosExpediente($u, 'docente');
        $bloques[] = self::tabla('Asignaciones y contratos', ['Período', 'Carrera', 'Materia', 'Semestre', 'Paralelo', 'Situación', 'Contrato'],
            $filas->all(), 'No tiene materias asignadas.', 'general', [0, 4, 5, 6]);

        return self::ficha('Ficha del docente', $u, [
            ['C.I.', $u->cedula], ['Correo', $u->email], ['Celular', $u->phone],
        ], [
            [$u->is_active ? 'Cuenta activa' : 'Cuenta inactiva', $u->is_active ? 'ok' : 'mal'],
            [$vigentes . ' asignación(es) vigente(s)', 'info'],
        ], $bloques, false);
    }

    // ── PDF y auditoría ──────────────────────────────────────────────────────

    /** Genera el PDF y lo devuelve para verse en el navegador (desde ahí se imprime o descarga). */
    public static function respuesta(array $ficha, string $nombreArchivo)
    {
        return response(self::generar($ficha), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"',
            'Cache-Control'       => 'private, no-store',
        ]);
    }

    /** El PDF en bytes (con "Página X de Y" en cada hoja). */
    public static function generar(array $ficha): string
    {
        $pdf = Pdf::loadView('pdf.ficha', ['ficha' => $ficha, 'instituto' => self::instituto()])->setPaper('a4', 'portrait');
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fuente = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        // A4 en puntos: 595 × 842. A la altura del pie, alineado a la derecha del margen (14 mm ≈ 40 pt)
        $canvas->page_text($canvas->get_width() - 40 - 62, $canvas->get_height() - 47, 'Página {PAGE_NUM} de {PAGE_COUNT}', $fuente, 5.5, [0.39, 0.45, 0.55]);

        return $dompdf->output();
    }

    /** Deja constancia de quién descargó la ficha de quién, y si incluyó el resumen financiero. */
    public static function auditarDescarga(Model $auditable, User $persona, string $ficha, bool $financiero): void
    {
        AuditoriaGeneral::create([
            'auditable_type'  => $auditable::class,
            'auditable_id'    => $auditable->getKey(),
            'evento'          => 'exportado',
            'valores_antes'   => null,
            'valores_despues' => [
                'accion'             => 'Descargó la ' . mb_strtolower($ficha) . ' en PDF',
                'user_id'            => $persona->id,
                'resumen_financiero' => $financiero ? 'Sí' : 'No',
            ],
            'user_id'         => auth()->id(),
            'ip_address'      => request()?->ip(),
        ]);
    }

    public static function nombreArchivo(string $prefijo, User $u): string
    {
        return $prefijo . '-' . Str::slug(($u->cedula ?: $u->id) . ' ' . $u->name) . '-' . now()->format('Ymd') . '.pdf';
    }

    // ── Piezas ───────────────────────────────────────────────────────────────

    private static function ficha(string $titulo, User $u, array $datos, array $chips, array $bloques, bool $financiero, ?string $observacion = null): array
    {
        $foto = $u->profile_photo_path ? storage_path('app/public/' . $u->profile_photo_path) : null;

        return [
            'titulo'      => $titulo,
            'nombre'      => $u->name,
            'iniciales'   => mb_strtoupper(mb_substr(trim((string) ($u->first_name ?: $u->name)), 0, 1) . mb_substr(trim((string) $u->last_name), 0, 1)),
            'foto'        => $foto && is_file($foto) && in_array(strtolower(pathinfo($foto, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'], true) ? $foto : null,
            'datos'       => array_values(array_filter($datos, fn($d) => filled($d[1]))),
            'chips'       => $chips,
            'observacion' => $observacion,
            'bloques'     => array_values(array_filter($bloques)),
            'financiero'  => $financiero,
            'generado'    => now()->format('d/m/Y H:i'),
            'generadoPor' => auth()->user()?->name ?? 'Sistema',
        ];
    }

    /** Bloques de datos personales; omite lo que no aplica (detalle de discapacidad sin discapacidad, etc.). */
    private static function bloquesPersona(User $u, array $grupos): array
    {
        $bloques = [];
        foreach ($grupos as $titulo => $campos) {
            $campos = array_values(array_filter($campos, fn($c) => self::aplica($u, $c)));
            $bloques[] = self::campos($titulo, array_map(fn($c) => [FichaPersona::ETIQUETAS[$c] ?? Str::headline($c), self::valor($u, $c), in_array($c, self::ANCHOS, true)], $campos));
        }
        return $bloques;
    }

    private static function aplica(User $u, string $campo): bool
    {
        return match (true) {
            $campo === 'pueblo_nacionalidad'                                             => $u->etnia === 'Indígena' || filled($u->pueblo_nacionalidad),
            in_array($campo, ['tipo_discapacidad', 'porcentaje_discapacidad', 'nro_conadis'], true) => (bool) $u->tiene_discapacidad || filled($u->$campo),
            str_starts_with($campo, 'fact_')                                             => ! $u->is_facturador || filled($u->$campo),
            $campo === 'matricula_numero'                                                => filled($u->matricula_numero) || \App\Support\PerfilUsuario::esEstudiante($u),
            default                                                                      => true,
        };
    }

    /** Valor legible de un campo de la ficha (null = sin registrar). */
    private static function valor(User $u, string $campo): ?string
    {
        $v = $u->$campo;

        return match (true) {
            in_array($campo, ['tiene_discapacidad', 'is_facturador'], true) => $v ? 'Sí' : 'No',
            blank($v)                                                       => null,
            $campo === 'fecha_nacimiento' => ($f = Carbon::parse($v))->format('d/m/Y') . ' (' . $f->age . ' años)',
            $campo === 'porcentaje_discapacidad' => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.') . '%',
            $v instanceof \DateTimeInterface => $v->format('d/m/Y'),
            default => (string) $v,
        };
    }

    private static function documentosExpediente(User $u, string $perfil): array
    {
        $vigentes = ExpedienteDocumento::vigentes()->where('user_id', $u->id)->whereNull('asignacion_docente_id')
            ->with('revisadoPor:id,name')->latest('id')->get();

        $filas = [];
        foreach (ExpedienteService::tiposVisibles($u, $perfil) as $tipo => $nombre) {
            $doc = $vigentes->firstWhere('tipo', $tipo);
            $filas[] = self::filaDocumento($nombre, $doc);
        }
        foreach ($vigentes->where('tipo', 'otro') as $doc) {
            $filas[] = self::filaDocumento('Otro: ' . ($doc->descripcion ?: 'documento'), $doc);
        }

        return self::tabla('Documentos del expediente', ['Documento', 'Estado', 'Subido', 'Revisado', 'Observación'], $filas, 'No hay documentos registrados.');
    }

    private static function filaDocumento(string $nombre, ?ExpedienteDocumento $doc): array
    {
        if (! $doc) return [$nombre, 'Sin cargar', null, null, null];

        $estado = $doc->tieneArchivo() || $doc->estado === 'solicitado'
            ? (ExpedienteDocumento::ESTADOS[$doc->estado] ?? $doc->estado)
            : 'Sin cargar';

        return [
            $nombre,
            $estado,
            $doc->subido_at?->format('d/m/Y'),
            $doc->revisado_at ? $doc->revisado_at->format('d/m/Y') . ($doc->revisadoPor ? ' · ' . $doc->revisadoPor->name : '') : null,
            $doc->observacion,
        ];
    }

    /** Resumen financiero del estudiante: totales, beneficios, por matrícula, detalle de obligaciones y pago de admisión. */
    private static function financieroEstudiante(User $u, $matriculas, ?Aspirante $aspirante): array
    {
        $obligaciones = ObligacionesFinanciera::where('user_id', $u->id)
            ->where('estado', '!=', 'Invalidado')
            ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
            ->orderBy('fecha_vencimiento')->orderBy('id')->get();

        $saldoDe   = fn($o) => max(0, round((float) $o->monto_final - (float) $o->pagado, 2));
        $vencidas  = $obligaciones->filter(fn($o) => $saldoDe($o) > 0 && $o->fecha_vencimiento && $o->fecha_vencimiento->lt(today()));
        $total     = round($obligaciones->sum(fn($o) => (float) $o->monto_final), 2);
        $pagado    = round($obligaciones->sum(fn($o) => min((float) $o->pagado, (float) $o->monto_final)), 2);
        $beca      = $u->becaActiva();
        $convenio  = $u->convenioActivo();

        $bloques = [];
        $bloques[] = self::campos('Totales y beneficios', [
            ['Total de obligaciones', self::dinero($total)],
            ['Total pagado', self::dinero($pagado)],
            ['Saldo pendiente', self::dinero(EstadoAcademicoService::saldoPendiente($u->id))],
            ['Vencido', $vencidas->isNotEmpty() ? self::dinero($vencidas->sum($saldoDe)) . ' en ' . $vencidas->count() . ' obligación(es)' : 'Nada vencido'],
            ['Pagos en revisión', EstadoAcademicoService::pagosEnRevision($u->id) ? 'Sí, hay comprobantes por revisar' : 'No'],
            ['Beca activa', $beca ? $beca->tipoBeca?->nombre . ' · ' . rtrim(rtrim(number_format((float) $beca->porcentaje_aplicado, 2, '.', ''), '0'), '.') . '%' : 'Ninguna'],
            ['Convenio activo', $convenio ? $convenio->tipoConvenio?->nombre . ' · ' . $convenio->porcentajeTexto() : 'Ninguno'],
        ], 'financiero');

        $porMatricula = $obligaciones->groupBy('matricula_id');
        $bloques[] = self::tabla('Saldo por matrícula', ['Matrícula', 'Período', 'Total', 'Pagado', 'Saldo', 'Vencido'],
            $matriculas->filter(fn($m) => $porMatricula->has($m->id))->map(function ($m) use ($porMatricula, $saldoDe) {
                $obs = $porMatricula[$m->id];
                return [$m->code, $m->periodo?->code,
                    self::dinero($obs->sum(fn($o) => (float) $o->monto_final)),
                    self::dinero($obs->sum(fn($o) => min((float) $o->pagado, (float) $o->monto_final))),
                    self::dinero($obs->sum($saldoDe)),
                    self::dinero($obs->filter(fn($o) => $saldoDe($o) > 0 && $o->fecha_vencimiento?->lt(today()))->sum($saldoDe))];
            })->values()->all(), 'No tiene obligaciones registradas.', 'financiero');

        $matriculaCode = $matriculas->pluck('code', 'id');
        $bloques[] = self::tabla('Detalle de obligaciones', ['Matrícula', 'Concepto', 'Vence', 'Valor', 'Pagado', 'Saldo', 'Estado'],
            $obligaciones->map(fn($o) => [
                $matriculaCode[$o->matricula_id] ?? '—',
                $o->descripcion ? Str::limit($o->descripcion, 70) : self::concepto((string) $o->tipo),
                $o->fecha_vencimiento?->format('d/m/Y'),
                self::dinero((float) $o->monto_final),
                self::dinero(min((float) $o->pagado, (float) $o->monto_final)),
                self::dinero($saldoDe($o)),
                $saldoDe($o) > 0 && $o->fecha_vencimiento?->lt(today()) ? 'Vencido' : $o->estado,
            ])->all(), 'No tiene obligaciones registradas.', 'financiero');

        if ($aspirante) {
            $bloques[] = self::pagoAdmision($aspirante, false);
        }

        return $bloques;
    }

    private static function pagoAdmision(Aspirante $a, bool $conValorEsperado): array
    {
        $resumen = $a->resumenPago();
        $filas = [
            ['Estado del comprobante', $a->pago_comprobante_path ? Str::ucfirst((string) $a->pago_estado) : 'Sin cargar'],
            ['Monto confirmado', $resumen ? self::dinero($resumen['monto']) : ($a->pagoVerificadoSinMonto() ? 'Verificado sin monto registrado' : 'Sin confirmar')],
        ];
        if (! $resumen && (float) $a->pago_monto > 0) {
            $filas[] = ['Monto declarado por el aspirante', self::dinero((float) $a->pago_monto)];
        }
        if ($resumen) {
            array_push($filas,
                ['Método', PagoAdmisionService::METODOS[$resumen['metodo']] ?? $resumen['metodo']],
                ['Fecha del pago', $resumen['fecha_pago']],
                ['Verificado el', $resumen['verificado']],
                ['Aplicado a', $resumen['aplicado'] ? ($resumen['matricula'] ?? 'su matrícula') : 'Aún no aplicado a una matrícula'],
            );
        }
        if ($conValorEsperado) {
            $filas[] = ['Valor de matrícula esperado', self::dinero($a->valorMatriculaAdmision())];
        }

        return self::campos('Pago de admisión', $filas, 'financiero');
    }

    private static function campos(string $titulo, array $campos, string $grupo = 'general'): array
    {
        return ['tipo' => 'campos', 'titulo' => $titulo, 'grupo' => $grupo,
            'campos' => array_map(fn($c) => ['etiqueta' => $c[0], 'valor' => filled($c[1] ?? null) ? (string) $c[1] : null, 'ancho' => (bool) ($c[2] ?? false)], $campos)];
    }

    /** @param int[] $sinPartir columnas cortas que no deben partirse en dos líneas */
    private static function tabla(string $titulo, array $columnas, array $filas, string $vacio = 'Sin registros.', string $grupo = 'general', array $sinPartir = []): array
    {
        return ['tipo' => 'tabla', 'titulo' => $titulo, 'grupo' => $grupo, 'columnas' => $columnas, 'filas' => $filas, 'vacio' => $vacio, 'sinPartir' => $sinPartir];
    }

    private static function concepto(string $tipo): string
    {
        return match ($tipo) {
            'MATRICULA'   => 'Matrícula',
            'COLEGIATURA' => 'Colegiatura',
            'ARRASTRE'    => 'Arrastre',
            'REINTEGRO'   => 'Reintegro',
            default       => Str::ucfirst(mb_strtolower(str_replace('_', ' ', $tipo))),
        };
    }

    private static function nota(string $titulo, string $texto, string $tono = 'info'): array
    {
        return ['tipo' => 'nota', 'titulo' => $titulo, 'texto' => $texto, 'tono' => $tono, 'grupo' => 'general'];
    }

    private static function dinero(float $valor): string
    {
        return '$' . number_format($valor, 2);
    }

    public static function instituto(): array
    {
        $logo = SettingService::get('instituto.logo_path');

        return [
            'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
            'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
            'ruc'          => SettingService::get('instituto.ruc', ''),
            'direccion'    => SettingService::get('instituto.direccion', ''),
            'telefono'     => SettingService::get('instituto.telefono', ''),
            'email'        => SettingService::get('instituto.email', ''),
            'web'          => SettingService::get('instituto.web', ''),
            'logo_file'    => $logo ? storage_path('app/public/' . $logo) : public_path('imagenes/icono.webp'),
        ];
    }
}
