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
        Schema::table('chamas', function (Blueprint $table) {
             $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subdomain')->nullable()->unique(); // e.g. wambua.smartchama.co.ke
            $table->string('logo')->nullable();
            $table->string('primary_color')->default('#1d4ed8');
            $table->boolean('is_active')->default(true);
        });
 
        // Superadmin table for platform management
        Schema::create('superadmins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chamas', function (Blueprint $table) {
            //
        });
    }
};
