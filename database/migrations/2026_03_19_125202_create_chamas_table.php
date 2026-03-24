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
        Schema::create('chamas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code', 20)->unique();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('contribution_amount', 15, 2)->default(2000.00);
            $table->string('contribution_frequency')->default('monthly');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('chamas');
    }
};
