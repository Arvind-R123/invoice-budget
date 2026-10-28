# Invoice & Budget

A Bootstrap-based Laravel application for managing customers, suppliers, products, invoices, payments, budgets, expenses, currency rates, and financial reports.

## Features

- Sales and purchase invoices with item calculations and live previews
- Paid, Pending, and Overdue invoice status tracking
- Payment history with partial payments and overpayment protection
- Customer, supplier, product, budget, and expense CRUD
- Monthly and yearly budget tracking with expense summaries
- Dashboard KPIs and Frankfurter currency rates
- Responsive reports with CSV exports

## Tech stack

Laravel, PHP 8.4, MySQL, Blade, Bootstrap 5 CDN, PHPUnit, and Frankfurter.

## Setup

1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env`.
4. Run `php artisan key:generate`.
5. Configure the MySQL database values in `.env`.
6. Run `php artisan migrate:fresh --seed`.
7. Start the application with `php artisan serve`.

Seeded admin credentials: `admin@example.com` / `ChangeMe123!`. These can be changed with `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and `ADMIN_NAME`.

## Database tables

`users`, `customers`, `suppliers`, `products`, `sales_invoices`, `purchase_invoices`, `invoice_items`, `payments`, `budgets`, `expenses`, `cache`, and `jobs`.

## Currency API

Currency rates are provided by Frankfurter. Configure the endpoint and behavior with `CURRENCY_API_URL`, `CURRENCY_API_TIMEOUT`, and `CURRENCY_API_CACHE_TTL` in `.env`.

On Windows, cURL error 60 usually means PHP cannot find a current CA certificate bundle. Configure PHP cURL with a valid CA bundle; do not disable TLS verification in production.

## Tests

Run the test suite with:

```bash
php artisan test
```

## Development notes

AI assistance was used during development for UI polish, regression tests, and documentation.
