<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamas', function (Blueprint $table) {
            // M-Pesa collection details
            $table->string('mpesa_type')->default('paybill')->after('code'); // paybill | till
            $table->string('mpesa_shortcode', 20)->nullable()->after('mpesa_type');
            $table->string('mpesa_account_name', 100)->nullable()->after('mpesa_shortcode');
            $table->string('mpesa_consumer_key')->nullable()->after('mpesa_account_name');
            $table->string('mpesa_consumer_secret')->nullable()->after('mpesa_consumer_key');
            $table->string('mpesa_passkey')->nullable()->after('mpesa_consumer_secret');

            // Chama extra details
            $table->string('phone', 20)->nullable()->after('mpesa_passkey');
            $table->string('location')->nullable()->after('phone');
            $table->string('meeting_day')->nullable()->after('location'); // e.g. "Every 1st Saturday"
            $table->string('category')->default('general')->after('meeting_day'); // general|women|youth|investment
        });
    }

    public function down(): void
    {
        Schema::table('chamas', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_type', 'mpesa_shortcode', 'mpesa_account_name',
                'mpesa_consumer_key', 'mpesa_consumer_secret', 'mpesa_passkey',
                'phone', 'location', 'meeting_day', 'category',
            ]);
        });
    }
};
