{{-- The client's own token counts of one run: input, output, cache and their total, as the exact figures the run record keeps. --}}
@php
    $fmt = fn (int $n) => app()->getLocale() === 'pt_BR' ? number_format($n, 0, ',', '.') : number_format($n);
@endphp
<span class="ab-tokens" title="{{ __('ai_benchmark.tokens_help') }}">{{ __('ai_benchmark.tokens_line', ['input' => $fmt($tokens['input']), 'output' => $fmt($tokens['output']), 'cache' => $fmt($tokens['cache']), 'total' => $fmt($tokens['total'])]) }}</span>
