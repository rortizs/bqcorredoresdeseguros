# QBO Corredores de Seguros

Sitio web de QBO Corredores de Seguros, empresa guatemalteca con más de 25 años de experiencia en el mercado asegurador.

## Stack

- **Backend**: Laravel 12 / PHP 8.3
- **Frontend**: Blade + Bootstrap 4 + CSS
- **Base de datos**: MySQL
- **Hosting**: cPanel (Apache)
- **Build**: Vite 7 + Tailwind CSS 4

## Desarrollo local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
composer dev
```

## Deployment

El sitio se despliega en cPanel via Git. El `public_html/index.php` apunta al directorio `laravel/` en el servidor.
