{{-- An extra row in the access panel of /p/ai-memory.
     Rendered by the convention in projects/show.blade.php: a project with a
     partials/projects/<slug>-access.blade.php gets it at the bottom of the
     aside.

     WHY THIS PROJECT HAS TWO. The panel's first row is `external_url`, which is
     akitaonrails/ai-memory — the product the page explains, and the repository
     someone wanting to INSTALL it needs. What it leaves out is the half that is
     ours: samirhvbr/ai-memory-web, the read-only panel whose screens fill the
     rest of this page. A reader who scrolls through nine captures and then has
     to hunt for the repository that produced them was offered the wrong two
     links.

     `upstream_repo` could not carry it — that column already holds
     akitaonrails/ai-memory, which is what the version monitor compares our fork
     against, and it means "the OSS this is a fork of" rather than "a second
     link". So the address lives here, in the one file that also holds the copy
     describing it.

     `has-divider` is unconditional: this row always follows the site row, since
     a project with no `external_url` has no reason for a second link either. --}}
<div class="s-project-action-panel__option has-divider">
    <span class="s-project-action-panel__icon"><i class="fa-brands fa-github"></i></span>
    <div>
        <h2>{{ __('ai_memory.access_web_title') }}</h2>
        <p>{{ __('ai_memory.access_web_desc') }}</p>
    </div>
    <a href="https://github.com/samirhvbr/ai-memory-web" target="_blank" rel="noopener" class="s-btn s-btn--ghost">
        {{ __('ai_memory.access_web_cta') }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
    </a>
</div>
