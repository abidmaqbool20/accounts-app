<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_receipts', function (Blueprint $table) {
            $table->id();

            // Relationship to Zoho Expenses
            $table->foreignId('expense_id')
                ->nullable()
                ->constrained('expenses')
                ->onDelete('cascade');

            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_download_url')->nullable();
            $table->timestamp('uploaded_time')->nullable();

            $table->string('mime_type')->nullable(); // optional
            $table->boolean('downloaded')->default(false); // flag if file is stored locally

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_receipts');
    }
};
