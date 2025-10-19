<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->unique();
            $table->string('account_name')->nullable();
            $table->string('account_code')->nullable();
            $table->string('account_type')->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_user_created')->default(false);
            $table->boolean('is_system_account')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('can_show_in_ze')->default(false);

            $table->string('parent_account_id')->nullable();
            $table->string('parent_account_name')->nullable();
            $table->integer('depth')->default(0);

            $table->boolean('has_attachment')->default(false);
            $table->boolean('is_child_present')->default(false);
            $table->integer('child_count')->nullable();

            $table->json('documents')->nullable();

            $table->boolean('is_standalone_account')->default(false);

            $table->timestamp('created_time')->nullable();
            $table->timestamp('last_modified_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoho_chart_of_accounts');
    }
};
