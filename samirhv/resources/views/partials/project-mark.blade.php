{{-- The square that identifies a project, in one place.

     Four places drew it — home featured, home list, downloads card, project
     header — and each held its own copy of a `<span>` with the Font Awesome
     class inside. So a project that has a real logo had to be taught to four
     templates, or would show a generic glyph in three of them.

     Expects $project. Optional:
       $class    the square's own classes, because the four callers do not share
                 one (`s-icon`, `s-project-mark`, `s-portfolio-item__icon`).
                 Sizing and shape stay where they already were; only what goes
                 INSIDE the square is decided here.
       $fallback a Font Awesome class for a project with no icon of its own.

     Falls through twice: the logo, then the icon, then nothing at all — an
     empty bordered box is worse than no box, which is what the `@if` around
     the old markup was already saying. --}}
@php
    $markUrl = $project->mark_url;
    $markClass = $class ?? 's-icon';
    $markIcon = $project->icon ?: ($fallback ?? null);
@endphp

@if($markUrl)
    <span class="{{ $markClass }} s-mark--image">
        <img src="{{ $markUrl }}" alt="" width="64" height="64" loading="lazy" decoding="async">
    </span>
@elseif($markIcon)
    <span class="{{ $markClass }}"><i class="{{ $markIcon }}"></i></span>
@endif
