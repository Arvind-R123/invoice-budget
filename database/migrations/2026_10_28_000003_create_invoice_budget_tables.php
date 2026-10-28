<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->string('role')->default('admin')->index());

        Schema::create('customers', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->nullable(); $table->string('phone')->nullable(); $table->text('address')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->nullable(); $table->string('phone')->nullable(); $table->text('address')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('type')->default('product'); $table->decimal('unit_price', 12, 2); $table->decimal('tax_rate', 5, 2)->default(0); $table->timestamps(); $table->softDeletes();
        });
        foreach (['sales_invoices' => 'customer_id', 'purchase_invoices' => 'supplier_id'] as $tableName => $party) {
            Schema::create($tableName, function (Blueprint $table) use ($party) {
                $table->id(); $table->string('invoice_number')->unique(); $table->foreignId($party)->constrained()->restrictOnDelete(); $table->date('issue_date'); $table->date('due_date'); $table->decimal('discount_value', 12, 2)->default(0); $table->string('discount_type')->default('flat'); $table->decimal('tax_rate', 5, 2)->default(0); $table->decimal('subtotal', 12, 2)->default(0); $table->decimal('discount_amount', 12, 2)->default(0); $table->decimal('tax_amount', 12, 2)->default(0); $table->decimal('total', 12, 2)->default(0); $table->string('status')->default('Pending')->index(); $table->text('notes')->nullable(); $table->timestamps(); $table->softDeletes(); $table->index(['issue_date', 'due_date']);
            });
        }
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id(); $table->morphs('invoice'); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $table->string('description'); $table->decimal('quantity', 12, 2); $table->decimal('unit_price', 12, 2); $table->decimal('tax_rate', 5, 2)->default(0); $table->decimal('discount_value', 12, 2)->default(0); $table->string('discount_type')->default('flat'); $table->decimal('line_total', 12, 2); $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); $table->morphs('invoice'); $table->date('payment_date'); $table->decimal('amount', 12, 2); $table->string('method')->default('bank'); $table->text('notes')->nullable(); $table->timestamps(); $table->index('payment_date');
        });
        Schema::create('budgets', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('period_type'); $table->string('category'); $table->decimal('amount', 12, 2); $table->date('period_start'); $table->date('period_end'); $table->timestamps(); $table->index(['period_type', 'period_start']);
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->id(); $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete(); $table->date('expense_date'); $table->string('category'); $table->decimal('amount', 12, 2); $table->string('description'); $table->timestamps(); $table->index(['expense_date', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses'); Schema::dropIfExists('budgets'); Schema::dropIfExists('payments'); Schema::dropIfExists('invoice_items'); Schema::dropIfExists('purchase_invoices'); Schema::dropIfExists('sales_invoices'); Schema::dropIfExists('products'); Schema::dropIfExists('suppliers'); Schema::dropIfExists('customers'); Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};
