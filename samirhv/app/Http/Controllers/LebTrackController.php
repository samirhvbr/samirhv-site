<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * `/ai-benchmark/leb-default` and `/ai-benchmark/leb-security` — the two tracks of the next LEB series.
 *
 * No database: the series is a proposal (docs/new-series-design.md of samirhvbr/ai-benchmark, status PROPOSED), so both pages
 * are status pages, and the one whose instance has an aggregate in the synced results (the Default track since 15 October 2026)
 * also shows that first case, read through AiBenchmark::aggregate(). They are written once in lang/ — `leb_series` for what the two say alike,
 * `leb_default` and `leb_security` for what each track measures — and rendered by one view; shvia.org renders the same
 * copy. Nothing on them goes beyond what the public repository says, and no page of the series calls an answer key
 * private: the key of every instance is published on GitHub with the instance, before its first run.
 */
class LebTrackController extends Controller
{
    /** Each track: the instance of system A it runs on, and the other track (for the link between the two pages). */
    public const TRACKS = [
        'default' => ['instance' => 'LEB-D-300-A', 'other' => 'security'],
        'security' => ['instance' => 'LEB-S-300-A', 'other' => 'default'],
    ];

    public function show(string $track): View
    {
        abort_unless(isset(self::TRACKS[$track]), 404);

        return view('ai-benchmark.leb-track', ['track' => $track] + self::TRACKS[$track]);
    }
}
