<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chama_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('original_amount', 15, 2)->nullable();
            $table->string('original_currency', 3)->default('KES');
            $table->string('payment_method')->default('mpesa'); // mpesa|paypal|wave|cash
            $table->string('transaction_ref')->nullable()->unique();
            $table->string('transaction_code')->nullable();
            $table->string('status')->default('pending');       // pending|completed|failed
            $table->text('notes')->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
