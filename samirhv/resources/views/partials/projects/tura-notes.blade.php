{{-- The Tura Notes section — /p/tura-notes.
     Rendered by the convention in projects/show.blade.php: a project whose slug
     has a partial in this folder gets it between the description and the
     changelog.

     WHY THIS SECTION EXISTS. Tura Notes syncs between machines through a server
     the USER runs — there is no account here and no cloud of ours. That is the
     part of the product a download page cannot carry on its own: the reader has
     to know the option exists, that it is optional, and what it costs, before
     they go looking for a "sign in" that does not exist.

     The full guide is English, in the repository, and is the contract this copy
     is written from (docs/SELF-HOSTING.md). This section is the Portuguese and
     English product copy: what it is for, the shape of the setup, and the three
     things to read before deciding — not a second copy of the procedure, which
     would be a copy that goes stale.

     Every string is a lang key (lang/{en,pt_BR}/tura_notes.php). The values are
     repository content and no user input reaches the unescaped echoes. --}}
@push('styles')
    <link rel="stylesheet" href="{{ vasset('css/site/project-sections.css') }}">
@endpush

@php
    $turaCode = fn (string $text) => '<code>'.e($text).'</code>';
@endphp

<section class="ps" aria-labelledby="tura-sync-title">
    <h2 id="tura-sync-title" class="ps__title">{{ __('tura_notes.title') }}</h2>
    <p class="ps__lead">{{ __('tura_notes.lead') }}</p>
    <p class="ps__lead">{{ __('tura_notes.lead_2') }}</p>

    <div class="ps-callout">
        <div class="ps-callout__top">
            <span class="ps-glyph" aria-hidden="true"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <span class="ps-callout__label">{{ __('tura_notes.honest_label') }}</span>
        </div>
        <h3>{{ __('tura_notes.honest_title') }}</h3>
        <p>{{ __('tura_notes.honest_desc') }}</p>
        <p>{{ __('tura_notes.honest_desc_2') }}</p>
    </div>

    <h3 class="ps__sub">{{ __('tura_notes.setup_title') }}</h3>
    <p>{!! __('tura_notes.setup_desc', ['compose' => $turaCode('docker compose')]) !!}</p>
    <pre class="ps-code"><code>{{ __('tura_notes.setup_commands') }}</code></pre>
    <p>{{ __('tura_notes.setup_after') }}</p>

    <h3 class="ps__sub">{{ __('tura_notes.pair_title') }}</h3>
    <p>{!! __('tura_notes.pair_desc', ['panel' => $turaCode(__('tura_notes.pair_panel'))]) !!}</p>
    <div class="ps-table-wrap">
        <table class="ps-table">
            <thead><tr><th>{{ __('tura_notes.pair_col_mode') }}</th><th>{{ __('tura_notes.pair_col_when') }}</th></tr></thead>
            <tbody>
                <tr><td>{{ __('tura_notes.mode_upload') }}</td><td>{{ __('tura_notes.mode_upload_when') }}</td></tr>
                <tr><td>{{ __('tura_notes.mode_download') }}</td><td>{{ __('tura_notes.mode_download_when') }}</td></tr>
                <tr><td>{{ __('tura_notes.mode_reconcile') }}</td><td>{{ __('tura_notes.mode_reconcile_when') }}</td></tr>
            </tbody>
        </table>
    </div>
    <p>{{ __('tura_notes.pair_apply') }}</p>

    <div class="ps-grid">
        <div class="ps-card">
            <i class="fa-solid fa-key" aria-hidden="true"></i>
            <h3>{{ __('tura_notes.c_credential') }}</h3>
            <p>{{ __('tura_notes.c_credential_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
            <h3>{{ __('tura_notes.c_backup') }}</h3>
            <p>{{ __('tura_notes.c_backup_desc') }}</p>
        </div>
        <div class="ps-card">
            <i class="fa-solid fa-server" aria-hidden="true"></i>
            <h3>{{ __('tura_notes.c_existing') }}</h3>
            <p>{{ __('tura_notes.c_existing_desc') }}</p>
        </div>
    </div>

    <p class="ps-note">
        <i class="fa-solid fa-book" aria-hidden="true"></i>
        <span>{!! __('tura_notes.note', [
            'guide' => '<a href="https://github.com/samirhvbr/tura-notes/blob/master/docs/SELF-HOSTING.md" target="_blank" rel="noopener">docs/SELF-HOSTING.md</a>',
        ]) !!}</span>
    </p>
</section>
