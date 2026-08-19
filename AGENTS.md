# AGENTS.md

Coffee shop web app (Laravel 12). UI language is Indonesian (Bahasa Indonesia default per PRD). All 9 phases implemented (data layer, auth/RBAC, seeders, admin, POS, customer, kitchen display, reports, tests).

## Read these first
- `.agents/prd.md` — the authoritative product spec (Indonesian), with ✅/🔜 implementation status per feature.
- `.agents/task-instruction.md` — tech spec. Prescribes the real architecture and conventions.
- `.agents/task-list.md` — execution status per phase.
- `.agents/design/roasted_refined/DESIGN.md` — the design system source (color tokens, Playfair Display headings + Inter body, 12px radii, soft shadows). Mockup HTML/PNG previews were removed to keep the repo lean.

## Architecture conventions (from tech spec)
- **Form Request** for validation, never inline in controllers.
- **Policy** for authorization per resource.
- **Service classes** for complex business logic (stock, loyalty points, payment).
- **Event + Listener** for side effects (e.g. order completed → notify + add points).
- **Module layout**: `app/Http/Controllers/{Customer,Pos,Admin,Kitchen}/`, `app/Services/`, `app/Events/`, `app/Listeners/`, `app/Policies/`.
- RBAC via Spatie `laravel-permission`; roles: super-admin, admin, kasir, barista, customer.
- **Livewire 4 single-file components** live in `resources/views/components/{pos,customer,kitchen}/` (named e.g. `pos.pos-interface`) — no `app/Livewire/` classes. Test them with `Livewire::actingAs($user); Livewire::test('pos.pos-interface')`. Livewire computed properties must be accessed as `$this->prop` in Blade, and action methods returning redirects must not be typed `void`.

## Commands
- `composer dev` — runs `artisan serve` + `queue:listen` + `pail` + Vite concurrently. Use for day-to-day dev.
- `composer test` — clears config then runs `artisan test`. Single test: `php artisan test --filter=Name`.
- `composer setup` — fresh bootstrap: composer install, copy `.env`, key:generate, migrate, npm install/build.
- Lint/format: `vendor/bin/pint` (default preset; no pint.json in repo).
- Frontend assets: `npm run build` / `npm run dev`.

## Environment gotchas
- Local DB is **MySQL 8** (Laragon `127.0.0.1:3306`, root/no password) — databases `coffeeshop` (dev) and `coffeeshop_test` (tests, set in `phpunit.xml`). SQLite was the initial local setup but has been replaced.
- Tests run against MySQL `coffeeshop_test` via `RefreshDatabase` (phpunit.xml).
- No `tailwind.config.js` — Tailwind 4 configured via `@theme` in `resources/css/app.css`; Vite entrypoints are `resources/css/app.css` and `resources/js/app.js`. If `breeze:install` ever re-adds Tailwind 3 files, remove `tailwind.config.js`/`postcss.config.js` and restore the `@tailwindcss/vite` plugin.
- CLI `php.ini` (Laragon `php-8.3.33`) has `pdo_sqlite`/`sqlite3` commented out — `artisan test` fails with "could not find driver" until enabled (only relevant if switching back to SQLite).
- `npm` via PowerShell fails (unsigned `npm.ps1`); use `npm.cmd` instead.

## Testing
Write at minimum feature tests for checkout, payment, and stock-deduction flows (per tech spec).
Covered by `tests/Feature/{PosSmokeTest,PosOrdersTest,CustomerFlowTest,CustomerOrderStatusTest,FullFlowTest,KitchenDisplayTest,AdminSmokeTest,AdminProductImageTest,AdminBannerTest,RegistrationRbacTest,ProfileTest}.php`.