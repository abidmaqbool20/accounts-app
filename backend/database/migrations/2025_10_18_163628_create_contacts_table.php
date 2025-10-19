<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->unique();
            $table->string('contact_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('language_code')->nullable();
            $table->string('language_code_formatted')->nullable();
            $table->string('contact_type')->nullable();
            $table->string('contact_type_formatted')->nullable();
            $table->string('status')->nullable();
            $table->string('customer_sub_type')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_linked_with_zohocrm')->default(false);
            $table->integer('payment_terms')->default(0);
            $table->string('payment_terms_label')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('currency_code')->nullable();

            $table->decimal('outstanding_receivable_amount', 15, 2)->default(0);
            $table->decimal('outstanding_receivable_amount_bcy', 15, 2)->default(0);
            $table->decimal('outstanding_payable_amount', 15, 2)->default(0);
            $table->decimal('outstanding_payable_amount_bcy', 15, 2)->default(0);
            $table->decimal('unused_credits_receivable_amount', 15, 2)->default(0);
            $table->decimal('unused_credits_receivable_amount_bcy', 15, 2)->default(0);
            $table->decimal('unused_credits_payable_amount', 15, 2)->default(0);
            $table->decimal('unused_credits_payable_amount_bcy', 15, 2)->default(0);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();

            $table->string('portal_status')->nullable();
            $table->string('portal_status_formatted')->nullable();

            $table->timestamp('created_time')->nullable();
            $table->timestamp('last_modified_time')->nullable();

            $table->json('custom_fields')->nullable();
            $table->json('custom_field_hash')->nullable();
            $table->json('tags')->nullable();

            $table->boolean('ach_supported')->default(false);
            $table->boolean('has_attachment')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoho_contacts');
    }
};
