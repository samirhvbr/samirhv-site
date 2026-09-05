<!DOCTYPE html>
<html dir="ltr" lang="{{ \App\Support\Locales::tag() }}">
<head>

    {{-- charset first, and in the short form: a parser sniffs the encoding from
         the first bytes of the head, and the http-equiv spelling this used to
         carry is not what every parser looks for. --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', __('shell.meta_description'))">
    <meta name="author" content="Samir Hanna Verza">
    <meta name="theme-color" content="#0b0b11">

    @php $ogImage = asset('img/og-card.png'); @endphp
    <meta property="og:site_name" content="Samirhv">
    <meta property="og:title" content="@yield('title', 'Samirhv') | {{ __('shell.title_suffix') }}">
    <meta property="og:description" content="@yield('description', __('shell.meta_description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', \App\Support\Locales::tag()) }}">
    @foreach(\App\Support\Locales::SUPPORTED as $ogAlt)
        @if($ogAlt !== app()->getLocale())
            <meta property="og:locale:alternate" content="{{ str_replace('-', '_', \App\Support\Locales::tag($ogAlt)) }}">
        @endif
    @endforeach

    {{-- The card declared summary_large_image and supplied no image, so every
         share degraded to a card with no picture. It has one now; the generator
         is tools/make-og-card.php. --}}
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ __('shell.og_image_alt') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Language pair. hreflang must be RECIPROCAL (each page points at itself
         and at its sibling) or Google discards the whole set without reporting
         anything — the symptom is the wrong language in search results, never
         an error. The urls come from the matched route, not from a table: a new
         page joins on its own, and cannot claim a sibling that does not
         resolve. --}}
    @php $alternates = \App\Support\Locales::alternates(); @endphp
    @if(!empty($alternates))
        @foreach($alternates as $altLocale => $altUrl)
            <link rel="alternate" hreflang="{{ str_replace('_', '-', $altLocale) }}" href="{{ $altUrl }}">
        @endforeach
        {{-- x-default is the address that NEGOTIATES: the bare url decides the
             language from the visitor's browser, which is precisely what
             Google reads x-default to mean. --}}
        <link rel="alternate" hreflang="x-default" href="{{ $alternates[\App\Support\Locales::BARE] ?? url()->current() }}">
        <link rel="canonical" href="{{ $alternates[app()->getLocale()] ?? url()->current() }}">
    @endif

    <title>@yield('title', 'Samirhv') — {{ __('shell.title_suffix') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- No preconnect here. It pointed at Google Fonts on every public page,
         and no public page loads a Google font — the only @import lives in the
         standalone login view. Two handshakes opened for nothing. --}}

    <link rel="stylesheet" href="{{ asset('vendor/canvas/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/blog-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/custom.css') }}">

    @stack('styles')

    {{-- Structured data. A search engine reading a download page cannot tell an
         application from a blog post out of prose alone; this says which it is.
         JSON_UNESCAPED_* keeps accented titles readable in the source, and
         JSON_HEX_TAG closes the `</script>` escape. --}}
    @php
        $siteLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Samirhv',
            'url' => \App\Support\Locales::homeUrl(app()->getLocale()),
            'inLanguage' => \App\Support\Locales::tag(),
            'description' => __('shell.meta_description'),
            'author' => [
                '@type' => 'Person',
                'name' => 'Samir Hanna Verza',
                'url' => 'https://github.com/samirhvbr',
            ],
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($siteLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>

    @stack('head')

    {{-- Matomo Analytics (self-hosted) — só renderiza com MATOMO_* configurado. --}}
    @include('partials.matomo')
</head>

<body class="stretched dark">

    <a class="s-skip" href="#content">{{ __('shell.skip_to_content') }}</a>

    <div id="wrapper">

        <!-- Header -->
        <header id="header" class="transparent-header dark">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row s-site-header">

                        <div id="logo">
                            <a href="{{ lroute('home') }}" aria-label="{{ __('shell.home_aria') }}">
                                <span class="s-logo">samirhv<b>.</b></span>
                            </a>
                        </div>

                        <div class="primary-menu-trigger" data-target=".primary-menu">
                            <button class="cnvs-hamburger" type="button" title="{{ __('shell.menu_open') }}" aria-label="{{ __('shell.menu_open') }}">
                                <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                            </button>
                        </div>

                        <nav class="primary-menu on-click" aria-label="{{ __('shell.nav_main') }}">
                            <ul class="menu-container">
                                <li class="menu-item"><a class="menu-link" href="{{ lroute('home') }}"><div>{{ __('shell.home') }}</div></a></li>
                                @php $navProjects = $navProjects ?? collect(); @endphp
                                @if($navProjects->isNotEmpty())
                                <li class="menu-item s-dd-parent">
                                    {{-- Was `<a href="#" onclick="return false;">`: an anchor that
                                         navigates nowhere, announced as a link, with no way to open
                                         the menu from a keyboard — the submenu only appeared on
                                         :hover. A button says what it does, carries the expanded
                                         state, and works without JavaScript because the CSS also
                                         opens on :focus-within. --}}
                                    <button type="button" class="menu-link s-dd-trigger" aria-expanded="false" aria-controls="nav-projects" data-dd-trigger>
                                        <div>{{ __('shell.projects') }} <i class="bi-chevron-down s-caret" aria-hidden="true"></i></div>
                                    </button>
                                    <ul class="s-dd" id="nav-projects">
                                        @foreach($navProjects as $navp)
                                        <li>
                                            @if($navp->redirectsToSite())
                                                <a href="{{ $navp->external_url }}" target="_blank" rel="noopener">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-up-right-from-square' }}"></i>
                                                    <span class="s-dd-text"><strong>{{ \App\Support\Content::project($navp, 'title') }}</strong><small>{{ preg_replace('#^www\.#', '', parse_url($navp->external_url, PHP_URL_HOST)) }}&nbsp;↗</small></span>
                                                </a>
                                            @else
                                                <a href="{{ lroute('project.show', $navp) }}">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-box-open' }}"></i>
                                                    <span class="s-dd-text"><strong>{{ \App\Support\Content::project($navp, 'title') }}</strong><small>{{ \App\Support\Content::category($navp->category) ?: 'download' }}</small></span>
                                                </a>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @else
                                <li class="menu-item"><a class="menu-link" href="{{ lroute('downloads') }}"><div>{{ __('shell.projects') }}</div></a></li>
                                @endif
                                <li class="menu-item"><a class="menu-link" href="{{ lroute('downloads') }}"><div>{{ __('shell.downloads') }}</div></a></li>
                            </ul>
                        </nav>

                        <div class="header-misc d-none d-lg-flex align-items-center gap-3">
                            {{-- Language switcher: real links, not a <select>.
                                 With JavaScript off a select does not navigate,
                                 and a control that is dead on screen is worse
                                 than no control. `to` carries you to the SAME
                                 page in the other language — a switcher that
                                 drops you at the home page is one nobody uses
                                 twice. --}}
                            <nav class="s-lang" aria-label="{{ __('shell.language') }}">
                                @foreach(\App\Support\Locales::SUPPORTED as $langOption)
                                    <a href="{{ \App\Support\Locales::switchUrl($langOption) }}"
                                       hreflang="{{ str_replace('_', '-', $langOption) }}"
                                       @if($langOption === app()->getLocale()) aria-current="page" @endif
                                    >{{ \App\Support\Locales::shortLabel($langOption) }}</a>
                                @endforeach
                            </nav>
                            <a href="{{ lroute('downloads') }}" class="s-btn s-btn--sm s-header-action m-0">
                                {{ __('shell.explore_releases') }} <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="header-wrap-clone"></div>
        </header>

        <main id="content" tabindex="-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer id="footer" class="dark">
            <div class="container">
                <div class="footer-widgets-wrap py-5">
                    <div class="row g-4 g-lg-5">

                        <!-- Brand -->
                        <div class="col-12 col-lg-5">
                            <a href="{{ lroute('home') }}" style="text-decoration: none;">
                                <p class="s-logo" style="font-size: 1.6rem; margin-bottom: 14px;">samirhv<b>.</b></p>
                            </a>
                            <p class="s-body s-muted" style="max-width: 36ch; font-size: 0.94rem; line-height: 1.75;">
                                {{ __('shell.tagline') }}
                            </p>
                            <div class="d-flex gap-2 mt-3">
                                <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-icon" style="width:42px;height:42px;font-size:1.05rem;" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                                <a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="s-icon" style="width:42px;height:42px;font-size:1.05rem;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="s-icon" style="width:42px;height:42px;font-size:1.05rem;" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="col-6 col-lg-3 offset-lg-1">
                            <h4>{{ __('shell.nav') }}</h4>
                            <ul class="list-unstyled mb-0" style="display: flex; flex-direction: column; gap: 10px;">
                                <li><a href="{{ lroute('home') }}" class="s-flink">{{ __('shell.home') }}</a></li>
                                <li><a href="{{ lroute('downloads') }}" class="s-flink">{{ __('shell.downloads') }}</a></li>
                                <li><a href="{{ route('admin.dashboard') }}" class="s-flink">{{ __('shell.admin_panel') }}</a></li>
                                @foreach(\App\Support\Locales::SUPPORTED as $langOption)
                                    @if($langOption !== app()->getLocale())
                                        <li><a href="{{ \App\Support\Locales::switchUrl($langOption) }}"
                                               hreflang="{{ str_replace('_', '-', $langOption) }}"
                                               class="s-flink">{{ \App\Support\Locales::nativeName($langOption) }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <!-- Contact -->
                        <div class="col-6 col-lg-3">
                            <h4>{{ __('shell.contact') }}</h4>
                            <ul class="list-unstyled mb-0" style="display: flex; flex-direction: column; gap: 10px;">
                                <li><a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-flink">GitHub</a></li>
                                <li><a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="s-flink">Instagram</a></li>
                                <li><a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="s-flink">LinkedIn</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

            <div id="copyrights" class="dark">
                <div class="container">
                    <div class="row align-items-center justify-content-between py-3">
                        <div class="col-md-6 s-meta" style="font-size: 0.8rem;">
                            {{ __('shell.copyright', ['year' => date('Y')]) }}
                            @if(!empty($appVersion))
                                <span style="color: var(--s-faint); margin-left: .45rem; font-family: 'JetBrains Mono', monospace; font-size: .74rem;">v{{ $appVersion }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end mt-2 mt-md-0 align-items-center gap-3 s-meta" style="font-size: 0.8rem;">
                            <span style="color: var(--s-faint);">{{ __('shell.made_with') }}</span>
                            <span style="color: var(--s-faint);">·</span>
                            <a href="{{ lroute('home') }}" class="s-flink">{{ __('shell.home') }}</a>
                            <span style="color: var(--s-faint);">·</span>
                            <a href="{{ lroute('downloads') }}" class="s-flink">{{ __('shell.downloads') }}</a>
                        </div>
                    </div>
                </div>
            </div>

        </footer>

    </div><!-- #wrapper end -->

    <script src="{{ asset('vendor/canvas/js/plugins.min.js') }}"></script>
    <script src="{{ asset('vendor/canvas/js/functions.bundle.js') }}"></script>
    <script defer src="{{ asset('js/site.js') }}"></script>

    @stack('scripts')

</body>
</html>
