# AGENTS.md

## What this is

Mailbook (`xammie/mailbook`) is a **Laravel package** — not an app — that provides a web UI to preview Laravel mailables and notifications without triggering them. Users register mails in `routes/mailbook.php` via the `Mailbook` facade and browse them at `/mailbook`.

- PHP namespace: `Xammie\Mailbook\` → `src/`, `Xammie\Mailbook\Tests\` → `tests/` (PSR-4).
- Supports **PHP ^8.0** and **Laravel 9–13** (`illuminate/contracts`). Package tooling: `spatie/laravel-package-tools`.
- CI matrix runs PHPUnit on PHP 8.2–8.5 × Laravel 12/13, `prefer-lowest` and `prefer-stable`, Linux + Windows. **Never use language/library features newer than PHP 8.0 or that assume Laravel ≥12** in `src/`.

## Commands

| Task | Command |
|---|---|
| Install deps | `composer install` + `yarn install` |
| Run tests | `composer test` (PHPUnit via Orchestra Testbench) |
| Static analysis | `composer analyse` (PHPStan level 9) |
| Format PHP | `composer format` (Rector, then `pint --blade`) |
| Mutation tests | `composer mutate` (Infection, min-covered-msi 100) |
| Coverage | `composer coverage` |
| Build frontend | `yarn build` (CSS via Tailwind CLI, JS via Rollup+SWC+terser) |
| Watch frontend | `yarn watch:css` / `yarn watch:js` |
| Typecheck JS | `yarn lint` (tsc --noEmit) |

## Frontend assets are committed

`resources/dist/mailbook.css` and `resources/dist/mailbook.js` are **checked into git** and inlined into the dashboard view (read by `DashboardController` with `File::get`). After editing anything in `resources/css`, `resources/js`, or classes used in Blade views, run `yarn build` and commit the regenerated dist files — CI fails if they're stale. Frontend is vanilla TypeScript (no framework), Tailwind v4 (`@import 'tailwindcss'` + `tailwind.config.js` scanning `resources/views` and `resources/js`).

## Architecture (request flow)

1. **Registration**: user's `routes/mailbook.php` calls `Mailbook::add()` / `->to()` / `->category()` / `->comment()` / `->label()` / `->group()`. `Mailbook` (src/Mailbook.php) lazily includes that route file (`collect()`) and holds a `MailCollection` of `MailableItem`s. `MailRegistrar` applies chained context (notifiable, label, category, comment) to items added within a `group()`.
2. **Rendering**: `MailableItem` → `MailableResolver` (instantiates mailable from class-string/closure with container injection, resolves variants) → `MailableSender::collect()` temporarily overrides config (`mail.default`/`mail.driver` = `mailbook`, `queue.default` = `sync`) via `ConfigInjector`, sends the mail, and `MailbookTransport` (custom Symfony transport registered in the service provider's `packageBooted`) captures the resulting `Symfony\Component\Mime\Email` into the `Mailbook` facade, which returns it as a `ResolvedMail` (subject, recipients, HTML body, attachments).
3. **HTTP**: `routes/routes.php` registers `GET /`, `/content`, `/send` under `config('mailbook.route_prefix')`, guarded by `config('mailbook.enabled')` and the `RollbackDatabase` middleware (optional DB transaction rollback, config `database_rollback`). Controllers: `DashboardController` (full UI), `MailContentController` (iframe preview), `MailSendController` (real send with default driver, only when `send` config enabled).

Key details when touching this flow:

- **Faker consistency**: the dashboard embeds a `?s=<seed>` param (`FakeSeedGenerator`) so `fake()` output matches between the dashboard render and the `/content` iframe, which re-seeds before rendering.
- **Locales**: `config('mailbook.locales')` drives the language dropdown; `Mailbook::setLocale()` + `$mailable->locale()` are applied in the sender.
- The address `remove@mailbook.dev` is a sentinel filtered out of `ResolvedMail::to()`.
- `mailbook:install` command (`src/Commands/InstallMailbookCommand.php`) publishes stubs from `stubs/`: a route file, a sample mailable (envelope-based only if `Illuminate\Mail\Mailables\Envelope` exists — supports old Laravel), and a sample view.

## Layout

- `src/` — package code. `Data/` = domain objects (`MailableItem`, `MailableVariant`, `MailableGroup`, `MailCollection`, `ResolvedMail`); `Http/` = controllers/request/middleware; `Support/` = helpers; `Traits/` = `HasLabel`/`HasCategory`/`HasComment`.
- `config/mailbook.php` — published config (enabled, route_prefix, locales, send, database_rollback, middlewares, ...).
- `resources/views/` — Blade UI (`mailbook::` namespace); `resources/css|js` = source, `resources/dist` = built.
- `tests/` — mirrors `src/` structure; `tests/Fixtures/Mails/` has sample mailables/notifications used as test subjects; `tests/lang/nl.json` for localization tests.

## Conventions

- Every PHP file starts with `declare(strict_types=1);` (Pint enforces this and `void_return`). Full type hints everywhere (PHPStan level 9 on `src/` + `config/`).
- Internal classes are marked `@internal`; keep the public API (facade methods, `MailableItem` methods like `variant()`/`comment()`) backward compatible.
- Small focused classes, early returns, no side effects where avoidable — Rector runs codeQuality + earlyReturn sets, and `PreferTestsWithSnakeCaseRector`/`PreferPHPUnitSelfCallRector` apply to tests.
- Tests: PHPUnit 11, method names `test_snake_case`, use `self::` assertions, extend `Xammie\Mailbook\Tests\TestCase` (Testbench; enables `mailbook.enabled`, uses `testing` sqlite DB, deletes installed stub files in setup/teardown). Expectation objects live in `tests/Fixtures/` and `tests/Support/`.
- **Mutation coverage is enforced**: Infection requires 100% covered MSI on `src/` (excludes `Commands/` and `Support/`). New logic in `src/` needs tests that actually kill mutants; use `// @infection-ignore-all` sparingly.
- Blade views are formatted by `pint --blade` / prettier (plugins: blade, tailwindcss).
- Follow the existing style of not using `final` on public API classes; run `composer format` + `composer analyse` + `composer test` before considering a change done.

## Useful references

- `README.md` documents the end-user API (registering mails, variants, groups, locales, sending, config) — keep it in sync when changing public behavior.
- `CHANGELOG.md`, `infection.json5` (mutator ignores list), `.github/workflows/` (tests, linting, codestyle) define what CI enforces.
