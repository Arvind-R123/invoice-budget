# Invoice & Budget Management System

Laravel 13 application for admin-managed customers, suppliers, products, sales/purchase invoices, payments, budgets, expenses, reporting-ready totals, and cached currency rates. Invoice line items use a polymorphic relationship so the same item/payment tables serve both sales and purchase invoices.

## Setup

Requirements: PHP 8.3+, Composer, MySQL (SQLite is convenient for local tests), and Node/npm.

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Set `DB_CONNECTION=mysql` and the normal `DB_*` variables for MySQL. Admin seed credentials come from `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD`. Currency integration uses `CURRENCY_API_URL`, `CURRENCY_API_TIMEOUT`, and `CURRENCY_API_CACHE_TTL`; failures are handled with a dashboard fallback.

Run tests with `php artisan test`.

## Structure

`app/Models` contains domain relationships; `app/Services/InvoiceCalculator.php` is the single decimal calculation boundary; controllers handle HTTP orchestration; migrations enforce foreign keys/indexes and soft deletes; Blade templates provide the shared Bootstrap layout and invoice screens. The invoice calculator uses BCMath and never performs money arithmetic with floats.
