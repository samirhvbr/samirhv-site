<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Locales;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * `/sitemap.xml` — both languages of every public page.
 *
 * A route and not a generated file: the project pages come from the database
 * and are published or unpublished from the admin, so a file would go stale
 * between deploys and nothing in deploy.sh would notice. Cached for an hour,
 * because it is crawler-facing and must not be a query per hit.
 *
 * Every `<url>` carries the FULL alternate set — both languages, each entry
 * including itself, which is what Google requires — plus `x-default` pointing
 * at the bare English url, because `/` is the address that performs the
 * negotiation and that is exactly what `x-default` means.
 *
 * The alternates come from `Locales::alternatesFor()`, the same function that
 * builds the `hreflang` tags in the page's own head. That is the point of the
 * shared primitive: the sitemap cannot advertise a pair the page contradicts.
 */
class SitemapController extends Controller
{
    private const TTL = 3600;

    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', self::TTL, fn () => $this->build());

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function build(): string
    {
        $urls = [];

        foreach (['home' => '1.0', 'downloads' => '0.9', 'project.github-desktop' => '0.6'] as $name => $priority) {
            $urls[] = $this->entry($name, [], $priority);
        }

        /* A pure-link project's page 302s straight to an external site, so
           listing it would put a redirecting url in the sitemap — a Search
           Console warning, and a page with nothing of ours to index. */
        $projects = Project::published()
            ->orderBy('sort_order')
            ->get(['slug', 'external_url', 'redirect_to_site', 'updated_at']);

        foreach ($projects as $project) {
            if ($project->redirectsToSite()) {
                continue;
            }

            $urls[] = $this->entry(
                'project.show',
                ['project' => $project->slug],
                '0.8',
                $project->updated_at?->toAtomString(),
            );
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n"
            .implode('', $urls)
            .'</urlset>'."\n";
    }

    /** One `<url>` per language, each carrying every language's alternate. */
    private function entry(string $routeName, array $parameters, string $priority, ?string $lastmod = null): string
    {
        $alternates = Locales::alternatesFor($routeName, $parameters);

        if ($alternates === []) {
            return '';
        }

        $links = '';
        foreach ($alternates as $locale => $url) {
            $links .= '    <xhtml:link rel="alternate" hreflang="'.Locales::tag($locale).'" href="'.e($url).'"/>'."\n";
        }
        if (isset($alternates[Locales::BARE])) {
            $links .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.e($alternates[Locales::BARE]).'"/>'."\n";
        }

        $out = '';
        foreach ($alternates as $url) {
            $out .= '  <url>'."\n"
                .'    <loc>'.e($url).'</loc>'."\n"
                .($lastmod ? '    <lastmod>'.e($lastmod).'</lastmod>'."\n" : '')
                .$links
                .'    <priority>'.$priority.'</priority>'."\n"
                .'  </url>'."\n";
        }

        return $out;
    }
}
