<?php

namespace Tests\Unit;

use App\Support\OsDetector;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Which operating system the visitor is on, from the User-Agent.
 *
 * It picks the default tab and the recommended build on every project page, so
 * being wrong here is a visitor handed the installer for a system they are not
 * running.
 */
class OsDetectorTest extends TestCase
{
    public static function agents(): array
    {
        return [
            'Windows 11 Chrome' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131 Safari/537.36', 'windows', 'x64'],
            'macOS Intel Safari' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Version/17.0 Safari/605.1.15', 'macos', 'x64'],
            'Ubuntu Firefox' => ['Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:133.0) Gecko/20100101 Firefox/133.0', 'linux', 'x64'],
            'Linux arm64' => ['Mozilla/5.0 (X11; Linux aarch64) AppleWebKit/537.36 Chrome/131', 'linux', 'arm64'],
            'Android arm64' => ['Mozilla/5.0 (Linux; Android 14; arm64) AppleWebKit/537.36 Chrome/131 Mobile', 'linux', 'arm64'],
            'curl' => ['curl/8.5.0', 'linux', 'x64'],
        ];
    }

    #[DataProvider('agents')]
    public function test_it_reads_the_system_from_the_user_agent(string $ua, string $os, string $arch): void
    {
        $this->assertSame(['os' => $os, 'arch' => $arch], OsDetector::detect($ua));
    }

    /**
     * A bot, a proxy stripping headers, a browser sending nothing: the page has
     * to render regardless, so the answer is Linux and never an error.
     */
    public function test_an_absent_user_agent_still_produces_a_choice(): void
    {
        foreach ([null, ''] as $nothing) {
            $this->assertSame(['os' => 'linux', 'arch' => 'x64'], OsDetector::detect($nothing));
        }
    }

    /** Every detected OS must be one the tabs can actually render. */
    public function test_it_never_returns_an_os_the_page_cannot_show(): void
    {
        foreach (array_column(self::agents(), 0) as $ua) {
            $this->assertContains(OsDetector::detect($ua)['os'], OsDetector::OSES, $ua);
        }
    }

    /**
     * Safari on Apple Silicon reports "Intel Mac OS X". The class documents
     * that the architecture guess is weak and defaults to x64; this locks the
     * documented behaviour so nobody "fixes" it into a wrong arm64.
     */
    public function test_apple_silicon_safari_is_read_as_x64_by_design(): void
    {
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Version/17.4 Safari/605.1.15';

        $this->assertSame(['os' => 'macos', 'arch' => 'x64'], OsDetector::detect($ua));
    }

    public function test_every_os_has_a_label_and_the_unknown_one_has_a_fallback(): void
    {
        foreach (OsDetector::OSES as $os) {
            $this->assertNotSame('Outro', OsDetector::label($os), $os);
        }

        $this->assertSame('Outro', OsDetector::label('haiku'));
        $this->assertSame('Outro', OsDetector::label(null));
    }
}
