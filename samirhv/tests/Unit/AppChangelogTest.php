<?php

namespace Tests\Unit;

use App\Support\SemVer;
use Tests\TestCase;

/**
 * The per-application changelog, which is curated by hand in two languages.
 *
 * Hand-curated content in parallel files drifts: an entry added to one language
 * and forgotten in the other, a date typed wrong, a version out of order. None
 * of that shows up as an error on the page — it shows up as a Portuguese
 * reader seeing four releases where an English one sees five. These assertions
 * are the only thing that would notice.
 */
class AppChangelogTest extends TestCase
{
    private function changelog(string $locale): array
    {
        return require lang_path($locale.'/changelog.php');
    }

    public function test_both_languages_describe_the_same_releases(): void
    {
        $en = $this->changelog('en')['apps'];
        $pt = $this->changelog('pt_BR')['apps'];

        $this->assertSame(array_keys($en), array_keys($pt), 'The two files cover different applications.');

        foreach ($en as $slug => $releases) {
            $this->assertCount(count($releases), $pt[$slug], "[$slug] has a different number of releases.");

            foreach ($releases as $i => $release) {
                $this->assertSame($release['version'], $pt[$slug][$i]['version'], "[$slug][$i] version");
                $this->assertSame($release['date'], $pt[$slug][$i]['date'], "[$slug][$i] date");
                $this->assertCount(count($release['notes']), $pt[$slug][$i]['notes'], "[$slug][$i] notes");
            }
        }
    }

    public function test_every_release_is_shaped_like_a_release(): void
    {
        foreach (['en', 'pt_BR'] as $locale) {
            foreach ($this->changelog($locale)['apps'] as $slug => $releases) {
                $this->assertNotEmpty($releases, "[$locale/$slug] has no releases at all.");

                foreach ($releases as $i => $release) {
                    $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $release['version'], "[$locale/$slug][$i]");
                    $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $release['date'], "[$locale/$slug][$i]");
                    $this->assertNotEmpty($release['notes'], "[$locale/$slug][$i] has no notes.");

                    foreach ($release['notes'] as $note) {
                        $this->assertIsString($note);
                        $this->assertNotSame('', trim($note));
                    }
                }
            }
        }
    }

    /** The first entry is rendered as "current", so newest really must be first. */
    public function test_releases_are_listed_newest_first(): void
    {
        foreach ($this->changelog('en')['apps'] as $slug => $releases) {
            $versions = array_column($releases, 'version');
            $dates = array_column($releases, 'date');

            $sortedVersions = $versions;
            usort($sortedVersions, fn ($a, $b) => SemVer::compare($b, $a));
            $this->assertSame($sortedVersions, $versions, "[$slug] versions are not newest-first.");

            $sortedDates = $dates;
            rsort($sortedDates);
            $this->assertSame($sortedDates, $dates, "[$slug] dates are not newest-first.");
        }
    }

    /** A note keyed to an app with no releases would render against nothing. */
    public function test_every_side_note_belongs_to_a_listed_application(): void
    {
        foreach (['en', 'pt_BR'] as $locale) {
            $file = $this->changelog($locale);

            foreach (array_keys($file['notes']) as $slug) {
                $this->assertArrayHasKey($slug, $file['apps'], "[$locale] note for [$slug] has no releases.");
            }
        }
    }
}
