<?php

namespace Tests\Unit;

use App\Support\FilenameInspector;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * What a build's filename says about it.
 *
 * This pre-fills the OS, architecture, type and version on every upload, and
 * the admin can override any of it — so a wrong guess is a wrong badge on a
 * public download row that nobody thought to check.
 *
 * The filenames below are real ones, from the projects in the catalogue.
 */
class FilenameInspectorTest extends TestCase
{
    public static function realArtifacts(): array
    {
        return [
            'fork deb' => ['GitHub-Desktop_3.6.3_fork-0.4.0_amd64.deb', 'linux', 'x64', 'deb', '3.6.3'],
            'fork AppImage' => ['GitHub-Desktop_3.6.3_fork-0.4.0_x86_64.AppImage', 'linux', 'x64', 'appimage', '3.6.3'],
            'rpm with a release suffix' => ['ShvIA-1.4.8-1.x86_64.rpm', 'linux', 'x64', 'rpm', '1.4.8'],
            'shvia deb' => ['ShvIA_1.4.8_amd64.deb', 'linux', 'x64', 'deb', '1.4.8'],
            'windows msi' => ['SShvTerm_1.2.80_x64_en-US.msi', 'windows', 'x64', 'msi', '1.2.80'],
            'macos dmg arm' => ['ShvIA_1.4.8_aarch64.dmg', 'macos', 'arm64', 'dmg', '1.4.8'],
            'linux tarball arm' => ['ai-usagebar-linux-aarch64.tar.gz', 'linux', 'arm64', 'gz', null],
            'universal pkg' => ['SShvTerm-2.0.5-universal.pkg', 'macos', 'universal', 'pkg', '2.0.5'],
        ];
    }

    #[DataProvider('realArtifacts')]
    public function test_it_reads_a_real_artifact_name(string $name, ?string $os, ?string $arch, ?string $type, ?string $version): void
    {
        $out = FilenameInspector::inspect($name);

        $this->assertSame($os, $out['os'], 'os');
        $this->assertSame($arch, $out['arch'], 'arch');
        $this->assertSame($type, $out['file_type'], 'file_type');
        $this->assertSame($version, $out['version'], 'version');
    }

    /**
     * The version regex requires a dot precisely so `x86_64` is not read as a
     * version. That is the trap this heuristic was written around.
     */
    public function test_an_architecture_is_not_mistaken_for_a_version(): void
    {
        $this->assertNull(FilenameInspector::inspect('app-x86_64.deb')['version']);
        $this->assertNull(FilenameInspector::inspect('app-aarch64.AppImage')['version']);
        $this->assertSame('x64', FilenameInspector::inspect('app-x86_64.deb')['arch']);
    }

    public function test_the_product_name_is_what_comes_before_the_version(): void
    {
        $this->assertSame('GitHub Desktop', FilenameInspector::inspect('GitHub-Desktop_3.6.3_amd64.deb')['name']);
        $this->assertSame('ShvIA', FilenameInspector::inspect('ShvIA_1.4.8_amd64.deb')['name']);
    }

    /** Nothing inferable is null, never a wrong guess dressed as a fact. */
    public function test_it_returns_null_rather_than_guessing(): void
    {
        $out = FilenameInspector::inspect('build');

        $this->assertNull($out['os']);
        $this->assertNull($out['arch']);
        $this->assertNull($out['file_type']);
        $this->assertNull($out['version']);
    }

    /** Case must not decide: .DEB and .deb are the same package. */
    public function test_it_ignores_case(): void
    {
        $lower = FilenameInspector::inspect('app_1.0.0_amd64.deb');
        $upper = FilenameInspector::inspect('APP_1.0.0_AMD64.DEB');

        $this->assertSame($lower['os'], $upper['os']);
        $this->assertSame($lower['arch'], $upper['arch']);
        $this->assertSame($lower['file_type'], $upper['file_type']);
    }

    /** Whatever it returns has to be a value the columns accept. */
    public function test_every_inference_is_a_value_the_schema_allows(): void
    {
        foreach (array_column(self::realArtifacts(), 0) as $name) {
            $out = FilenameInspector::inspect($name);

            $this->assertContains($out['os'], ['linux', 'windows', 'macos', null], $name);
            $this->assertContains($out['arch'], ['x64', 'arm64', 'universal', null], $name);
        }
    }
}
