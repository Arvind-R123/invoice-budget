# Invoice & Budget

## Setup

1. Clone and run `composer install`.
2. Copy `.env.example` to `.env`, then run `php artisan key:generate`.
3. Configure MySQL in `.env`.
4. Run `php artisan migrate:fresh --seed`.
5. Start with `php artisan serve`.

Seeded admin: `admin@example.com` / `password` (configurable with `ADMIN_EMAIL`, `ADMIN_PASSWORD`, and `ADMIN_NAME`).

## Features

Dashboard KPIs, customers, suppliers, products, sales and purchase invoices, payments, budgets, expenses, currency rates, and CSV reports. Unpaid invoices automatically become Overdue after their due date.

## Database tables

Users, customers, suppliers, products, sales_invoices, purchase_invoices, invoice_items, payments, budgets, expenses, cache, and jobs.

## API

Currency rates use Frankfurter. For Windows cURL error 60, update the CA certificate bundle or configure PHP cURL's certificate path; do not disable TLS verification in production.

## Development notes

AI assistance was used during development for UI polish, testing support, and documentation.
