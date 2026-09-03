{{-- The "models behind it" section — only on /p/shvia.
     Tells ShvIA's REAL (hybrid) story: local on-prem models (the prompt never
     leaves Blue3) plus optional cloud providers via BYOK (data does leave
     Blue3, with LGPD masking). The logos carry information (on-prem vs cloud);
     they are not decoration — this site's anti-reference is the "marquee of
     technology icons". --}}
@push('styles')
<style>
    .shm{ margin-top:clamp(2.5rem,5vw,4rem); padding-top:clamp(2rem,4vw,3rem); border-top:1px solid var(--s-line); }
    .shm__title{ font-family:var(--s-sans); font-weight:700; font-size:clamp(1.35rem,2.4vw,1.7rem); letter-spacing:-0.02em; color:var(--s-ink); margin:0 0 .85rem; text-wrap:balance; }
    .shm__lead{ font-size:1rem; line-height:1.7; color:var(--s-ink-2); margin:0; max-width:60ch; text-wrap:pretty; }
    .shm__lead b{ color:var(--s-ink); font-weight:600; }

    /* ── On-prem: the "committed" moment (indigo ink, the real differentiator) ── */
    .shm-onprem{ position:relative; overflow:hidden; margin-top:clamp(1.6rem,3vw,2.2rem);
        background:var(--s-surface); border:1px solid var(--s-line-2); border-radius:var(--s-r-lg);
        padding:clamp(1.15rem,2.2vw,1.6rem); }
    .shm-onprem::before{ content:''; position:absolute; inset:0; background:var(--s-accent-soft); opacity:.55; pointer-events:none; }
    .shm-onprem > *{ position:relative; }
    .shm-onprem__top{ display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:.95rem; }
    .shm-glyph{ width:40px; height:40px; border-radius:11px; background:var(--s-accent-soft-2);
        border:1px solid var(--s-line-2); color:var(--s-accent-ink-2);
        display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
    .shm-glyph svg{ width:23px; height:23px; }
    .shm-label{ font-family:var(--s-mono); font-size:.72rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--s-ink-2); }
    .shm-pill{ display:inline-flex; align-items:center; gap:7px; margin-left:auto;
        font-family:var(--s-sans); font-size:.8rem; font-weight:600; color:#7ee0b0;
        background:rgba(52,211,153,.10); border:1px solid rgba(52,211,153,.30);
        border-radius:var(--s-r-pill); padding:5px 13px; }
    .shm-pill::before{ content:''; width:7px; height:7px; border-radius:50%; background:var(--s-ok); box-shadow:0 0 0 3px rgba(52,211,153,.16); }
    .shm-onprem__models{ font-family:var(--s-sans); font-weight:700; font-size:1.15rem; color:var(--s-ink); letter-spacing:-0.01em; }
    .shm-onprem__sub{ margin-top:.4rem; font-size:.9rem; line-height:1.5; color:var(--s-muted); }
    .shm-onprem__sub code{ font-family:var(--s-mono); font-size:.82em; color:var(--s-accent-ink-2); }

    /* ── Cloud: restrained. The provider row carries information. ── */
    .shm-cloud{ margin-top:clamp(1.5rem,3vw,2rem); }
    .shm-cloud__head{ display:flex; align-items:baseline; justify-content:space-between; gap:10px 16px; flex-wrap:wrap; margin-bottom:.95rem; }
    .shm-cloud__label{ font-family:var(--s-mono); font-size:.72rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--s-muted); }
    .shm-cloud__note{ font-family:var(--s-sans); font-size:.82rem; color:var(--s-ink-2); }
    .shm-chips{ display:flex; flex-wrap:wrap; gap:10px; }
    .shm-chip{ display:inline-flex; align-items:center; gap:9px; padding:8px 14px 8px 11px;
        border:1px solid var(--s-line); border-radius:10px; background:rgba(150,155,185,.04);
        color:var(--s-ink-2); font-family:var(--s-sans); font-weight:500; font-size:.9rem;
        transition:border-color .2s ease, background .2s ease, transform .15s ease, color .2s ease; }
    .shm-chip svg{ width:20px; height:20px; color:var(--s-muted); transition:color .2s ease; flex-shrink:0; }
    .shm-chip:hover{ border-color:var(--s-line-2); background:var(--s-surface-2); transform:translateY(-1px); color:var(--s-ink); }
    .shm-chip:hover svg{ color:var(--s-ink); }
    .shm-chip--more{ font-family:var(--s-mono); font-size:.76rem; color:var(--s-muted); padding:8px 13px; }
    .shm-caption{ display:flex; align-items:flex-start; gap:8px; margin-top:1.05rem; font-size:.85rem; line-height:1.5; color:var(--s-muted); max-width:62ch; }
    .shm-caption::before{ content:''; margin-top:.4em; width:7px; height:7px; border-radius:50%; background:var(--s-warn); opacity:.85; flex-shrink:0; }
    .shm-caption b{ color:var(--s-ink-2); font-weight:600; }

    @media (max-width:560px){ .shm-pill{ margin-left:0; } }
    @media (prefers-reduced-motion: reduce){ .shm-chip{ transition:none; } .shm-chip:hover{ transform:none; } }
</style>
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
