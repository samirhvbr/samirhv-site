# samirhv — Laravel application

This directory is the Laravel application. The repository's documentation lives
one level up:

- [`../README.md`](../README.md) — what the project is, the bilingual URL
  scheme, the structure, how to run the tests
- [`../CHANGELOG.md`](../CHANGELOG.md) — one entry per release
- [`../CLAUDE.md`](../CLAUDE.md) — the guide for AI agents working here
- [`docs/`](docs/) — the AI-MEMORY module and the GitHub View token setup

Quick start, from this directory:

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve
php artisan test
```

There is no frontend build step. The theme is Canvas, served statically from
`public/vendor/canvas/`; Vite and Tailwind were removed in 0.7.0 because
nothing used them.
