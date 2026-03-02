# Specifications: laravel-refactor

> Migration of QBO Corredores de Seguros from vanilla PHP to Laravel 12.
> The landing page MUST render identically after migration.
> Security is the #1 priority -- site was previously compromised via SQL injection.

**RFC 2119 keywords** apply throughout: MUST, MUST NOT, SHALL, SHALL NOT, SHOULD, SHOULD NOT, MAY.

---

## 1. Functional Requirements

### SPEC-FR-01: Landing Page Renders All Eight Sections

**Given** a visitor navigates to the root URL `/`
**When** the page loads in any modern browser (Chrome, Firefox, Safari, Edge)
**Then** the page MUST render all eight sections in this exact order:

| # | Section ID | Content |
|---|-----------|---------|
| 1 | `#myHeader` | Sticky header with logo, nav links (Inicio, Quienes somos, Tipos de seguro, Quiero cotizar), Facebook social icon |
| 2 | `#first` | Hero section with background image (`seguros.jpg`), heading "Distintas necesidades? Diferentes opciones", subheading, CTA button, animated scroll-down arrow |
| 3 | `#second` | "Quienes somos" section with left background image (`corredores_de_seguros.jpg`), logo, content toggle (Conoce/Mision/Vision), CTA button |
| 4 | `#third` | Values section with 5 icons and labels: Respeto, Responsabilidad, Integridad, Conciencia social, Servicio |
| 5 | `#four` | Insurance types section (Individuales: 5 items, Empresariales: 10 items) with dark blue background, Cotizar CTA |
| 6 | `#five` | Contact form section with 5 fields (nombre, apellido, correo, telefono, comentario) and submit button |
| 7 | `#seven` | Partner logos section with 10 insurance company logos in two rows |
| 8 | `#footer` | Footer with eAspayb link, two physical addresses, phone number, "Sitio web por Bedtime Studio" credit |

**And** section `#six` (parallax image divider with `seguro_de_vida.png`) MUST be present between `#five` and `#seven`
**And** the success modal (`#myModal`) MUST be present in the DOM (hidden by default)
**And** all text content MUST match the original verbatim (Spanish copy, accents, line breaks)

---

### SPEC-FR-02: Contact Form Submission Saves to Database via Eloquent

**Given** a visitor has filled out the contact form at section `#five`
**And** the form fields are: `nombre` (text), `apellido` (text), `correo` (email, required), `telefono` (text), `comentario` (textarea)
**When** the visitor clicks "Enviar"
**Then** the form MUST submit via POST to a Laravel route (e.g., `POST /contacto`)
**And** the request MUST be handled by a dedicated controller (e.g., `ContactoController@store`)
**And** the data MUST be persisted to the `clientes` MySQL table using Eloquent ORM
**And** the Eloquent model MUST map to columns: `nombre`, `apellido`, `email`, `telefono`, `comentario`
**And** upon success, the user MUST be redirected to `/?contacto=enviado`
**And** upon validation failure, the user MUST be redirected back with error indicators
**And** upon server error, the error MUST be logged and the user MUST be redirected to `/?error=servidor`
**And** raw SQL queries MUST NOT be used anywhere in the application

---

### SPEC-FR-03: Server-Side Form Validation via Form Request

**Given** a contact form submission arrives at the server
**When** the Laravel controller processes the request
**Then** validation MUST be handled by a dedicated Form Request class (e.g., `StoreContactoRequest`)
**And** the following validation rules MUST apply:

| Field | Rules |
|-------|-------|
| `nombre` | nullable, string, max:100 |
| `apellido` | nullable, string, max:100 |
| `correo` | required, email, max:255 |
| `telefono` | nullable, string, regex:`/^[\d\s\+\-\(\)]{5,20}$/`, max:20 |
| `comentario` | nullable, string, max:1000 |

**And** validation error messages SHOULD be in Spanish
**And** the Form Request MUST sanitize inputs (trim whitespace) before validation
**And** the `nombre` field MUST be mapped to the `nombre` database column
**And** the `correo` field MUST be mapped to the `email` database column

---

### SPEC-FR-04: Success Modal After Form Submission

**Given** a contact form was submitted successfully
**And** the user was redirected to `/?contacto=enviado`
**When** the page finishes loading
**Then** the Bootstrap modal (`#myModal`) MUST be displayed automatically
**And** the modal MUST contain:
  - Title: "Datos enviados"
  - Body: "Sus datos han sido enviados. Pronto sera contactado por el equipo de QBO"
  - A "Cerrar" button that dismisses the modal
**And** the modal trigger logic MUST use the same `contactoEnviado()` JavaScript function pattern (read URL query parameter `contacto`, show modal if value is `enviado`)

---

### SPEC-FR-05: Sticky Header Behavior Preserved

**Given** the page has loaded
**When** the user scrolls down past the header's original position
**Then** the header (`#myHeader`) MUST receive the CSS class `sticky`
**And** the header MUST become fixed at the top of the viewport (`position: fixed; top: 0;`)
**When** the user scrolls back up to the original header position
**Then** the `sticky` class MUST be removed
**And** the header MUST return to its normal document flow position
**And** the JavaScript implementation MUST use the same `window.onscroll` pattern from `header.js`

---

### SPEC-FR-06: Content Toggle (Conoce/Mision/Vision) Preserved

**Given** the "Quienes somos" section (`#second`) is visible
**When** the user clicks the "Conoce" button
**Then** the paragraph `#elemento` MUST display the "Conoce" text: "Somos un equipo de profesionales con mas de 25 anos de experiencia..."
**And** the "Conoce" button MUST have CSS class `select` (active state)
**And** the "Mision" and "Vision" buttons MUST have CSS class `no-select` (inactive state)

**When** the user clicks the "Mision" button
**Then** `#elemento` MUST display: "Asesorar a nuestros clientes en la gestion de sus seguros y riesgos..."
**And** the "Mision" button MUST have class `select`
**And** the other two buttons MUST have class `no-select`

**When** the user clicks the "Vision" button
**Then** `#elemento` MUST display: "Generar un valor agregado a nuestros clientes, basado en ser profesionales, eticos..."
**And** the "Vision" button MUST have class `select`
**And** the other two buttons MUST have class `no-select`

**And** the `changeState()` JavaScript function MUST be preserved with identical logic

---

### SPEC-FR-07: Responsive Design Preserved

**Given** the page is rendered on different viewport widths
**Then** the following responsive breakpoints MUST be preserved exactly:

| Breakpoint | Key Behaviors |
|-----------|---------------|
| Desktop (> 1024px) | All sections at designed heights, two-column layout in `#second`, partner logos at default size |
| Tablet (813px - 1024px) | Enlarged typography for `.big` (4.0rem) and `.midium` (3.2rem), full-width logos |
| Small Tablet (414px - 813px) | Reduced typography, `#second` at 130vh, `#four` at 150vh, `#five` at 175vh, `#seven` at 80vh, `#footer` at 47vh |
| Mobile (< 414px) | `#fondo-2` hidden (0% width/height), `.fifty` becomes full-width, `.logos` at 30%, scroll arrow hidden, `#third` at 140vh, border-radius removed from cards |

**And** the CSS MUST use the same three `@media` breakpoints: `max-width: 1024px`, `max-width: 813px`, `max-width: 414px`
**And** the Bootstrap 4.5.3 grid system MUST remain the layout foundation
**And** custom fonts (Galano Grotesque Bold, Light, Medium) MUST load from local `.woff` files

---

### SPEC-FR-08: Google Analytics Preserved

**Given** the page is rendered
**Then** the HTML head MUST include the Google Analytics gtag.js snippet
**And** the tracking ID MUST be `G-XBHY47M8BH`
**And** the gtag script MUST load asynchronously from `https://www.googletagmanager.com/gtag/js?id=G-XBHY47M8BH`
**And** the `dataLayer` initialization and `gtag('config', 'G-XBHY47M8BH')` call MUST be present
**And** the tracking ID SHOULD be configurable via environment variable (e.g., `GA_TRACKING_ID`) but MUST default to `G-XBHY47M8BH`

---

## 2. Security Requirements

### SPEC-SEC-01: CSRF Protection on All Forms

**Given** the Laravel application is running
**When** any form is rendered in a Blade template
**Then** the form MUST include the `@csrf` Blade directive
**And** Laravel's `VerifyCsrfToken` middleware MUST be active for all web routes
**And** the hidden `_token` field MUST be automatically included in every form
**And** any POST request without a valid CSRF token MUST be rejected with HTTP 419

---

### SPEC-SEC-02: SQL Injection Prevention

**Given** any database interaction in the application
**Then** ALL database queries MUST use Eloquent ORM or the Laravel Query Builder
**And** raw SQL queries (`DB::raw()`, `DB::statement()`, `DB::unprepared()`) MUST NOT be used
**And** the `Cliente` Eloquent model MUST define a `$fillable` array to whitelist mass-assignable fields: `['nombre', 'apellido', 'email', 'telefono', 'comentario']`
**And** the `$guarded` property MUST NOT be set to an empty array

---

### SPEC-SEC-03: Input Validation via Form Request

**Given** any user input reaches the application
**Then** ALL input MUST be validated through a Laravel Form Request class before processing
**And** the Form Request MUST be type-hinted in the controller method signature (automatic resolution)
**And** validation MUST happen BEFORE any database operation
**And** failed validation MUST redirect back with errors (Laravel default behavior)
**And** the validated data MUST be retrieved via `$request->validated()` (not `$request->all()` or `$request->input()`)

---

### SPEC-SEC-04: Environment Variables for Credentials

**Given** the application is deployed
**Then** ALL sensitive configuration MUST be stored in the `.env` file:
  - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - `APP_KEY` (auto-generated via `php artisan key:generate`)
  - `APP_ENV` (set to `production` on live server)
  - `APP_DEBUG` (set to `false` on live server)
**And** the `.env` file MUST be listed in `.gitignore`
**And** a `.env.example` file MUST be provided with placeholder values (no real credentials)
**And** `config/database.php` MUST reference `env()` helper for all database credentials
**And** hardcoded credentials MUST NOT exist anywhere in the codebase

---

### SPEC-SEC-05: Security Headers via Middleware

**Given** any HTTP response is sent by the application
**Then** the following security headers SHOULD be set via a custom middleware:

| Header | Value |
|--------|-------|
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `X-XSS-Protection` | `1; mode=block` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` |

**And** the middleware MUST be registered in the application's global middleware stack
**And** the `Strict-Transport-Security` header SHOULD be added when HTTPS is confirmed active

---

### SPEC-SEC-06: No Error Disclosure in Production

**Given** `APP_ENV=production` and `APP_DEBUG=false`
**When** any exception or error occurs
**Then** the user MUST see a generic error page (Laravel's default 500 page or a custom one)
**And** stack traces, SQL queries, file paths, and environment details MUST NOT be exposed to the user
**And** errors MUST be logged to `storage/logs/laravel.log`
**And** the `APP_DEBUG` environment variable MUST be set to `false` in the production `.env`
**And** the `debug` key in `config/app.php` MUST read from `env('APP_DEBUG', false)`

---

### SPEC-SEC-07: HTTPS Enforcement

**Given** the application is deployed to the production server
**Then** the application SHOULD enforce HTTPS via one of:
  - Apache `.htaccess` redirect rule (preferred for cPanel shared hosting)
  - Laravel `App\Http\Middleware\TrustProxies` configuration for load balancer/proxy setups
  - `URL::forceScheme('https')` in `AppServiceProvider::boot()`
**And** all internal links and asset URLs generated by Laravel helpers (`url()`, `asset()`, `route()`) MUST use HTTPS in production
**And** the `APP_URL` environment variable MUST be set to `https://qbocorredoresdeseguros.com`
**And** mixed content (HTTP resources on HTTPS page) MUST NOT occur

---

### SPEC-SEC-08: Rate Limiting on Form Submission

**Given** the contact form endpoint exists at `POST /contacto`
**Then** the route MUST be protected by Laravel's rate limiting middleware
**And** the rate limit SHOULD be set to a maximum of **5 submissions per minute per IP address**
**And** when the limit is exceeded, the server MUST respond with HTTP 429 (Too Many Requests)
**And** the rate limiter MUST be configured in `RouteServiceProvider` or `bootstrap/app.php` (Laravel 12)
**And** the throttle key SHOULD be based on the client IP address

---

## 3. Deployment Requirements

### SPEC-DEP-01: Deployable to cPanel Shared Hosting

**Given** the target hosting environment is cPanel shared hosting (Apache, no root access)
**When** the Laravel application is deployed
**Then** the `public/` directory MUST serve as the document root (mapped to `public_html/`)
**And** all Laravel framework files (app/, bootstrap/, config/, database/, routes/, storage/, vendor/) MUST reside OUTSIDE of `public_html/` (one level up, e.g., in `~/laravel/`)
**And** the `public/index.php` MUST be modified to reference the correct paths to `bootstrap/app.php` and `vendor/autoload.php` relative to the deployment structure
**And** `storage/` and `bootstrap/cache/` directories MUST have write permissions (chmod 775)
**And** the deployment MUST NOT require SSH access, Composer on the server, or `php artisan` commands run on the server (vendor/ SHOULD be uploaded pre-built)
**And** a deployment guide document SHOULD be provided describing the exact cPanel file structure

---

### SPEC-DEP-02: MySQL Database Compatibility

**Given** the existing MySQL database on the cPanel server
**Then** the Laravel migration for the `clientes` table MUST match the existing schema:

```
clientes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NULL,
    apellido    VARCHAR(100) NULL,
    email       VARCHAR(255) NOT NULL,
    telefono    VARCHAR(20) NULL,
    comentario  TEXT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
)
```

**And** if the `clientes` table already exists, the migration SHOULD use a conditional check (`Schema::hasTable()`) to avoid errors
**And** the migration MAY add `created_at` and `updated_at` columns if they do not already exist (Eloquent timestamps)
**And** the `DB_CONNECTION` MUST be set to `mysql` in `.env`
**And** the `DB_CHARSET` SHOULD be `utf8mb4` and `DB_COLLATION` SHOULD be `utf8mb4_unicode_ci`

---

### SPEC-DEP-03: Apache .htaccess Compatibility

**Given** the hosting server runs Apache with `mod_rewrite` enabled
**Then** the `public/.htaccess` MUST be the standard Laravel `.htaccess` that:
  - Enables `mod_rewrite`
  - Redirects all requests to `index.php` (front controller pattern)
  - Handles `Authorization` header forwarding
**And** a root-level `.htaccess` SHOULD redirect requests from `public_html/` to `public_html/public/` if the deployment structure requires it
**And** the `.htaccess` MUST include HTTPS redirect rules for production
**And** the `.htaccess` MUST NOT conflict with cPanel's default Apache configuration

---

### SPEC-DEP-04: Static Assets Accessible

**Given** the Laravel application is deployed
**Then** all static assets MUST be accessible from the `public/` directory:

| Asset Type | Source Location | Laravel Location |
|-----------|----------------|-----------------|
| CSS | `css/styles.css` | `public/css/styles.css` |
| JavaScript | `js/varios.js`, `js/header.js` | `public/js/varios.js`, `public/js/header.js` |
| Images | `img/*.png`, `img/*.jpg` | `public/img/*` (all 22 files) |
| Fonts | `font/*.woff` | `public/font/*.woff` (3 files: Bold, Light, Medium) |

**And** the CSS MUST reference fonts and images using relative paths (`../font/`, `../img/`) as in the original
**And** external CDN resources MUST remain unchanged:
  - Bootstrap 4.5.3 CSS and JS from `cdn.jsdelivr.net`
  - jQuery 3.5.1 from `code.jquery.com`
  - Google Analytics from `googletagmanager.com`
**And** the `asset()` helper MAY be used for Blade template asset references but relative paths in CSS MUST NOT change
**And** all 22 image files, 3 font files, 1 CSS file, and 2 JS files MUST be present and unmodified

---

## 4. Non-Functional Requirements

### SPEC-NF-01: Blade Template Structure

**Given** the migration to Laravel
**Then** the single `index.php` page MUST be converted to a Blade template (e.g., `resources/views/landing.blade.php`)
**And** the template SHOULD extend a base layout (`resources/views/layouts/app.blade.php`)
**And** the layout MUST include the `<head>` section with meta tags, CSS, JS, and Google Analytics
**And** the `@csrf` directive MUST be present in the contact form
**And** all PHP output MUST use Blade syntax (`{{ }}` for escaped output, `{!! !!}` only when explicitly needed for trusted HTML)

### SPEC-NF-02: Route Definitions

**Given** the Laravel application
**Then** the following routes MUST be defined in `routes/web.php`:

| Method | URI | Controller | Action |
|--------|-----|-----------|--------|
| GET | `/` | `LandingController` | `index` |
| POST | `/contacto` | `ContactoController` | `store` |

**And** the GET `/` route MUST return the landing Blade view
**And** the POST `/contacto` route MUST be protected by CSRF middleware (default) and rate limiting
**And** no API routes are required for this migration

### SPEC-NF-03: No Feature Regression

**Given** the migrated Laravel application
**Then** the following behaviors from the original MUST be preserved without modification:
  - Smooth scroll behavior (`scroll-behavior: smooth` on `html`)
  - Anchor navigation (`#first`, `#second`, `#four`, `#five`)
  - Bootstrap modal functionality (jQuery-based)
  - All CSS animations (scroll-down arrow keyframes)
  - All external links (Facebook, eAspayb portal)
  - Phone link (`tel:+50222129752`)
  - Open Graph and Twitter Card meta tags
  - Page title: "QBO - Corredores de seguros en Guatemal" (note: original has typo, MUST preserve as-is)

---

## Traceability Matrix

| Spec ID | Category | Priority | Depends On |
|---------|----------|----------|-----------|
| SPEC-FR-01 | Functional | MUST | SPEC-NF-01, SPEC-DEP-04 |
| SPEC-FR-02 | Functional | MUST | SPEC-SEC-02, SPEC-DEP-02 |
| SPEC-FR-03 | Functional | MUST | SPEC-SEC-03 |
| SPEC-FR-04 | Functional | MUST | SPEC-FR-02, SPEC-NF-01 |
| SPEC-FR-05 | Functional | MUST | SPEC-DEP-04 |
| SPEC-FR-06 | Functional | MUST | SPEC-DEP-04 |
| SPEC-FR-07 | Functional | MUST | SPEC-NF-01, SPEC-DEP-04 |
| SPEC-FR-08 | Functional | MUST | SPEC-NF-01 |
| SPEC-SEC-01 | Security | MUST | SPEC-NF-01 |
| SPEC-SEC-02 | Security | MUST | SPEC-FR-02 |
| SPEC-SEC-03 | Security | MUST | SPEC-FR-03 |
| SPEC-SEC-04 | Security | MUST | SPEC-DEP-01 |
| SPEC-SEC-05 | Security | SHOULD | - |
| SPEC-SEC-06 | Security | MUST | SPEC-SEC-04 |
| SPEC-SEC-07 | Security | SHOULD | SPEC-DEP-03 |
| SPEC-SEC-08 | Security | MUST | SPEC-FR-02 |
| SPEC-DEP-01 | Deployment | MUST | - |
| SPEC-DEP-02 | Deployment | MUST | - |
| SPEC-DEP-03 | Deployment | MUST | SPEC-DEP-01 |
| SPEC-DEP-04 | Deployment | MUST | SPEC-DEP-01 |
| SPEC-NF-01 | Non-Functional | MUST | - |
| SPEC-NF-02 | Non-Functional | MUST | - |
| SPEC-NF-03 | Non-Functional | MUST | SPEC-FR-01 |
