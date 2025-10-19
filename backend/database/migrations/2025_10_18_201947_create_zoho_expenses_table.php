<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_id')->unique();
            $table->date('date')->nullable();

            $table->string('user_name')->nullable();
            $table->string('paid_through_account_name')->nullable();
            $table->string('account_name')->nullable();

            $table->text('description')->nullable();

            // Currency info
            $table->string('currency_id')->nullable();
            $table->string('currency_code', 10)->nullable();

            // Monetary values
            $table->decimal('bcy_total', 15, 2)->default(0);
            $table->decimal('bcy_total_without_tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('total_without_tax', 15, 2)->default(0);

            $table->boolean('is_billable')->nullable();
            $table->string('reference_number')->nullable();

            // Customer / Vendor
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('vendor_name')->nullable();

            $table->string('status')->nullable();

            $table->timestamp('created_time')->nullable();
            $table->timestamp('last_modified_time')->nullable();

            $table->string('expense_receipt_name')->nullable();
            $table->decimal('exchange_rate', 10, 6)->default(1);

            // Mileage fields
            $table->decimal('distance', 10, 2)->default(0);
            $table->decimal('mileage_rate', 10, 2)->default(0);
            $table->string('mileage_unit', 10)->nullable();
            $table->string('mileage_type')->nullable();
            $table->string('expense_type')->nullable();

            // Report info
            $table->string('report_id')->nullable();
            $table->string('report_name')->nullable();
            $table->string('report_number')->nullable();

            $table->boolean('has_attachment')->default(false);
            $table->json('custom_fields_list')->nullable();
            $table->json('tags')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoho_expenses');
    }
};
