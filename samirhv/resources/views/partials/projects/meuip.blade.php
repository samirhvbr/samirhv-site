{{-- The meuip.rs section — /p/meuip.
     Rendered by the convention in projects/show.blade.php: a project whose slug
     has a partial in this folder gets it between the description and the
     changelog.

     WHAT THIS SECTION HAS TO CARRY. meuip.rs is not a download and it is not a
     product with an onboarding — it is an address you curl. So the page has to
     show the shape of the answer, not describe it: the terminal block and the
     endpoint table ARE the explanation, and the prose around them only says why
     one address answers twice.

     Every string is a lang key (lang/{en,pt_BR}/meuip.php). The values are
     repository content and no user input reaches the unescaped echoes. --}}
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/project-sections.css') }}">
@endpush

@php
    $mipCode = fn (string $text) => '<code>'.e($text).'</code>';

    /* The routing table of the front controller, in its order. Kept as data so
       the markup stays one row and the translation stays one key per line. */
    $mipEndpoints = [
        ['meuip.rs', 'ep_root'],
        ['meuip.rs/ip', 'ep_ip'],
        ['meuip.rs/asn', 'ep_asn'],
        ['meuip.rs/isp', 'ep_isp'],
        ['meuip.rs/country', 'ep_country'],
        ['meuip.rs/region', 'ep_region'],
        ['meuip.rs/coord', 'ep_coord'],
        ['meuip.rs/all', 'ep_all'],
        ['meuip.rs/trace', 'ep_trace'],
        ['meuip.rs/doc', 'ep_doc'],
    ];
@endphp

<section class="ps" aria-labelledby="meuip-title">
    <h2 id="meuip-title" class="ps__title">{{ __('meuip.title') }}</h2>
    <p class="ps__lead">{!! __('meuip.lead', ['curl' => $mipCode('curl meuip.rs')]) !!}</p>
    <p class="ps__lead">{!! __('meuip.lead_2', ['accept' => $mipCode('Accept')]) !!}</p>

    <h3 class="ps__sub">{{ __('meuip.terminal_title') }}</h3>
    <div class="ps-code">
<pre><code><span class="prompt">$</span> curl meuip.rs
<span class="out">170.233.230.254</span>

<span class="prompt">$</span> curl meuip.rs/asn
<span class="out">AS265198</span>

<span class="prompt">$</span> curl -s meuip.rs/all | jq .isp
<span class="out">"BLUE3 TELECOM"</span></code></pre>
    </div>
    <p class="ps-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>{{ __('meuip.terminal_note') }}</span></p>

    <h3 class="ps__sub">{{ __('meuip.endpoints_title') }}</h3>
    <div class="ps-table-wrap">
        <table class="ps-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('meuip.endpoints_route') }}</th>
                    <th scope="col">{{ __('meuip.endpoints_answer') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mipEndpoints as [$route, $key])
                    <tr>
                        <td class="cmd">{{ $route }}</td>
                        <td>{{ __('meuip.'.$key) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="ps-callout">
        <div class="ps-callout__top">
            <span class="ps-glyph" aria-hidden="true"><i class="fa-solid fa-route"></i></span>
            <span class="ps-callout__label">{{ __('meuip.trace_label') }}</span>
        </div>
        <h3>{{ __('meuip.trace_title') }}</h3>
        <p>{!! __('meuip.trace_desc', ['trace' => $mipCode('/trace'), 'mtr' => $mipCode('mtr')]) !!}</p>
        <p>{!! __('meuip.trace_desc_2', ['curl' => $mipCode('curl')]) !!}</p>
    </div>

    <h3 class="ps__sub">{{ __('meuip.facts_title') }}</h3>
    <div class="ps-grid">
        <div class="ps-card">
            <i class="fa-solid fa-user-slash" aria-hidden="true"></i>
            <h3>{{ __('meuip.fact_account') }}</h3>
            <p>{{ __('meuip.fact_account_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-key" aria-hidden="true"></i>
            <h3>{{ __('meuip.fact_key') }}</h3>
            <p>{!! __('meuip.fact_key_desc', ['curl' => $mipCode('curl')]) !!}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-cookie-bite" aria-hidden="true"></i>
            <h3>{{ __('meuip.fact_cookie') }}</h3>
            <p>{!! __('meuip.fact_cookie_desc', ['storage' => $mipCode('localStorage')]) !!}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-code" aria-hidden="true"></i>
            <h3>{{ __('meuip.fact_js') }}</h3>
            <p>{{ __('meuip.fact_js_desc') }}</p>
        </div>
    </div>

    <p class="ps-note">
        <i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i>
        <span>{!! __('meuip.note', ['shell' => $mipCode('shell_exec("curl …")')]) !!}</span>
    </p>
</section>
