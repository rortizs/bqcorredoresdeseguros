# Proposal: Laravel 12 Migration for QBO Corredores de Seguros

## Intent

The QBO landing page was **compromised via SQL injection** due to raw PHP with zero framework protections. While 8 vulnerabilities were patched manually (prepared statements, CSRF tokens, env-based credentials, security headers), these fixes are band-aids on a fundamentally insecure architecture. A vanilla PHP codebase requires the developer to manually implement EVERY security measure that a framework provides out of the box.

Migrating to Laravel 12 replaces manual security patches with battle-tested framework primitives: Eloquent ORM (eliminates SQL injection by design), built-in CSRF middleware, `.env` configuration, Blade auto-escaping (prevents XSS), rate limiting, encrypted cookies, and security headers middleware. This is not a "nice to have" -- it is the difference between hoping the next developer remembers to use prepared statements vs. making insecure code structurally impossible.

Secondary goals: maintainability (MVC structure vs. spaghetti PHP), testability (Pest/PHPUnit), and a foundation for future features (admin panel, API, email notifications).

## Scope

### In Scope

- **Laravel 12 project scaffold** with Clean MVC architecture (per laravel-expert skill)
- **Blade template** for the landing page -- pixel-identical output to current `index.php`
- **Eloquent model** for `clientes` table with migration
- **Form Request** (`StoreContactRequest`) for server-side validation with all current rules
- **Thin controller** (`ContactController`) delegating to a service layer
- **ContactService** for form submission logic (DB insert)
- **`.env` configuration** for database credentials (replaces `db.php` + `.htaccess` SetEnv)
- **CSRF protection** via Laravel middleware (replaces manual session token)
- **Security headers** via middleware (replaces `.htaccess` custom headers)
- **Static assets migration**: CSS, JS, fonts, images into `public/` directory
- **cPanel deployment configuration**: `.htaccess` for `public/` subdirectory deployment
- **Google Analytics** preserved in Blade layout
- **Bootstrap 4.5.3 + jQuery 3.5.1** kept as-is (CDN links preserved, upgrade deferred)
- **Database migration** for `clientes` table schema

### Out of Scope

- **Admin panel** (FilamentPHP) -- deferred to future change
- **Email notifications** on form submission -- deferred
- **Bootstrap 5 / jQuery removal** -- deferred (visual identity must stay identical)
- **API endpoints** -- not needed for current landing page
- **User authentication** -- no login system required
- **Queue system** -- overkill for a single form handler
- **CDN / asset bundling** (Vite) -- static assets served directly, no build step needed on cPanel
- **PHP version upgrade** on server -- must work with whatever cPanel provides (8.1+ assumed)
- **FTP port closure** -- server-level config, outside application scope

## Approach

### Architecture: Clean MVC (Simple CRUD)

Per the laravel-expert skill, this is a **Simple CRUD** project type, so we use **Clean MVC + Form Requests** -- no hexagonal architecture, no DDD. That would be over-engineering a landing page with one form.

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ContactController.php      # Thin: receive, delegate, respond
│   ├── Requests/
│   │   └── StoreContactRequest.php    # Validation + sanitization rules
│   └── Middleware/
│       └── SecurityHeaders.php        # X-Frame-Options, HSTS, etc.
├── Models/
│   └── Cliente.php                    # Eloquent model, $fillable, $casts
├── Services/
│   └── ContactService.php            # Business logic: create contact record
└── Providers/
    └── AppServiceProvider.php         # Service bindings

resources/views/
├── layouts/
│   └── app.blade.php                 # Base layout (head, scripts, analytics)
└── landing/
    └── index.blade.php               # Full landing page (sections 1-7 + footer)

public/
├── css/styles.css                    # Exact current CSS
├── js/
│   ├── header.js                     # Sticky header + validation
│   └── varios.js                     # Content toggle + modal
├── img/                              # All current images
└── font/                             # Galano Grotesque woff files

database/migrations/
└── xxxx_create_clientes_table.php    # Schema from current DB

routes/
└── web.php                           # GET / and POST /contacto
```

### Migration Strategy

1. **Scaffold** Laravel 12 project in a new directory structure
2. **Extract** HTML from `index.php` into Blade template (replace `<?php echo` with `{{ }}`)
3. **Move** static assets (`css/`, `js/`, `img/`, `font/`) into `public/`
4. **Create** Eloquent migration matching current `clientes` table schema
5. **Implement** `StoreContactRequest` with same validation rules as current `save_form.php`
6. **Implement** `ContactController` + `ContactService` (thin controller pattern)
7. **Configure** `.env` for database, app URL, environment
8. **Add** `SecurityHeaders` middleware replicating current `.htaccess` security headers
9. **Configure** cPanel-compatible `.htaccess` for subdirectory deployment
10. **Verify** pixel-identical output by visual comparison

### cPanel Deployment Strategy

Laravel on cPanel shared hosting requires a specific approach since `public_html` must point to Laravel's `public/` directory:

**Option A (Recommended): Subdirectory method**
- Laravel project lives in `~/laravel/` (above `public_html`)
- `public_html/` contains only a modified `index.php` and `.htaccess` that bootstraps from `~/laravel/`

**Option B: Symlink method**
- Laravel project in `~/qbo-laravel/`
- `public_html` symlinked to `~/qbo-laravel/public/`
- Requires SSH access or cPanel Terminal

Both methods are documented for the client. Option A is safer on restrictive shared hosting.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `index.php` | Replaced | Converted to Blade template at `resources/views/landing/index.blade.php` |
| `save_form.php` | Replaced | Logic moved to `ContactController` + `StoreContactRequest` + `ContactService` |
| `db.php` | Replaced | Replaced by Laravel's `config/database.php` + `.env` |
| `.htaccess` | Replaced | Security headers moved to middleware; deployment `.htaccess` for cPanel |
| `css/styles.css` | Moved | To `public/css/styles.css` (content unchanged) |
| `js/header.js` | Moved | To `public/js/header.js` (content unchanged) |
| `js/varios.js` | Moved | To `public/js/varios.js` (content unchanged) |
| `img/*` | Moved | To `public/img/` (all files unchanged) |
| `font/*` | Moved | To `public/font/` (all files unchanged) |
| `composer.json` | New | Laravel 12 dependencies |
| `.env` | New | Database credentials, app config |
| `database/migrations/` | New | `clientes` table migration |
| `app/Http/` | New | Controller, Request, Middleware |
| `app/Models/` | New | `Cliente` Eloquent model |
| `app/Services/` | New | `ContactService` |
| `routes/web.php` | New | `GET /` and `POST /contacto` |

## Security Improvements: All 8 Vulnerabilities Addressed

| # | Vulnerability | Severity | Current Fix | Laravel Solution |
|---|--------------|----------|-------------|-----------------|
| 1 | SQL Injection in `save_form.php` | CRITICAL | Prepared statements (mysqli) | **Eloquent ORM** -- parameterized queries by design, no raw SQL ever touches user input |
| 2 | Hardcoded DB credentials | HIGH | `getenv()` in `db.php` | **`.env` file** -- Laravel's native config system, `.env` excluded from git by default |
| 3 | Error disclosure to users | HIGH | `error_log()` + generic messages | **`APP_DEBUG=false`** in production, `config/logging.php` channels, Whoops only in dev |
| 4 | No CSRF protection | HIGH | Manual session token | **`VerifyCsrfToken` middleware** -- automatic on all POST/PUT/DELETE, `@csrf` Blade directive |
| 5 | Broken `.gitignore` | MEDIUM | Fixed manually | **Laravel's default `.gitignore`** -- comprehensive, includes `.env`, `vendor/`, `storage/`, `node_modules/` |
| 6 | FTP port open | HIGH | Server config (outside app) | **Out of scope** -- but Laravel does not require FTP; deployment via SSH/Git is recommended |
| 7 | No security headers | MEDIUM | `.htaccess` headers | **Custom `SecurityHeaders` middleware** -- X-Frame-Options, HSTS, X-Content-Type-Options, CSP, Permissions-Policy |
| 8 | Outdated dependencies | MEDIUM | Not yet addressed | **Composer dependency management** -- `composer audit` for vulnerability scanning, lockfile for reproducible installs |

### Additional Security Gains from Laravel

- **Blade auto-escaping**: All `{{ $var }}` output is HTML-escaped by default (prevents XSS)
- **Mass assignment protection**: `$fillable` whitelist on Eloquent models
- **Rate limiting**: Built-in throttle middleware for form submission endpoint
- **Encrypted cookies**: Session data encrypted by default
- **HTTPS enforcement**: `TrustProxies` middleware + `APP_URL=https://` for correct URL generation
- **Input sanitization**: `TrimStrings` and `ConvertEmptyStringsToNull` middleware
- **SQL injection immunity**: Eloquent and Query Builder use PDO prepared statements exclusively

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| cPanel PHP version too old (< 8.1) | Medium | Check cPanel PHP version selector BEFORE starting. Laravel 12 requires PHP 8.2+. If stuck on 8.1, use Laravel 11. |
| cPanel lacks required PHP extensions (e.g., `pdo_mysql`, `mbstring`, `openssl`) | Low | Most cPanel providers include these. Verify with `php -m` via cPanel Terminal. |
| `public_html` directory structure conflicts with Laravel's `public/` | High | Use the subdirectory deployment method (Option A). Well-documented pattern for shared hosting. |
| Visual regression after Blade conversion | Medium | Side-by-side screenshot comparison of every section. The HTML output must be byte-identical (minus whitespace). |
| Composer not available on cPanel | Low | Most modern cPanel installations include Composer. If not, run `composer install` locally and upload `vendor/`. |
| Storage/cache permissions on shared hosting | Medium | Ensure `storage/` and `bootstrap/cache/` are writable (775). Add to deployment checklist. |
| Client unfamiliar with Laravel maintenance | Medium | Provide deployment documentation and a `DEPLOYMENT.md` with step-by-step instructions for cPanel. |
| `.htaccess` rewrite conflicts with cPanel defaults | Medium | Test `.htaccess` thoroughly. cPanel often has its own rewrite rules that can conflict. |

## Rollback Plan

1. **Before migration**: Create a `legacy/vanilla-php` branch containing ALL current files (post-security-patches) as a complete, working snapshot.
2. **Deployment rollback**: If the Laravel deployment fails on cPanel, restore by pointing `public_html` back to the legacy files. The original `index.php`, `save_form.php`, `db.php`, and `.htaccess` are fully functional with the security patches already applied.
3. **Database**: The `clientes` table schema does NOT change. The Eloquent migration creates the same structure. No data migration needed -- both versions read/write the same table.
4. **DNS/Domain**: No DNS changes required. The rollback is purely a file-level swap on the server.

**Rollback time estimate**: < 5 minutes (copy legacy files back to `public_html`).

## Dependencies

- **PHP 8.2+** on the cPanel server (required for Laravel 12)
- **Composer** available on the server (or vendor uploaded manually)
- **MySQL 5.7+ / MariaDB 10.3+** (already in use, confirmed by current `db.php`)
- **SSH access or cPanel Terminal** for initial Laravel setup (artisan commands)
- **mod_rewrite enabled** in Apache (already confirmed by current `.htaccess` working)
- **Current `clientes` table schema** -- need to inspect actual DB columns to create accurate migration

## Success Criteria

- [ ] Landing page renders pixel-identical to current version (all 7 sections + footer + modal)
- [ ] Contact form submits successfully and inserts into `clientes` table via Eloquent
- [ ] CSRF token is automatically included and validated on form POST
- [ ] All security headers present in response (X-Frame-Options, HSTS, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
- [ ] `.env` file holds all credentials; no secrets in source code
- [ ] `APP_DEBUG=false` in production; errors logged to `storage/logs/`, never shown to users
- [ ] Google Analytics tracking code fires correctly
- [ ] Responsive layout works on mobile (414px), tablet (813px), desktop (1024px+) breakpoints
- [ ] Sticky header, Conoce/Mision/Vision tabs, and confirmation modal all function correctly
- [ ] Form validation (client-side Bootstrap + server-side FormRequest) rejects invalid input
- [ ] `composer audit` reports no known vulnerabilities in dependencies
- [ ] Deployment on cPanel shared hosting confirmed working
- [ ] Legacy files preserved in `legacy/vanilla-php` branch for rollback
- [ ] Galano Grotesque custom fonts load correctly from `public/font/`
