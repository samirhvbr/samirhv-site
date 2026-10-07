{{-- Google tag (gtag.js), the Google Analytics 4 stream for samirhv.com.br. Renders only
     when services.google.tag_id is set, so an empty GOOGLE_TAG_ID turns it off (local
     copies of .env.example, the test suite). The id is a public measurement id, not a
     secret. Async: it does not block rendering. Public layout only; the admin layout and
     the login view do not include it. --}}
@php($googleTagId = config('services.google.tag_id'))
@if ($googleTagId)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleTagId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', {{ \Illuminate\Support\Js::from($googleTagId) }});
    </script>
@endif
