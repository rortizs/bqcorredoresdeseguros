# Design: Laravel 12 Migration (laravel-refactor)

## Technical Approach

Migrate the QBO Corredores de Seguros vanilla PHP landing page to a Laravel 12 project using **Clean MVC** architecture. This is a single-page landing with one form endpoint -- not complex enough for hexagonal/DDD, but the Service layer pattern is still used to keep the controller thin per Laravel best practices.

The migration strategy is **parallel build**: create the full Laravel project alongside the existing files, validate visual parity, then replace. The landing page visual output MUST remain pixel-identical. All original CSS, JS, images, and fonts are preserved as-is (no rewrite, no Tailwind conversion, no Vite bundling of legacy assets).

Security hardening is the primary driver: the original site was compromised via SQL injection (XANTIBOT). Laravel provides CSRF, Eloquent ORM (parameterized queries), `.env` credential isolation, and middleware-based security headers out of the box.

---

## Architecture Decisions

### Decision: Clean MVC with Service Layer

**Choice**: Thin Controller + Form Request + Service (no Actions, no Events, no Jobs, no Policies)
**Alternatives considered**:
- Fat controller (all logic in controller) -- rejected: violates SRP, harder to test
- Hexagonal / DDD -- rejected: massive overkill for a single landing page with one form
- Controller-only (no service) -- rejected: even though simple, the service pattern keeps validation (FormRequest) separated from persistence logic (Service), and is trivially testable

**Rationale**: The laravel-expert skill prescribes "Simple CRUD = Clean MVC + Form Requests". This landing page has exactly one write operation (contact form). A `ContactService` encapsulates the DB insert, keeping the controller at ~5 lines. If future features are added (email notifications, admin panel), the service is already in place.

### Decision: No Asset Compilation (No Vite)

**Choice**: Serve original CSS/JS/images/fonts as static files from `public/`
**Alternatives considered**:
- Vite + Blade directives (`@vite`) -- rejected: adds build step complexity for zero benefit on legacy assets
- Laravel Mix -- rejected: deprecated in Laravel 12
- Tailwind CSS rewrite -- rejected: would require pixel-perfect recreation of custom Galano Grotesque typography and all responsive breakpoints, risk of visual regression

**Rationale**: The CSS uses 3 custom `@font-face` declarations (Galano Grotesque Bold/Light/Medium), Bootstrap 4.5.3 via CDN, and 660+ lines of hand-written responsive CSS. Rewriting this in Tailwind or processing it through Vite gains nothing and risks breaking the visual output. Static files served from `public/` work identically to the current cPanel setup.

### Decision: Bootstrap 4.5.3 via CDN (Unchanged)

**Choice**: Keep Bootstrap 4.5.3 CDN links in the Blade template
**Alternatives considered**:
- Upgrade to Bootstrap 5 -- rejected: jQuery-dependent modal and form validation in the JS would break
- Install via npm/Vite -- rejected: adds build step for no benefit

**Rationale**: The existing JS (`header.js`, `varios.js`) relies on jQuery 3.5.1 slim + Bootstrap 4 bundle for the modal (`$('#myModal').modal('show')`) and form validation classes. Upgrading would require rewriting JS, which is out of scope for a security-focused migration.

### Decision: cPanel Deployment with Symlink Strategy

**Choice**: Laravel app above `public_html`, symlink `public_html` to Laravel's `public/`
**Alternatives considered**:
- Move everything into `public_html` with modified `index.php` paths -- rejected: exposes `.env`, `vendor/`, `storage/` to web
- Subdomain pointing to `public/` -- rejected: requires cPanel DNS changes, client URL disruption
- `.htaccess` rewrite in `public_html` to proxy to Laravel -- rejected: fragile, performance overhead

**Rationale**: cPanel allows changing the document root or creating symlinks. The standard Laravel deployment on shared hosting is:
```
/home/user/
├── qbo-app/              ← Laravel root (OUTSIDE public_html)
│   ├── app/
│   ├── config/
│   ├── .env
│   ├── vendor/
│   └── public/           ← This IS public_html (symlinked)
└── public_html → qbo-app/public/   ← Symlink
```
This keeps `.env`, `vendor/`, `storage/`, and all app code outside the web root. Apache serves only what is in `public/`.

### Decision: Single Blade Template (No Layout/Components)

**Choice**: One `landing.blade.php` file, no `@extends`, no `@section`, no Blade components
**Alternatives considered**:
- Layout + sections (`@extends('layouts.app')`, `@section('content')`) -- rejected: over-engineering for a single page
- Blade components (`<x-header>`, `<x-footer>`) -- rejected: same reason, no reuse case

**Rationale**: There is exactly ONE page. The HTML is ~340 lines. Splitting it into layout + sections + components adds indirection with zero reuse benefit. If the site grows to multiple pages in the future, THEN extract a layout. YAGNI.

### Decision: Rate Limiting on Form Submission

**Choice**: Laravel's built-in `throttle` middleware on the POST route (10 requests/minute per IP)
**Alternatives considered**:
- Honeypot field -- considered as complementary, not primary
- reCAPTCHA -- rejected: adds Google dependency, UX friction, requires API keys
- Custom rate limiter -- rejected: Laravel's built-in is battle-tested

**Rationale**: The original site had no rate limiting. An attacker could spam the contact form infinitely. Laravel's `throttle:10,1` middleware limits to 10 submissions per minute per IP, which is more than enough for legitimate use while blocking automated abuse.

### Decision: Custom SecurityHeaders Middleware

**Choice**: Custom middleware that adds X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, Content-Security-Policy
**Alternatives considered**:
- Apache `.htaccess` headers -- rejected: harder to maintain, not version-controlled with app
- Third-party package (spatie/laravel-csp) -- rejected: overkill for static CSP on a single page
- No custom headers (rely on Laravel defaults) -- rejected: Laravel does NOT add security headers by default

**Rationale**: The security audit identified missing security headers. A single middleware class (~30 lines) adds all necessary headers and is applied globally. This is more maintainable than `.htaccess` directives and travels with the codebase.

---

## Data Flow

### Sequence 1: Landing Page Load (GET /)

```
Browser                    Apache/.htaccess           Laravel Router
  │                              │                        │
  │  GET /                       │                        │
  │─────────────────────────────>│                        │
  │                              │  Rewrite to index.php  │
  │                              │───────────────────────>│
  │                              │                        │
  │                              │              ┌─────────┴──────────┐
  │                              │              │  Middleware Stack   │
  │                              │              │  1. SecurityHeaders │
  │                              │              │  2. VerifyCsrfToken │
  │                              │              │     (read, no check │
  │                              │              │      on GET)        │
  │                              │              └─────────┬──────────┘
  │                              │                        │
  │                              │              ┌─────────┴──────────┐
  │                              │              │ LandingController   │
  │                              │              │   ->index()         │
  │                              │              │                     │
  │                              │              │ return view(        │
  │                              │              │   'landing',        │
  │                              │              │   ['contacto' =>    │
  │                              │              │     $request->query │
  │                              │              │     ('contacto')]   │
  │                              │              │ );                  │
  │                              │              └─────────┬──────────┘
  │                              │                        │
  │                              │              ┌─────────┴──────────┐
  │                              │              │ landing.blade.php   │
  │                              │              │                     │
  │                              │              │ - CSRF token via    │
  │                              │              │   @csrf directive   │
  │                              │              │ - Static HTML       │
  │                              │              │ - CSS/JS/img refs   │
  │                              │              │   from public/      │
  │                              │              │ - Bootstrap CDN     │
  │                              │              │ - GA tag            │
  │                              │              └─────────┬──────────┘
  │                              │                        │
  │  200 OK (HTML)               │                        │
  │<─────────────────────────────│────────────────────────┘
  │                              │
  │  GET /css/styles.css         │
  │─────────────────────────────>│  (served directly by Apache,
  │  200 OK (CSS)                │   no Laravel involved)
  │<─────────────────────────────│
  │                              │
  │  GET /js/header.js           │
  │─────────────────────────────>│  (static file)
  │  GET /js/varios.js           │
  │  GET /img/*.png|jpg          │
  │  GET /font/*.woff            │
  │<─────────────────────────────│
```

### Sequence 2: Contact Form Submission (POST /contacto)

```
Browser                    Laravel Router         ContactFormRequest     ContactService        Database
  │                              │                        │                    │                  │
  │  POST /contacto              │                        │                    │                  │
  │  {csrf_token, nombre,        │                        │                    │                  │
  │   apellido, correo,          │                        │                    │                  │
  │   telefono, comentario}      │                        │                    │                  │
  │─────────────────────────────>│                        │                    │                  │
  │                              │                        │                    │                  │
  │                    ┌─────────┴──────────┐             │                    │                  │
  │                    │  Middleware Stack   │             │                    │                  │
  │                    │  1. SecurityHeaders │             │                    │                  │
  │                    │  2. throttle:10,1   │             │                    │                  │
  │                    │  3. VerifyCsrfToken │             │                    │                  │
  │                    │     (validates      │             │                    │                  │
  │                    │      _token field)  │             │                    │                  │
  │                    └─────────┬──────────┘             │                    │                  │
  │                              │                        │                    │                  │
  │                              │  [If CSRF fails: 419]  │                    │                  │
  │                              │  [If throttle: 429]    │                    │                  │
  │                              │                        │                    │                  │
  │                              │──Resolve FormRequest──>│                    │                  │
  │                              │                        │                    │                  │
  │                              │               ┌────────┴─────────┐         │                  │
  │                              │               │ Validation Rules  │         │                  │
  │                              │               │                   │         │                  │
  │                              │               │ correo: required, │         │                  │
  │                              │               │   email, max:255  │         │                  │
  │                              │               │ nombre: nullable, │         │                  │
  │                              │               │   string, max:100 │         │                  │
  │                              │               │ apellido: nullable│         │                  │
  │                              │               │   string, max:100 │         │                  │
  │                              │               │ telefono: nullable│         │                  │
  │                              │               │   regex, max:20   │         │                  │
  │                              │               │ comentario:       │         │                  │
  │                              │               │   nullable,       │         │                  │
  │                              │               │   string, max:1000│         │                  │
  │                              │               └────────┬─────────┘         │                  │
  │                              │                        │                    │                  │
  │                              │  [If validation fails] │                    │                  │
  │                              │  302 → /?error=validacion                   │                  │
  │                              │                        │                    │                  │
  │                              │  [If validation passes]│                    │                  │
  │                              │<───validated() data────┘                    │                  │
  │                              │                        │                    │                  │
  │                    ┌─────────┴──────────┐                                  │                  │
  │                    │ LandingController   │                                  │                  │
  │                    │   ->store()         │                                  │                  │
  │                    │                     │                                  │                  │
  │                    │ $this->contactService                                 │                  │
  │                    │   ->save($validated)│                                  │                  │
  │                    └─────────┬──────────┘                                  │                  │
  │                              │                                             │                  │
  │                              │──────────────────────>│                     │                  │
  │                              │                       │                     │                  │
  │                              │              ┌────────┴─────────┐           │                  │
  │                              │              │ ContactService    │           │                  │
  │                              │              │   ->save()        │           │                  │
  │                              │              │                   │           │                  │
  │                              │              │ Cliente::create(  │           │                  │
  │                              │              │   $validated      │──────────>│                  │
  │                              │              │ );                │           │  INSERT INTO     │
  │                              │              │                   │           │  clientes (...)  │
  │                              │              │                   │           │  VALUES (?, ?,   │
  │                              │              │                   │           │    ?, ?, ?)      │
  │                              │              │                   │<──────────│                  │
  │                              │              │ return $cliente;  │           │                  │
  │                              │              └────────┬─────────┘           │                  │
  │                              │                       │                     │                  │
  │                              │<──────────────────────┘                     │                  │
  │                              │                                             │                  │
  │  302 → /?contacto=enviado    │                                             │                  │
  │<─────────────────────────────│                                             │                  │
  │                              │                                             │                  │
  │  GET /?contacto=enviado      │                                             │                  │
  │─────────────────────────────>│                                             │                  │
  │                              │  (landing.blade.php renders,                │                  │
  │                              │   JS checks URL param,                      │                  │
  │                              │   shows Bootstrap modal)                    │                  │
  │  200 OK (HTML + modal shown) │                                             │                  │
  │<─────────────────────────────│                                             │                  │
```

---

## File Changes

### New Files (Laravel Project)

| File | Action | Description |
|------|--------|-------------|
| `app/Http/Controllers/LandingController.php` | Create | Thin controller: `index()` renders Blade, `store()` delegates to ContactService then redirects |
| `app/Http/Requests/ContactFormRequest.php` | Create | Validates correo (required, email), nombre, apellido, telefono, comentario with same rules as current `save_form.php` |
| `app/Http/Middleware/SecurityHeaders.php` | Create | Adds X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, CSP headers |
| `app/Models/Cliente.php` | Create | Eloquent model with `$fillable = ['nombre', 'apellido', 'email', 'telefono', 'comentario']`, `$table = 'clientes'` |
| `app/Services/ContactService.php` | Create | Single `save(array $data): Cliente` method using `Cliente::create()` |
| `database/migrations/xxxx_create_clientes_table.php` | Create | Migration: id, nombre (varchar 100 nullable), apellido (varchar 100 nullable), email (varchar 255), telefono (varchar 20 nullable), comentario (text nullable), timestamps |
| `resources/views/landing.blade.php` | Create | Converted from `index.php`: replaces PHP CSRF with `@csrf`, keeps ALL HTML structure identical |
| `routes/web.php` | Modify | Add `GET /` and `POST /contacto` routes |
| `bootstrap/app.php` | Modify | Register SecurityHeaders middleware globally |
| `.env` | Create | DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_DEBUG=false, APP_URL=https://qbocorredoresdeseguros.com |
| `.env.example` | Modify | Add project-specific env vars documentation |
| `public/css/styles.css` | Create | Exact copy of current `css/styles.css` |
| `public/js/header.js` | Create | Exact copy of current `js/header.js` |
| `public/js/varios.js` | Create | Exact copy of current `js/varios.js` |
| `public/img/*` | Create | Exact copy of all 17 image files from `img/` |
| `public/font/*` | Create | Exact copy of all 3 font files from `font/` |
| `public/.htaccess` | Modify | Laravel default .htaccess (already handles routing to index.php) |

### Files to Remove (Post-Migration, NOT During Build)

| File | Action | Description |
|------|--------|-------------|
| `index.php` | Delete | Replaced by Laravel routing + Blade template |
| `save_form.php` | Delete | Replaced by LandingController@store + ContactFormRequest + ContactService |
| `db.php` | Delete | Replaced by Laravel's `.env` + `config/database.php` + Eloquent |

### Files Unchanged

| File | Status | Description |
|------|--------|-------------|
| `css/styles.css` | Copied as-is | To `public/css/styles.css` -- zero modifications |
| `js/header.js` | Copied as-is | To `public/js/header.js` -- zero modifications |
| `js/varios.js` | Copied as-is | To `public/js/varios.js` -- zero modifications |
| `img/*` | Copied as-is | All 17 files to `public/img/` |
| `font/*` | Copied as-is | All 3 `.woff` files to `public/font/` |

---

## Interfaces / Contracts

### LandingController

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService,
    ) {}

    public function index(Request $request): View
    {
        return view('landing', [
            'contacto' => $request->query('contacto'),
        ]);
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $this->contactService->save($request->validated());

        return redirect('/?contacto=enviado');
    }
}
```

### ContactFormRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public form, no auth required
    }

    public function rules(): array
    {
        return [
            'nombre'     => ['nullable', 'string', 'max:100'],
            'apellido'   => ['nullable', 'string', 'max:100'],
            'correo'     => ['required', 'email', 'max:255'],
            'telefono'   => ['nullable', 'string', 'regex:/^[\d\s\+\-\(\)]{5,20}$/', 'max:20'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo electronico es obligatorio.',
            'correo.email'    => 'Por favor ingrese un correo electronico valido.',
            'telefono.regex'  => 'El telefono no tiene un formato valido.',
        ];
    }
}
```

### ContactService

```php
<?php

namespace App\Services;

use App\Models\Cliente;

class ContactService
{
    public function save(array $data): Cliente
    {
        return Cliente::create([
            'nombre'     => $data['nombre'] ?? null,
            'apellido'   => $data['apellido'] ?? null,
            'email'      => $data['correo'],
            'telefono'   => $data['telefono'] ?? null,
            'comentario' => $data['comentario'] ?? null,
        ]);
    }
}
```

### Cliente Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'comentario',
    ];
}
```

### SecurityHeaders Middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net https://www.googletagmanager.com https://www.google-analytics.com; "
            . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; "
            . "img-src 'self' https://imageupload.io https://www.xpertys.com.mx https://www.google-analytics.com data:; "
            . "font-src 'self'; "
            . "connect-src 'self' https://www.google-analytics.com; "
            . "frame-ancestors 'self'"
        );

        return $response;
    }
}
```

### Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
            $table->string('apellido', 100)->nullable();
            $table->string('email', 255);
            $table->string('telefono', 20)->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
```

### Routes (web.php)

```php
<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/contacto', [LandingController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contacto.store');
```

### Blade Template Key Changes (landing.blade.php)

The Blade template is a direct 1:1 conversion of `index.php` with these changes ONLY:

| Original (index.php) | Laravel (landing.blade.php) |
|---|---|
| `<?php session_start(); ... ?>` | Removed (Laravel handles sessions) |
| `<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">` | `@csrf` |
| `action="save_form.php"` | `action="{{ route('contacto.store') }}"` |
| `<body onload="contactoEnviado()">` | `<body onload="contactoEnviado()">` (unchanged, JS stays the same) |

Everything else -- every `<div>`, every class, every `<img>` src, every CDN link, every inline script -- stays IDENTICAL.

---

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Feature | GET `/` returns 200, renders landing view | `$this->get('/')->assertOk()->assertViewIs('landing')` |
| Feature | POST `/contacto` with valid data redirects to `/?contacto=enviado` | `$this->post('/contacto', $validData)->assertRedirect('/?contacto=enviado')` |
| Feature | POST `/contacto` with invalid email returns validation error | `$this->post('/contacto', ['correo' => 'not-email'])->assertSessionHasErrors('correo')` |
| Feature | POST `/contacto` creates record in `clientes` table | `$this->assertDatabaseHas('clientes', ['email' => 'test@test.com'])` |
| Feature | CSRF token missing returns 419 | `$this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)` is NOT applied -- test that raw POST without token gets 419 |
| Feature | Rate limiting returns 429 after 10 requests | Loop 11 POST requests, assert 11th returns 429 |
| Unit | ContactService creates Cliente record | Mock DB, call `save()`, assert `Cliente::create` called with correct data |
| Unit | ContactFormRequest rules match expected validation | Instantiate request, assert `rules()` returns expected array |
| Visual | Landing page renders identically to original | Manual comparison (screenshot diff) -- no automated visual regression tool in scope |
| Security | Security headers present in response | `$this->get('/')->assertHeader('X-Frame-Options', 'SAMEORIGIN')` |
| Security | Post-deployment nmap/nikto scan | As specified in `config.yaml` verify rules |

---

## Migration / Rollout

### Phase 1: Build (Local)

1. Create Laravel 12 project via `composer create-project laravel/laravel qbo-laravel`
2. Copy static assets (`css/`, `js/`, `img/`, `font/`) to `public/`
3. Create all app files (Controller, FormRequest, Service, Model, Migration, Middleware, Blade)
4. Configure `.env` with DB credentials
5. Run `php artisan migrate` to create `clientes` table
6. Test locally with `php artisan serve`
7. Visual comparison: original vs Laravel (screenshot both, diff)

### Phase 2: Deploy to cPanel

1. Upload Laravel project to `/home/cpanel-user/qbo-app/` (above public_html)
2. Backup current `public_html/` contents
3. Remove current `public_html/` directory
4. Create symlink: `ln -s /home/cpanel-user/qbo-app/public /home/cpanel-user/public_html`
5. Copy `.env` to `/home/cpanel-user/qbo-app/.env` with production credentials
6. SSH or cPanel terminal: `cd /home/cpanel-user/qbo-app && composer install --no-dev --optimize-autoloader`
7. Run: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
8. Run: `php artisan migrate` (on production DB)
9. Verify: visit site, submit test form, check DB

### Phase 3: Existing Data

- If the existing `clientes` table already has data from the vanilla PHP app, the migration should check `Schema::hasTable('clientes')` before creating. If the table exists with the old schema (no `timestamps` columns), create a separate migration to add `created_at` and `updated_at` columns.
- Alternatively, if the table is empty or data is expendable (compromised by XANTIBOT), drop and recreate cleanly.

### Rollback Plan

1. Remove symlink: `rm /home/cpanel-user/public_html`
2. Restore backup: `mv /home/cpanel-user/public_html_backup /home/cpanel-user/public_html`
3. Site is back to vanilla PHP immediately
4. Total rollback time: ~2 minutes

---

## cPanel-Specific Constraints

| Constraint | Solution |
|---|---|
| No SSH on all plans | Use cPanel Terminal (available on most modern plans) or upload via FTP |
| PHP version must be 8.2+ | Set via cPanel MultiPHP Manager (Laravel 12 requires PHP 8.2+) |
| No `composer` on server | Run `composer install` locally, upload `vendor/` via FTP if SSH unavailable |
| No symlink permissions | Alternative: modify `public/index.php` to set `$app->usePublicPath()` and copy public files to `public_html/`, adjust paths in `bootstrap/app.php` |
| Storage permissions | `chmod -R 775 storage bootstrap/cache` after upload |
| No cron for scheduler | Not needed for this app (no scheduled tasks) |
| APP_KEY generation | Run `php artisan key:generate` or set manually in `.env` |

---

## Open Questions

- [x] Architecture pattern: Clean MVC (decided -- single landing page, not complex enough for DDD)
- [x] Asset strategy: Static files, no Vite (decided -- preserves visual parity)
- [ ] Existing `clientes` table: Does the production DB already have this table with data? If yes, need a conditional migration strategy (add timestamps to existing table vs. fresh create)
- [ ] cPanel SSH access: Does the hosting plan have SSH/Terminal access? This determines whether we can run `composer install` and `php artisan` on the server, or need to upload `vendor/` manually
- [ ] PHP version on server: Confirm PHP 8.2+ is available via cPanel MultiPHP Manager (Laravel 12 requirement)
- [ ] Domain DNS: Is `qbocorredoresdeseguros.com` pointing to this cPanel account? Any CDN/proxy (Cloudflare) in front?
