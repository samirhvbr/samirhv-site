<?php

namespace Tests\Unit\AiMemory;

use App\Services\AiMemory\AiMemoryTime;
use Tests\TestCase;

/**
 * app/Services/AiMemory/*.php is a byte-identical copy of the same directory in
 * samirhvbr/ai-memory-web (ADR-006 there, docs/AI-MEMORY.md §6.1 here). This
 * suite fails when the copy stops being one, or when a sync brought something
 * this app does not provide yet. The other half, "ai-memory-web moved and the
 * copy did not follow", needs the network, and is the CI job in
 * .github/workflows/ai-memory-reader.yml.
 */
class ReaderCopyTest extends TestCase
{
    /** 2026-09-05 12:34:56 UTC, in microseconds. */
    private const MICROS = 1_788_611_696_000_000;

    public function test_the_copy_is_byte_identical_to_the_commit_it_pins(): void
    {
        $manifest = $this->manifest();
        $here = array_map('basename', glob($this->dir().'/*.php'));
        sort($here);
        $pinned = array_keys($manifest['files']);
        sort($pinned);

        $this->assertSame($pinned, $here, 'The set of classes differs from UPSTREAM.json. '.$this->howToFix($manifest));

        foreach ($manifest['files'] as $file => $sha256) {
            $this->assertSame($sha256, hash_file('sha256', $this->dir().'/'.$file),
                "{$file} was edited here. ".$this->howToFix($manifest));
        }
    }

    public function test_every_string_the_copy_produces_has_a_portuguese_translation(): void
    {
        $strings = [];
        foreach ($this->sources() as $source) {
            array_push($strings, ...$this->literalsPassedTo($source, ['__', 'say']));
        }
        // Control: an extractor that finds nothing would pass this test forever.
        $this->assertContains('still open', $strings, 'the extractor no longer finds the strings it must check');

        $pt = json_decode((string) file_get_contents(lang_path('pt_BR.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach (array_unique($strings) as $english) {
            $this->assertArrayHasKey($english, $pt, "lang/pt_BR.json has no translation for \"{$english}\". "
                .'Without one, the admin shows this line in English. Add it, with the same :placeholders.');
        }
    }

    public function test_every_config_key_the_copy_reads_is_declared_here(): void
    {
        $declared = array_keys(require config_path('aimemory.php'));

        foreach ($this->sources() as $file => $source) {
            preg_match_all("/config\\(\\s*'aimemory\\.([^']+)'/", $source, $m);
            foreach ($m[1] as $key) {
                $this->assertContains($key, $declared, "{$file} reads config('aimemory.{$key}'), which "
                    .'config/aimemory.php does not declare. Add it with this app\'s value.');
            }
        }
    }

    public function test_every_class_the_copy_imports_exists_here(): void
    {
        foreach ($this->sources() as $file => $source) {
            preg_match_all('/^use\s+([^;\s]+)\s*;/m', $source, $m);
            foreach ($m[1] as $class) {
                $this->assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class),
                    "{$file} imports {$class}, which this app does not have (ai-memory-web's ADR-006 host contract).");
            }
        }
    }

    public function test_under_this_apps_config_the_copy_speaks_portuguese_and_d_m_y(): void
    {
        // Admin routes render in the bare (English) locale, so the module's language
        // cannot come from the request. With nothing overridden here, this is what
        // an operator sees.
        app()->setLocale('en');

        $this->assertSame('05/09/2026 09:34', AiMemoryTime::format(self::MICROS));
        $this->assertSame('em aberto', AiMemoryTime::duration(self::MICROS, null));
    }

    private function dir(): string
    {
        return app_path('Services/AiMemory');
    }

    /** @return array{commit: string, version: string, files: array<string, string>} */
    private function manifest(): array
    {
        return json_decode((string) file_get_contents($this->dir().'/UPSTREAM.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    private function howToFix(array $manifest): string
    {
        return "This directory is a copy of ai-memory-web {$manifest['version']} ({$manifest['commit']}). "
            .'Change the class there, then run tools/sync-ai-memory-reader.sh (docs/AI-MEMORY.md §6.1).';
    }

    /** @return array<string, string> file name => source */
    private function sources(): array
    {
        $out = [];
        foreach (glob($this->dir().'/*.php') as $path) {
            $out[basename($path)] = (string) file_get_contents($path);
        }
        $this->assertNotEmpty($out, 'no reader class found: every check here would pass on nothing');

        return $out;
    }

    /**
     * The first argument of each call to one of $functions, when it is a string
     * literal or a chain of them joined by `.` — the shape the copy uses for
     * its UI text. A call whose first argument is a variable is skipped.
     *
     * @return list<string>
     */
    private function literalsPassedTo(string $source, array $functions): array
    {
        $tokens = array_values(array_filter(token_get_all($source),
            fn ($t) => ! is_array($t) || ! in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)));
        $out = [];

        foreach ($tokens as $i => $t) {
            if (! is_array($t) || $t[0] !== T_STRING || ! in_array($t[1], $functions, true)
                || ($tokens[$i + 1] ?? null) !== '(') {
                continue;
            }

            $parts = [];
            for ($j = $i + 2; is_array($tokens[$j] ?? null) && $tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING; $j += 2) {
                $parts[] = $this->unquote($tokens[$j][1]);
                if (($tokens[$j + 1] ?? null) !== '.') {
                    break;
                }
            }
            if ($parts !== []) {
                $out[] = implode('', $parts);
            }
        }

        return $out;
    }

    private function unquote(string $literal): string
    {
        $body = substr($literal, 1, -1);

        return $literal[0] === "'"
            ? strtr($body, ['\\\\' => '\\', "\\'" => "'"])
            : stripcslashes($body);
    }
}
