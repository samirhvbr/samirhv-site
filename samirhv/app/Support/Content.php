<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;

/**
 * Translations for the prose that lives in the DATABASE.
 *
 * `projects.title`, `.description`, `.category` and `project_files.label` are
 * written in the admin, in Portuguese. They are content, not interface, so they
 * are not in the `lang/<locale>/*.php` screens — they are here, keyed by the
 * project slug, in `lang/<locale>/content.php`.
 *
 * WHY A FILE AND NOT A COLUMN. A `title_en` column would be the tidier model
 * and would survive a project created in the admin. It would also need a field
 * in the admin CRUD to be fillable, and the admin is explicitly out of scope.
 * So the English copy is versioned in the repository instead: four projects,
 * short strings, reviewable in a diff.
 *
 * THE COST, WRITTEN DOWN. Rename a project in the admin and its English title
 * does not follow — it keeps the old translation until someone edits this file.
 * The fallback makes that safe rather than broken: a slug with no translation
 * renders the database value, so a new project appears in Portuguese on the
 * English page instead of appearing blank.
 */
final class Content
{
    /**
     * @param  object  $project  anything with ->slug and the requested field
     */
    public static function project(object $project, string $field): string
    {
        $slug = (string) ($project->slug ?? '');
        $fallback = (string) ($project->{$field} ?? '');

        return self::lookup("content.projects.{$slug}.{$field}", $fallback);
    }

    /** Files are keyed by project slug + the label itself: labels repeat across projects. */
    public static function fileLabel(object $file): string
    {
        $fallback = (string) ($file->label ?? '');

        if ($fallback === '') {
            return '';
        }

        return self::lookup('content.file_labels.'.self::key($fallback), $fallback);
    }

    public static function category(?string $category): string
    {
        $category = (string) $category;

        if ($category === '') {
            return '';
        }

        return self::lookup('content.categories.'.self::key($category), $category);
    }

    /**
     * `Lang::has()` and not `__()`: a missing key makes `__()` return the key
     * itself, which would put "content.projects.foo.title" on the page. Here a
     * miss has to mean "use what the database says".
     */
    private static function lookup(string $key, string $fallback): string
    {
        return Lang::has($key) ? (string) __($key) : $fallback;
    }

    /** A stable array key from free text: lowercase, no accents, underscores. */
    private static function key(string $text): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $ascii = $ascii === false ? $text : $ascii;

        return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($ascii)), '_');
    }
}
