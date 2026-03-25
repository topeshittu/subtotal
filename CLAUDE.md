# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Subtotal (BardPOS)** is a full-featured Point of Sale and business management system built on Laravel 12. It handles sales, purchasing, accounting, inventory, CRM, manufacturing, and restaurant operations.

## Commands

### Frontend
```bash
npm run dev       # Start Vite dev server
npm run build     # Build production assets
```

### Backend
```bash
php artisan migrate               # Run database migrations
php artisan key:generate          # Generate APP_KEY
php artisan test                  # Run all tests
php artisan test --filter=TestName  # Run a single test
./vendor/bin/phpunit tests/Feature/SomeTest.php  # Run specific test file
./vendor/bin/pint                 # Format PHP code (Laravel Pint)
```

### Setup
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Architecture

### Modular Structure
The app uses `nwidart/laravel-modules` for plugin-like modules under `Modules/`:
- **Accounting** — financial management and reporting
- **CRM** — customer relationship management
- **Essentials** — core HRM functionality
- **Manufacturing** — production workflows
- **ProductCatalogue** — product management
- **Desktopapp** — desktop client support
- **Superadmin** — subscription and licensing

Each module is self-contained with its own controllers, models, views, routes, and migrations.

### Core Application (`app/`)
- **Controllers/** — 92 RESTful controllers covering all business domains
- **Models/** — 74 Eloquent models; key entities: `Business`, `BusinessLocation`, `Product`, `Contact`, `Sell`, `Purchase`, `Account`
- **Services/** — `MenuService`, `AppSettingsService`, file upload, backup services
- **Actions/** — single-purpose action classes for complex business operations
- **Jobs/** — async processing for recurring invoices/expenses and payment reminders
- **Events/ + Listeners/** — event-driven architecture for major business operations; real-time broadcasting via Pusher

### Multi-Tenancy
The `Business` / `BusinessLocation` models are the top-level tenant boundary. Most queries scope by `business_id`.

### Frontend
- Blade templates with **AdminLTE** admin theme
- **Livewire** for real-time components (e.g., `PosProductSearch`)
- **Vite** bundles `resources/sass/app.scss` and `resources/js/app.js`
- **DataTables** for server-side table rendering throughout the UI

### Authentication & Authorization
- **Laravel Passport** (OAuth2) for API clients and desktop app
- **Laravel Sanctum** for token-based API auth
- **Spatie Permission** for role-based access control — permissions are checked throughout controllers

### Key Integrations
- Payment gateways: Stripe, PayPal, Razorpay, Paystack, Pesapal, Flutterwave
- Cloud storage: AWS S3, Dropbox (via Spatie Backup)
- PDF: mPDF and DOMPDF
- Excel import/export: Maatwebsite Excel
- OpenAI integration (`openai-php/laravel`)
- ZATCA (Saudi Arabia e-invoicing compliance)
- WooCommerce sync

### Routes
`routes/web.php` is very large (~61KB). API routes are in `routes/api.php`. Module routes are registered from within each module directory.
