## Goal
Complete a multi-tenant ERP-SaaS platform (NexaERP) with Laravel backend and Vue 3 SPA frontend, using Metronic Demo 42 for admin dashboard and glassmorphism for portfolio.

## Constraints & Preferences
- Backend: Laravel 11 + spatie/laravel-multitenancy + permissions + translation-loader + MySQL (landlord + per-tenant DBs).
- Frontend: pnpm monorepo with `apps/portfolio`, `apps/admin`, `apps/tenant` + `packages/api`, `packages/i18n`, `packages/ui`. Vue 3 + Vite + Pinia + Vue Router + Tailwind + vue-i18n.
- All backend logic is API-only SPA; no Blade views for dashboards.
- Arabic/English/French via dynamic DB translations + static fallback.
- Admin dashboard must use Metronic Demo 42 theme (Bootstrap 5 based).
- Portfolio must use glassmorphism (dark background #080C18 / #0F172A + transparent blur cards + Indigo #6366F1 / Cyan #06B6D4 / Amber #F59E0B).
- Design must be fully responsive across all devices and screen sizes.
- **Dynamic data everywhere** — Module checkboxes read from actual nwidart modules (filesystem), dashboard/portfolio data from real DB.

## Progress
### Done
- Backend nwidart/laravel-modules system fully installed: `Modules/Core/`, `Modules/Landlord/`.
- Super Admin Auth: 3 methods (password, email OTP, mobile OTP) via Sanctum with `super_admin` guard.
- Admin APIs: Stats overview (real DB counts + join-based revenue), Packages CRUD, Tenants CRUD, CMS (settings/hero/features/testimonials) CRUD, Languages CRUD, Translations CRUD+bulk, Subscriptions list/cancel/renew.
- Portfolio Public APIs: GET packages/settings(hero)/features/testimonials/modules; POST subscribe; POST contact.
- Tenant Auth & Dashboard APIs: login/logout, dashboard index, Core CRUD under `{tenant}.erp.test/api/v1/core/`.
- Dynamic Translation System: spatie/laravel-translation-loader + 64 keys × 3 languages seeded.
- Controller/Repository/Request refactor: Package, Tenant, Language, Translation, Subscription all with Interface+Repository pattern.
- Sequential login factor (1FA → 2FA).
- Frontend i18n dynamic loading.
- Admin Language & Translation pages (CRUD table + grid with bulk save).
- Profile Page: PUT profile + POST change-password.
- Metronic Demo 42 theme integrated (6144 files), MetronicShell.vue with full sidebar/header/toolbar/footer layout.
- Portfolio glassmorphism redesigned (main.css design tokens, 6 pages).
- Package features as dynamic checkboxes from filesystem modules (Core, Landlord).
- Live Preview in CMS editors (HeroPreview, FeaturesPreview, TestimonialsPreview).
- **ModuleController + PortfolioApiController::modules()** — reads actual nwidart modules from `Modules/*/module.json` via filesystem scan. New modules appear automatically.
- **StatsController::revenue()** — now uses `JOIN packages` for real revenue instead of hardcoded `$count * 79`.
- **PortfolioApiController::settings()** — now includes hero fields (`badge_en/ar`, `title_en/ar`, `title_highlight_en/ar`, `subtitle_en/ar`, `cta_primary_en/ar`, `cta_secondary_en/ar`) from CMS DB.
- **PricingPage.vue** — transforms API packages (features as `{key: bool}` → feature name array via modules lookup), computes `annual_price` (80%), marks middle plan as popular.
- **HomePage.vue** — features/testimonials from store API with hardcoded fallback.
- **DashboardPage.vue** — removed hardcoded "12%" badge, replaced with dynamic Active indicator.
- **StatsOverview interface** added + typed store.

### Pending
- Portfolio HomePage stats section is always hardcoded (needs a backend stats endpoint for public).
- `"type": "module"` needs to be added to all three apps' `package.json` to suppress PostCSS warning.
- Portfolio: pick locale from query param.
- Frontend Subscription store + SubscriptionsPage integration.

### Blocked
- (none)

## Key Decisions
- **Module scanning**: Both admin (`ModuleController`) and portfolio (`PortfolioApiController::modules()`) read from `Modules/*/module.json` via `File::directories()` — not from `modules_statuses.json` or a static config.
- **Hero data in settings**: Hero fields are merged into the portfolio `/settings` endpoint (instead of a separate `/hero` endpoint) so `usePortfolioStore` only needs one `fetchAll()` call.
- **PricingPage transformation**: API packages' `features: {key: bool}` is converted to `features_list: string[]` using module names fetched from `/modules`.
- **Revenue calculation**: `JOIN packages ON tenants.package_id = packages.id` with `SUM(packages.price)` for accurate monthly revenue.

## Next Steps
1. Add public stats endpoint for portfolio HomePage stats section.
2. Add `"type": "module"` to all three apps' `package.json`.
3. Portfolio: pick locale from query param.
4. Frontend Subscription store + integrate with SubscriptionsPage.

## Critical Context
- Database: MySQL localhost:3306. Landlord DB = `erp_saas_landlord`. Tenant DB = `erp_tenant_*`.
- Super Admin: `ahmednassag@gmail.com` / `password`.
- Tenant Admin (alpha): `admin@alpha.erp.test` / `password`.
- Backend dev: `php artisan serve --host=erp.test --port=8000`.
- Frontend dev: `pnpm dev:admin` (→3001), `pnpm dev:portfolio` (→5173), `pnpm dev:tenant` (default).
- Build status: Admin + Portfolio build successfully. Tenant has pre-existing TS errors.
- Modules on disk: `Modules/Core/`, `Modules/Landlord/` (filesystem scan).
- API response format: `{"status":"success","data":...}`.
