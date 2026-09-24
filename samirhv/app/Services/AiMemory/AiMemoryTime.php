<?php

namespace App\Services\AiMemory;

use Illuminate\Support\Carbon;

/**
 * Formatting of ai-memory timestamps for the views. ai-memory stores time as
 * INTEGER = microseconds since the epoch (UTC); these helpers convert to the
 * display timezone (config `aimemory.timezone`) and format it. Static on
 * purpose, so Blade can call it directly.
 *
 * The default format and the UI language come from the host app's config
 * (`aimemory.date_format`, `aimemory.locale`), never from this class: the class
 * is copied byte-for-byte into samirhv-site, which reads d/m/Y in Portuguese
 * (ADR-006). This app's own default is ISO-ish (Y-m-d) because its panel is
 * read by operators, and 03-04 must never be ambiguous between March and April.
 */
class AiMemoryTime
{
    public static function toCarbon(int|float|null $micros): ?Carbon
    {
        if (! $micros || $micros <= 0) {
            return null;
        }

        return Carbon::createFromTimestamp((int) ($micros / 1_000_000))
            ->timezone((string) config('aimemory.timezone', 'UTC'));
    }

    /** Formatted date/time, or "—" when empty. No format given = `aimemory.date_format`. */
    public static function format(int|float|null $micros, ?string $format = null): string
    {
        $format ??= (string) config('aimemory.date_format', 'Y-m-d H:i');

        return self::toCarbon($micros)?->format($format) ?? '—';
    }

    /** "3 days ago", or "—". */
    public static function human(int|float|null $micros): string
    {
        return self::toCarbon($micros)?->diffForHumans() ?? '—';
    }

    /** Duration between start and end (a session), tolerating a null end. */
    public static function duration(int|float|null $start, int|float|null $end): string
    {
        $a = self::toCarbon($start);
        if ($a === null) {
            return '—';
        }
        $b = self::toCarbon($end);
        if ($b === null) {
            return __('still open', [], config('aimemory.locale'));
        }

        $seconds = abs($b->getTimestamp() - $a->getTimestamp());
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);

        return $h > 0 ? "{$h}h {$m}min" : ($m > 0 ? "{$m}min" : "{$seconds}s");
    }
}
