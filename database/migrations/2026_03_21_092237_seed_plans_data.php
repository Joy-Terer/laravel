<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('plans')->insert([
            [
                'name'                    => 'Free',
                'slug'                    => 'free',
                'description'             => 'Perfect for small groups just getting started.',
                'price_kes'               => 0,
                'price_usd'               => 0,
                'max_members'             => 10,
                'has_pdf_export'          => false,
                'has_email_notifications' => false,
                'has_audit_logs'          => false,
                'has_multiple_chamas'     => false,
                'has_custom_branding'     => false,
                'has_priority_support'    => false,
                'has_api_access'          => false,
                'is_active'               => true,
                'sort_order'              => 1,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'name'                    => 'Basic',
                'slug'                    => 'basic',
                'description'             => 'For growing chamas that need more features.',
                'price_kes'               => 499,
                'price_usd'               => 3.84,
                'max_members'             => 30,
                'has_pdf_export'          => true,
                'has_email_notifications' => true,
                'has_audit_logs'          => true,
                'has_multiple_chamas'     => false,
                'has_custom_branding'     => false,
                'has_priority_support'    => false,
                'has_api_access'          => false,
                'is_active'               => true,
                'sort_order'              => 2,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'name'                    => 'Premium',
                'slug'                    => 'premium',
                'description'             => 'For established chamas that need full control.',
                'price_kes'               => 999,
                'price_usd'               => 7.69,
                'max_members'             => 100,
                'has_pdf_export'          => true,
                'has_email_notifications' => true,
                'has_audit_logs'          => true,
                'has_multiple_chamas'     => false,
                'has_custom_branding'     => false,
                'has_priority_support'    => true,
                'has_api_access'          => false,
                'is_active'               => true,
                'sort_order'              => 3,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'name'                    => 'Premium+',
                'slug'                    => 'premium_plus',
                'description'             => 'For large organisations managing multiple chamas.',
                'price_kes'               => 1999,
                'price_usd'               => 15.38,
                'max_members'             => -1,
                'has_pdf_export'          => true,
                'has_email_notifications' => true,
                'has_audit_logs'          => true,
                'has_multiple_chamas'     => true,
                'has_custom_branding'     => true,
                'has_priority_support'    => true,
                'has_api_access'          => true,
                'is_active'               => true,
                'sort_order'              => 4,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
        ]);
 
        // Assign all existing chamas to Free plan
        $freePlanId = DB::table('plans')->where('slug', 'free')->value('id');
        DB::table('chamas')->update(['plan_id' => $freePlanId]);
    }
 
    public function down(): void
    {
        DB::table('plans')->truncate();
    }
};
 