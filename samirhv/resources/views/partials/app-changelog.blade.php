{{-- What changed, for one application.
     Expects $slug. Renders nothing at all when that app has no entries — an
     empty "What changed" heading is worse than no section. --}}
@php
    $changelog = __('changelog.apps.'.$slug);
    $changelog = is_array($changelog) ? $changelog : [];
    $changelogNote = __('changelog.notes.'.$slug);
    $changelogNote = is_string($changelogNote) && ! str_starts_with($changelogNote, 'changelog.') ? $changelogNote : null;
@endphp

@if(! empty($changelog))
<section class="s-section" style="margin-top:48px;" aria-labelledby="changelog-heading">
    <h2 class="s-h2" id="changelog-heading" style="font-size:1.25rem; margin-bottom:6px;">
        <i class="fa-solid fa-clock-rotate-left" style="color:var(--s-accent-ink-2); margin-right:8px;"></i>{{ __('changelog.heading') }}
    </h2>
    <p class="s-body s-muted" style="font-size:.88rem; margin-bottom:22px;">{{ __('changelog.lead') }}</p>

    <ol class="list-unstyled" style="margin:0; display:flex; flex-direction:column; gap:20px;">
        @foreach($changelog as $release)
            <li style="border-left:2px solid var(--s-line); padding-left:18px;">
                <div class="d-flex align-items-baseline flex-wrap" style="gap:10px; margin-bottom:8px;">
                    <span class="s-h3" style="font-family:'JetBrains Mono',monospace; font-size:.98rem;">v{{ $release['version'] }}</span>
                    @if($loop->first)
                        <span class="s-tag" style="font-size:.7rem;">{{ __('changelog.current') }}</span>
                    @endif
                    <time class="s-meta" datetime="{{ $release['date'] }}" style="font-size:.78rem;">
                        {{ \Carbon\Carbon::parse($release['date'])->translatedFormat('d M Y') }}
                    </time>
                </div>
                <ul class="s-body s-muted" style="font-size:.88rem; line-height:1.75; margin:0; padding-left:18px;">
                    @foreach($release['notes'] as $note)
                        <li>{{ $note }}</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ol>

    @if($changelogNote)
        <p class="s-meta" style="margin-top:20px; line-height:1.7;">
            <i class="fa-solid fa-circle-info" style="color:var(--s-accent-ink-2); margin-right:5px;"></i>{{ $changelogNote }}
        </p>
    @endif
</section>
@endif
