<?php

namespace Tests\Unit;

use App\Support\SemVer;
use Tests\TestCase;

/**
 * Version comparison, which decides whether the monitor says a fork is behind
 * upstream and which build the download page calls the latest.
 *
 * Untested until now, and it is the class where being subtly wrong is worst:
 * a wrong answer here is a plausible-looking version number, not an error.
 */
class SemVerTest extends TestCase
{
    public function test_it_extracts_the_numeric_parts(): void
    {
        $this->assertSame([0, 12, 0], SemVer::parts('v0.12.0'));
        $this->assertSame([1, 2], SemVer::parts('1.2'));
        $this->assertSame([3, 6, 3], SemVer::parts('release-3.6.3'));
        $this->assertSame([2, 0, 10], SemVer::parts('2.0.10-rc1'));
        $this->assertSame([1, 4, 15], SemVer::parts('1.4.15+build.7'));
    }

    public function test_garbage_has_no_parts(): void
    {
        foreach ([null, '', 'latest', 'v', 'nightly'] as $rubbish) {
            $this->assertSame([], SemVer::parts($rubbish), var_export($rubbish, true));
            $this->assertFalse(SemVer::isParsable($rubbish));
        }
    }

    /**
     * The reason this class exists instead of version_compare or strcmp:
     * lexically "0.9" beats "0.10", and numerically it does not.
     */
    public function test_ten_is_greater_than_nine(): void
    {
        $this->assertGreaterThan(0, SemVer::compare('0.10.0', '0.9.0'));
        $this->assertGreaterThan(0, SemVer::compare('1.0.10', '1.0.9'));
        $this->assertGreaterThan(0, SemVer::compare('2.110.183', '2.110.99'));
    }

    public function test_the_v_prefix_is_not_part_of_the_number(): void
    {
        $this->assertSame(0, SemVer::compare('v1.2.3', '1.2.3'));
        $this->assertGreaterThan(0, SemVer::compare('v2.0.0', '1.9.9'));
    }

    /** "1.2" and "1.2.0" are the same version written two ways. */
    public function test_a_missing_part_counts_as_zero(): void
    {
        $this->assertSame(0, SemVer::compare('1.2', '1.2.0'));
        $this->assertSame(0, SemVer::compare('1', '1.0.0'));
        $this->assertLessThan(0, SemVer::compare('1.2', '1.2.1'));
    }

    /** A pre-release suffix is ignored, which is a documented simplification. */
    public function test_a_suffix_does_not_change_the_comparison(): void
    {
        $this->assertSame(0, SemVer::compare('1.0.0-rc1', '1.0.0'));
        $this->assertGreaterThan(0, SemVer::compare('1.0.1-beta', '1.0.0'));
    }

    public function test_comparison_is_symmetric(): void
    {
        foreach ([['1.0.0', '2.0.0'], ['0.9.9', '0.10.0'], ['3.6.3', '3.6.3']] as [$a, $b]) {
            $this->assertSame(
                -1 * (SemVer::compare($a, $b) <=> 0),
                SemVer::compare($b, $a) <=> 0,
                "$a vs $b",
            );
        }
    }

    public function test_max_picks_the_highest_and_ignores_the_unparsable(): void
    {
        $this->assertSame('v0.16.0', SemVer::max(['v0.9.0', 'v0.16.0', 'v0.10.0']));
        $this->assertSame('1.2.0', SemVer::max(['latest', '1.2.0', 'nightly', null]));
        $this->assertNull(SemVer::max(['latest', 'nightly']));
        $this->assertNull(SemVer::max([]));
    }

    /** max() returns the string it was given, not a normalised rebuild of it. */
    public function test_max_returns_the_original_spelling(): void
    {
        $this->assertSame('v2.0.10', SemVer::max(['2.0.9', 'v2.0.10']));
    }
}
