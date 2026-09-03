{{-- One version group: heading + files. Expects $group (an array from DownloadPresenter). --}}
<div style="margin-bottom:6px;">
    <div class="d-flex align-items-center" style="gap:10px; margin:18px 0 10px; font-family:var(--s-mono); font-size:.74rem;">
        <span style="color:var(--s-ink-2); font-weight:600;">{{ $group['version'] ? 'v'.$group['version'] : __('downloads.no_version') }}</span>
        @if($group['is_latest'])<span style="color:var(--s-ok);">● {{ __('downloads.latest') }}</span>@endif
        @if($group['date'])<span style="margin-left:auto; color:var(--s-muted);">{{ $group['date']->translatedFormat('d M Y') }}</span>@endif
    </div>
    <div class="s-stack" style="gap:8px;">
        @foreach($group['files'] as $file)
            @include('partials.download-file', ['file' => $file])
        @endforeach
    </div>
</div>
