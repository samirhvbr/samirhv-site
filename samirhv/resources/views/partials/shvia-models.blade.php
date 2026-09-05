{{-- The "models behind it" section — only on /p/shvia.
     Tells ShvIA's REAL (hybrid) story: local on-prem models (the prompt never
     leaves Blue3) plus optional cloud providers via BYOK (data does leave
     Blue3, with LGPD masking). The logos carry information (on-prem vs cloud);
     they are not decoration — this site's anti-reference is the "marquee of
     technology icons". --}}
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/shvia-models.css') }}">
@endpush

<section class="shm" aria-labelledby="shm-title">
    <h2 id="shm-title" class="shm__title">{{ __('shvia_models.title') }}</h2>
    @php($b = fn (string $key) => '<b>'.__('shvia_models.'.$key).'</b>')
    <p class="shm__lead">
        {!! __('shvia_models.lead', ['hybrid' => $b('hybrid'), 'local' => $b('local'), 'cloud' => $b('cloud')]) !!}
    </p>

    {{-- On-prem — the real differentiator, given visual weight. --}}
    <div class="shm-onprem">
        <div class="shm-onprem__top">
            <span class="shm-glyph" aria-hidden="true">
                {{-- Ollama (lhama) --}}
                <svg viewBox="0 0 24 24" fill="currentColor" role="img" aria-label="Ollama">
                    <path d="M8 2.5c.83 0 1.5.72 1.5 1.6v1.44c.8-.2 1.63-.3 2.5-.3s1.7.1 2.5.3V4.1c0-.88.67-1.6 1.5-1.6s1.5.72 1.5 1.6v2.9c0 .53.26 1 .72 1.28C19.6 9.06 20.5 10.3 20.5 11.8v6.1c0 1.16-.9 2.1-2 2.1h-1v-3a1.25 1.25 0 0 0-2.5 0v3h-2v-3a1.25 1.25 0 0 0-2.5 0v3h-1c-1.1 0-2-.94-2-2.1v-6.1c0-1.5.9-2.74 2.28-3.62.46-.28.72-.75.72-1.28V4.1C6.5 3.22 7.17 2.5 8 2.5Z"/>
                </svg>
            </span>
            <span class="shm-label">{{ __('shvia_models.onprem_label') }}</span>
            <span class="shm-pill">{{ __('shvia_models.onprem_pill') }}</span>
        </div>
        <div class="shm-onprem__models">Anna · Shana · Dev</div>
        <p class="shm-onprem__sub">{!! __('shvia_models.onprem_sub', ['ollama' => '<code>Ollama</code>']) !!}</p>
    </div>

    {{-- Cloud — optional, BYOK. A static row that states WHICH providers. --}}
    <div class="shm-cloud">
        <div class="shm-cloud__head">
            <span class="shm-cloud__label">{{ __('shvia_models.cloud_label') }}</span>
            <span class="shm-cloud__note">{{ __('shvia_models.cloud_note') }}</span>
        </div>
        <div class="shm-chips">
            <span class="shm-chip">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l1.5 5.6L18 4.9l-2.4 4.9L21 11l-5.4 1.3L18 17.3l-4.9-2.4L12 20.5l-1.1-5.6L6 17.3l2.4-4.9L3 11l5.4-1.2L6 4.9l4.9 2.7L12 2Z"/></svg>
                Anthropic
            </span>
            <span class="shm-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><ellipse cx="12" cy="12" rx="4" ry="9.2"/><ellipse cx="12" cy="12" rx="4" ry="9.2" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="4" ry="9.2" transform="rotate(120 12 12)"/></svg>
                OpenAI
            </span>
            <span class="shm-chip">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1.6c.55 5.6 4.8 9.85 10.4 10.4-5.6.55-9.85 4.8-10.4 10.4-.55-5.6-4.8-9.85-10.4-10.4C7.2 11.45 11.45 7.2 12 1.6Z"/></svg>
                Gemini
            </span>
            <span class="shm-chip">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M2.7 2.6h4.3l4.02 5.86L15.04 2.6h2.34l-5.22 7.6L21.4 21.4h-4.3l-4.5-6.55L8.05 21.4H5.7l5.5-8L2.7 2.6Z"/></svg>
                xAI
            </span>
            <span class="shm-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 12c0-3 1.7-5.2 4-5.2 3.4 0 5 5.2 5 5.2s1.6 5.2 5 5.2c2.3 0 4-2.2 4-5.2s-1.7-5.2-4-5.2c-3.4 0-5 5.2-5 5.2s-1.6 5.2-5 5.2C4.7 17.2 3 15 3 12Z"/></svg>
                Meta Llama
            </span>
            <span class="shm-chip shm-chip--more">{{ __('shvia_models.cloud_more') }}</span>
        </div>
        <p class="shm-caption">
            {!! __('shvia_models.cloud_caption', ['data_leaves' => $b('data_leaves')]) !!}
        </p>
    </div>
</section>
