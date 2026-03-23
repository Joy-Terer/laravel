<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_kes', 10, 2)->default(0);
            $table->decimal('price_usd', 10, 2)->default(0);
            $table->integer('max_members')->default(10);     // -1 = unlimited
            $table->boolean('has_pdf_export')->default(false);
            $table->boolean('has_email_notifications')->default(false);
            $table->boolean('has_audit_logs')->default(false);
            $table->boolean('has_multiple_chamas')->default(false);
            $table->boolean('has_custom_branding')->default(false);
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
