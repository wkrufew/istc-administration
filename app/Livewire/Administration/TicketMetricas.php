<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Traits\WithAuthorization;

class TicketMetricas extends Component
{
    use WithAuthorization;

    #[Url]
    public string $periodo = '30'; // '7' | '30' | '90' | 'all'

    private function desde(): ?Carbon
    {
        return match ($this->periodo) {
            '7'     => now()->subDays(7)->startOfDay(),
            '30'    => now()->subDays(30)->startOfDay(),
            '90'    => now()->subDays(90)->startOfDay(),
            default => null,
        };
    }

    #[Computed]
    public function resumen(): array
    {
        $desde = $this->desde();

        $counts = Ticket::query()
            ->when($desde, fn($q) => $q->where('created_at', '>=', $desde))
            ->selectRaw("
                COUNT(*) as total,
                SUM(estado = 'abierto') as abiertos,
                SUM(estado = 'en_proceso') as en_proceso,
                SUM(estado = 'esperando') as esperando,
                SUM(estado IN ('resuelto','cerrado')) as resueltos,
                SUM(prioridad = 'urgente') as urgentes
            ")
            ->first();

        return [
            'total'      => (int) ($counts->total      ?? 0),
            'abiertos'   => (int) ($counts->abiertos   ?? 0),
            'en_proceso' => (int) ($counts->en_proceso ?? 0),
            'esperando'  => (int) ($counts->esperando  ?? 0),
            'resueltos'  => (int) ($counts->resueltos  ?? 0),
            'urgentes'   => (int) ($counts->urgentes   ?? 0),
        ];
    }

    #[Computed]
    public function tiempos(): array
    {
        $desde = $this->desde();
        $q = Ticket::query()->when($desde, fn($q) => $q->where('created_at', '>=', $desde));

        $respuesta = (clone $q)
            ->whereNotNull('first_response_at')
            ->selectRaw("ROUND(AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) / 60, 1) as horas")
            ->value('horas');

        $resolucion = (clone $q)
            ->whereNotNull('resolved_at')
            ->selectRaw("ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)), 1) as horas")
            ->value('horas');

        return [
            'respuesta'  => $respuesta  !== null ? (float) $respuesta  : null,
            'resolucion' => $resolucion !== null ? (float) $resolucion : null,
        ];
    }

    #[Computed]
    public function chartData(): array
    {
        $desde = $this->desde();
        $base  = fn() => Ticket::query()->when($desde, fn($q) => $q->where('created_at', '>=', $desde));

        // Donut — por estado
        $rawEstado = $base()->selectRaw("estado, COUNT(*) as n")->groupBy('estado')->pluck('n', 'estado');
        $porEstado = [
            'abierto'    => (int) ($rawEstado['abierto']    ?? 0),
            'en_proceso' => (int) ($rawEstado['en_proceso'] ?? 0),
            'esperando'  => (int) ($rawEstado['esperando']  ?? 0),
            'resuelto'   => (int) ($rawEstado['resuelto']   ?? 0),
            'cerrado'    => (int) ($rawEstado['cerrado']    ?? 0),
        ];

        // Barras — por prioridad
        $rawPrio = $base()->selectRaw("prioridad, COUNT(*) as n")->groupBy('prioridad')->pluck('n', 'prioridad');
        $porPrioridad = [
            'baja'    => (int) ($rawPrio['baja']    ?? 0),
            'media'   => (int) ($rawPrio['media']   ?? 0),
            'alta'    => (int) ($rawPrio['alta']    ?? 0),
            'urgente' => (int) ($rawPrio['urgente'] ?? 0),
        ];

        // Línea — tickets por día (máx 90 días en el eje)
        $start = ($desde ?? now()->subDays(89)->startOfDay())->copy();
        $end   = now();

        $rawDia = $base()
            ->when(! $desde, fn($q) => $q->where('created_at', '>=', now()->subDays(89)->startOfDay()))
            ->selectRaw("DATE(created_at) as fecha, COUNT(*) as n")
            ->groupBy('fecha')
            ->pluck('n', 'fecha');

        $labels = [];
        $values = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            $labels[] = $cur->format('d/m');
            $values[] = (int) ($rawDia[$cur->format('Y-m-d')] ?? 0);
            $cur->addDay();
        }

        // Agregar por semana cuando hay más de 60 puntos
        if (count($labels) > 60) {
            $wLabels = [];
            $wValues = [];
            foreach (array_chunk($labels, 7) as $i => $chunk) {
                $wLabels[] = $chunk[0];
                $wValues[] = array_sum(array_slice($values, $i * 7, 7));
            }
            $labels = $wLabels;
            $values = $wValues;
        }

        return compact('porEstado', 'porPrioridad', 'labels', 'values');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-metricas');
    }
}
