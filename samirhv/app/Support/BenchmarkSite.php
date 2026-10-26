<?php

namespace App\Support;

/**
 * Where the AI Benchmark lives: shvia.org.
 *
 * This site stopped rendering the benchmark in 1.1.0. Every address it ever
 * had for it answers a permanent redirect to the Benchmark page of shvia.org,
 * in the language of the address, and the header and footer link there.
 */
final class BenchmarkSite
{
    public const HOST = 'https://shvia.org';

    /** shvia.org writes Portuguese as `pt-br`, English as `en`. */
    public static function folder(?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()) === 'pt_BR' ? 'pt-br' : 'en';
    }

    /** The Benchmark page of shvia.org in one language. */
    public static function page(?string $locale = null): string
    {
        return self::HOST.'/'.self::folder($locale).'/ai-benchmark.html';
    }

    /** The two CSV files the old pages offered for download, now served by shvia.org. */
    public static function data(string $file): string
    {
        return self::HOST.'/data/ai-benchmark/'.$file;
    }
}
