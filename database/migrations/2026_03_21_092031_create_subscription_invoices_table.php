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
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
             $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chama_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount_kes', 10, 2)->default(0);
            $table->decimal('amount_usd', 10, 2)->default(0);
            $table->string('currency')->default('KES');
            $table->string('payment_method')->nullable(); // mpesa|paypal
            $table->string('transaction_code')->nullable();
            $table->string('status')->default('pending'); // pending|paid|failed
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
