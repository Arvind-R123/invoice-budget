<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(['email' => env('ADMIN_EMAIL', 'admin@example.com')], ['name' => env('ADMIN_NAME', 'Admin'), 'password' => env('ADMIN_PASSWORD', 'password'), 'role' => 'admin']);
        $customer = \App\Models\Customer::firstOrCreate(['email' => 'customer@example.com'], ['name' => 'Acme Retail']);
        $supplier = \App\Models\Supplier::firstOrCreate(['email' => 'supplier@example.com'], ['name' => 'Global Supplies']);
        \App\Models\Product::firstOrCreate(['name' => 'Consulting'], ['type'=>'service','unit_price'=>150,'tax_rate'=>18]);
        \App\Models\Product::firstOrCreate(['name' => 'Office equipment'], ['type'=>'product','unit_price'=>500,'tax_rate'=>18]);
        \App\Models\Budget::firstOrCreate(['name'=>'Operations '.now()->year], ['period_type'=>'yearly','category'=>'Operations','amount'=>10000,'period_start'=>now()->startOfYear(),'period_end'=>now()->endOfYear()]);
    }
}
