<?php

namespace App\Services\AiMemory;

use App\Models\AiMemoryStatSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Números derivados do Dashboard do AI-MEMORY.
 *
 * Aqui NÃO se toca em banco: recebe o que os repositórios (e a tabela durável
 * de retratos) já trouxeram e devolve o que a view precisa desenhar — resumos
 * de série, variação em N dias, escala do eixo e caminhos SVG dos gráficos.
 * Fica num service porque é a única lógica de verdade da tela, e assim ela é
 * testável sem subir a view (controllers finos — ver CLAUDE.md).
 */
class DashboardSummary
{
    /**
     * Resumo de uma série diária [Y-m-d => total]: total do período, média,
     * pico (valor + dia) e o valor de hoje. `top` é o teto do eixo Y.
     *
     * @param  array<string,int>  $byDay
     * @return array{total:int,avg:int,max:int,top:int,peak_day:?string,today:int,days:int}
     */
    public function series(array $byDay): array
    {
        $values = array_values($byDay);
        $count = count($values);
        $total = array_sum($values);
        $max = $values ? max($values) : 0;

        $peakDay = null;
        foreach ($byDay as $day => $value) {
            if ($value === $max) {
                $peakDay = $day;   // empate → o pico mais recente
            }
        }

        return [
            'total' => $total,
            'avg' => $count > 0 ? (int) round($total / $count) : 0,
            'max' => $max,
            'top' => $this->niceMax($max),
            'peak_day' => $max > 0 ? $peakDay : null,
            'today' => $count > 0 ? (int) end($values) : 0,
            'days' => $count,
        ];
    }

    /**
     * Variação de um total entre o retrato mais antigo dentro da janela e o
     * número vivo de agora. Devolve também quantos dias esse intervalo tem de
     * fato (os retratos são diários, mas podem faltar) — a UI mostra o real,
     * nunca um "7 dias" que não aconteceu. Null quando não há retrato útil.
     *
     * @param  Collection<int,AiMemoryStatSnapshot>  $history
     * @return array{value:int,days:int}|null
     */
    public function delta(Collection $history, string $metric, int $live, int $days = 7): ?array
    {
        if ($history->isEmpty()) {
            return null;
        }

        $limit = Carbon::today()->subDays($days);
        $reference = $history->last(fn ($snap) => $snap->captured_on->lessThanOrEqualTo($limit))
            ?? $history->first();

        $elapsed = (int) round(abs($reference->captured_on->diffInDays(Carbon::today())));
        if ($elapsed < 1) {
            return null;   // só há retrato de hoje: ainda não dá para comparar
        }

        return ['value' => $live - (int) $reference->{$metric}, 'days' => $elapsed];
    }

    /**
     * Teto "redondo" do eixo Y (passos de meia ordem de grandeza), para o topo
     * do gráfico não ser um número quebrado: 78 → 80, 959 → 1000, 65.453 → 70.000.
     */
    public function niceMax(int $max): int
    {
        if ($max <= 5) {
            return max($max, 1);
        }

        $step = max(1, (10 ** (int) floor(log10($max))) / 2);

        return (int) (ceil($max / $step) * $step);
    }

    /**
     * Sparkline: escala min→max (interessa a FORMA da curva, não o zero) com
     * uma folga vertical para a linha não encostar nas bordas.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,last_y:float,height:float}
     */
    public function sparkline(array $values, float $width = 100, float $height = 30, float $pad = 3): array
    {
        return $this->path($values, $width, $height, null, $pad, false);
    }

    /**
     * Área do histórico: baseline no zero (é um acumulado — a escala tem de ser
     * honesta) e teto explícito, o mesmo do eixo.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,area:string,last_y:float,height:float}
     */
    public function areaPath(array $values, int $top, float $width = 1000, float $height = 220): array
    {
        return $this->path($values, $width, $height, $top, 0, true);
    }

    /** Séries dos retratos por métrica: ['observations' => [1, 2, …], …]. */
    public function historySeries(Collection $history, array $metrics): array
    {
        $out = [];
        foreach ($metrics as $metric) {
            $out[$metric] = $history->map(fn ($snap) => (int) $snap->{$metric})->values()->all();
        }

        return $out;
    }

    /**
     * Gera o caminho SVG de uma série. Coordenadas no espaço do viewBox — o SVG
     * é desenhado com `preserveAspectRatio="none"` e traço `non-scaling-stroke`,
     * então ele acompanha a largura do card sem distorcer a linha.
     *
     * @param  array<int,int>  $values
     * @return array{line:string,area:string,last_y:float,height:float}
     */
    private function path(array $values, float $width, float $height, ?int $forceMax, float $pad, bool $zeroBase): array
    {
        $values = array_values($values);
        $count = count($values);

        if ($count === 0) {
            return ['line' => '', 'area' => '', 'last_y' => $height, 'height' => $height];
        }
        if ($count === 1) {
            $values = [$values[0], $values[0]];
            $count = 2;
        }

        $max = $forceMax ?? max($values);
        $min = $zeroBase ? 0 : min($values);
        $span = max($max - $min, 1);
        $inner = $height - 2 * $pad;
        $step = $width / ($count - 1);

        $points = [];
        $lastY = $height;
        foreach ($values as $i => $value) {
            $x = round($i * $step, 2);
            $y = round($pad + $inner - (($value - $min) / $span) * $inner, 2);
            $points[] = "{$x},{$y}";
            $lastY = $y;
        }

        $line = 'M'.implode(' L', $points);

        return [
            'line' => $line,
            'area' => $line." L{$width},{$height} L0,{$height} Z",
            'last_y' => $lastY,
            'height' => $height,
        ];
    }
}
