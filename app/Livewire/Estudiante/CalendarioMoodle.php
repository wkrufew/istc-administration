<?php

namespace App\Livewire\Estudiante;

use App\Services\MoodleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CalendarioMoodle extends Component
{
    public string $filtro = 'todos'; // todos | tareas | quizzes | vencidos

    public function setFiltro(string $filtro): void
    {
        $this->filtro = in_array($filtro, ['todos', 'tareas', 'quizzes', 'vencidos'])
            ? $filtro
            : 'todos';
        unset($this->eventosFiltrados, $this->proximos, $this->vencidos);
    }

    public function refrescar(): void
    {
        $key = 'moodle_eventos_' . Auth::id() . '_' . (Auth::user()->moodle_id ?? 0);
        Cache::forget($key);
        unset($this->eventos, $this->eventosFiltrados, $this->proximos, $this->vencidos, $this->stats);
    }

    #[Computed]
    public function moodleId(): ?int
    {
        return Auth::user()->moodle_id;
    }

    #[Computed]
    public function moodleActivo(): bool
    {
        return MoodleService::activo();
    }

    #[Computed]
    public function eventos(): array
    {
        if (! $this->moodleId || ! $this->moodleActivo) {
            return [];
        }

        $userId   = Auth::id();
        $moodleId = $this->moodleId;

        try {
            return Cache::remember(
                "moodle_eventos_{$userId}_{$moodleId}",
                now()->addMinutes(10),
                fn () => (new MoodleService())->obtenerEventosCalendario($moodleId)
            );
        } catch (\Throwable $e) {
            Log::warning('CalendarioMoodle: error al obtener eventos', [
                'user_id'   => $userId,
                'moodle_id' => $moodleId,
                'error'     => $e->getMessage(),
            ]);
            return [];
        }
    }

    #[Computed]
    public function eventosFiltrados(): array
    {
        $eventos = $this->eventos;

        return array_values(match ($this->filtro) {
            'tareas'   => array_filter($eventos, fn ($e) => ($e['modulename'] ?? '') === 'assign'),
            'quizzes'  => array_filter($eventos, fn ($e) => ($e['modulename'] ?? '') === 'quiz'),
            'vencidos' => array_filter($eventos, fn ($e) => $e['vencido'] === true),
            default    => $eventos,
        });
    }

    #[Computed]
    public function proximos(): array
    {
        return array_values(array_filter($this->eventosFiltrados, fn ($e) => ! $e['vencido']));
    }

    #[Computed]
    public function vencidos(): array
    {
        return array_values(array_filter($this->eventosFiltrados, fn ($e) => $e['vencido']));
    }

    #[Computed]
    public function stats(): array
    {
        $todos = $this->eventos;

        if (empty($todos)) {
            return ['total' => 0, 'vencidos' => 0, 'esta_semana' => 0, 'cursos_activos' => 0];
        }

        $ahora        = now();
        $inicioSemana = $ahora->copy()->startOfWeek()->timestamp;
        $finSemana    = $ahora->copy()->endOfWeek()->timestamp;

        return [
            'total'          => count($todos),
            'vencidos'       => count(array_filter($todos, fn ($e) => $e['vencido'])),
            'esta_semana'    => count(array_filter($todos, fn ($e) =>
                ! $e['vencido']
                && $e['timestart'] >= $inicioSemana
                && $e['timestart'] <= $finSemana
            )),
            'cursos_activos' => count(array_unique(array_column($todos, 'courseid'))),
        ];
    }

    public function render()
    {
        return view('livewire.estudiante.calendario-moodle');
    }
}
