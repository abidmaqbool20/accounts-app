<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Optional: quick reference or relational optimization
            $table->unsignedBigInteger('latest_receipt_id')->nullable()->after('has_attachment');
            $table->foreign('latest_receipt_id')
                ->references('id')
                ->on('expense_receipts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['latest_receipt_id']);
            $table->dropColumn('latest_receipt_id');
        });
    }
};
