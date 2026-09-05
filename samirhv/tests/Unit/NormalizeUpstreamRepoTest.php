<?php

namespace Tests\Unit;

use App\Services\GithubReleaseChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * "owner/repo", whatever the admin actually pasted.
 *
 * The monitor compares our version against this repository, so a value that
 * normalises wrongly produces a 404 from GitHub that reads as "upstream has no
 * releases" rather than "the field is wrong". The seeder now fills this field
 * too, which makes the pure function behind it worth pinning down.
 */
class NormalizeUpstreamRepoTest extends TestCase
{
    public static function accepted(): array
    {
        return [
            'already normalised' => ['desktop/desktop', 'desktop/desktop'],
            'https url' => ['https://github.com/akitaonrails/ai-usagebar', 'akitaonrails/ai-usagebar'],
            'http url' => ['http://github.com/desktop/desktop', 'desktop/desktop'],
            'with www' => ['https://www.github.com/desktop/desktop', 'desktop/desktop'],
            'no scheme' => ['github.com/desktop/desktop', 'desktop/desktop'],
            'clone url' => ['https://github.com/samirhvbr/github-desktop.git', 'samirhvbr/github-desktop'],
            'deep link' => ['https://github.com/desktop/desktop/tree/development', 'desktop/desktop'],
            'trailing slash' => ['https://github.com/desktop/desktop/', 'desktop/desktop'],
            'surrounding space' => ['  desktop/desktop  ', 'desktop/desktop'],
            'dots in the name' => ['owner/repo.js', 'owner/repo.js'],
        ];
    }

    #[DataProvider('accepted')]
    public function test_it_reduces_what_a_person_pastes_to_owner_and_repo(string $input, string $expected): void
    {
        $this->assertSame($expected, GithubReleaseChecker::normalizeRepo($input));
    }

    /**
     * Null is what makes the admin form refuse the value instead of storing
     * something the monitor will silently fail on later.
     */
    public function test_it_refuses_what_is_not_a_repository(): void
    {
        foreach ([null, '', '   ', 'desktop', '/', 'https://github.com/', 'https://gitlab.com/'] as $rubbish) {
            $this->assertNull(GithubReleaseChecker::normalizeRepo($rubbish), var_export($rubbish, true));
        }
    }

    /** Normalising twice must not change the answer. */
    #[DataProvider('accepted')]
    public function test_it_is_idempotent(string $input, string $expected): void
    {
        $once = GithubReleaseChecker::normalizeRepo($input);

        $this->assertSame($once, GithubReleaseChecker::normalizeRepo($once));
    }

    /** The values the seeder writes have to survive their own normalisation. */
    public function test_the_seeded_repositories_are_already_normal(): void
    {
        foreach (['desktop/desktop', 'akitaonrails/ai-usagebar'] as $seeded) {
            $this->assertSame($seeded, GithubReleaseChecker::normalizeRepo($seeded));
        }
    }
}
