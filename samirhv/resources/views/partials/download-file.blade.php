{{-- One downloadable file row. Expects $file (a ProjectFile). --}}
<div class="dl-file">
    <div class="dl-file__info">
        <div class="dl-name">{{ $file->original_name ?: $file->label }}</div>
        <div class="dl-file__meta">
            @if($file->file_type)<span class="dl-badge">{{ $file->file_type }}</span>@endif
            @if($file->arch)<span class="dl-badge dl-badge-arch">{{ $file->arch }}</span>@endif
            <span class="dl-meta">{{ $file->human_size }}</span>
            @if($file->effective_date)<span class="dl-meta">{{ $file->effective_date->translatedFormat('d M Y') }}</span>@endif
            @if($file->short_hash)
                <button type="button" class="dl-copy" data-copy="{{ $file->sha256 }}" title="{{ __('downloads.copy_sha') }}">sha256 {{ $file->short_hash }}… ⧉</button>
            @endif
            <span class="dl-meta">{{ lnumber($file->downloads_count) }} {{ __('downloads.downloads') }}</span>
        </div>
    </div>
    @if($file->is_mirrored)
        <a href="{{ route('download.track', $file) }}" class="s-btn s-btn--sm m-0" style="flex-shrink:0;"><i class="fa-solid fa-download"></i> {{ __('downloads.download') }}</a>
    @else
        <span class="dl-btn-off">{{ __('downloads.publishing') }}</span>
    @endif
</div>
