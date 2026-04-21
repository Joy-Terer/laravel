<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
{
    Schema::table('chamas', function (Blueprint $table) {
        if (!Schema::hasColumn('chamas', 'subscription_plan')) {
            $table->string('subscription_plan')->default('free')->after('category');
        }
        if (!Schema::hasColumn('chamas', 'subscription_status')) {
            $table->string('subscription_status')->default('active')->after('subscription_plan');
        }
        if (!Schema::hasColumn('chamas', 'is_active')) {
            $table->boolean('is_active')->default(true)->after('subscription_status');
        }
        if (!Schema::hasColumn('chamas', 'trial_ends_at')) {
            $table->timestamp('trial_ends_at')->nullable()->after('is_active');
        }
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
