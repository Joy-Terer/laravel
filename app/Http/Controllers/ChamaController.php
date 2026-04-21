<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChamaRegistrationRequest;
use App\Http\Requests\ChamaSettingsRequest;
use App\Models\Chama;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class ChamaController extends Controller
{
    // ── Show registration form ────────────────────────────────────
    public function registerForm()
    {
        return view('chama.register');
    }

    // ── Create new chama + admin user ─────────────────────────────
    public function register(ChamaRegistrationRequest $request)
    {
        // Generate unique chama code
        $code = $this->generateChamaCode($request->chama_name);

        $mpesaShortcode = match($request->mpesa_type) {
            'paybill'    => $request->mpesa_shortcode,
            'till'       => $request->mpesa_shortcode_till,
            'pochi'      => $request->mpesa_shortcode_pochi,
            'Send Money'  => $request->mpesa_shortcode_sendmoney,
            default      => $request->mpesa_shortcode,
        };

        // Create the chama
        $chama = Chama::create([
            'name'                   => $request->chama_name,
            'description'            => $request->chama_description,
            'code'                   => $code,
            'balance'                => 0.00,
            'contribution_amount'    => $request->contribution_amount,
            'contribution_frequency' => $request->contribution_frequency,
            'category'               => $request->chama_category,
            'location'               => $request->chama_location,
            'meeting_day'            => $request->meeting_day,
            'mpesa_type'             => $request->mpesa_type,
            'mpesa_shortcode'        => $request->mpesa_shortcode,
            'mpesa_account_name'     => $request->mpesa_account_name,
            'mpesa_consumer_key'     => $request->mpesa_consumer_key
                ? Crypt::encryptString($request->mpesa_consumer_key) : null,
            'mpesa_consumer_secret'  => $request->mpesa_consumer_secret
                ? Crypt::encryptString($request->mpesa_consumer_secret) : null,
            'mpesa_passkey'          => $request->mpesa_passkey
                ? Crypt::encryptString($request->mpesa_passkey) : null,
            'is_active'              => true,
        ]);

        // Create the admin user
        $admin = User::create([
            'chama_id'          => $chama->id,
            'name'              => $request->admin_name,
            'email'             => $request->admin_email,
            'phone'             => $request->admin_phone,
            'password'          => Hash::make($request->admin_password),
            'role'              => 'admin',
            'status'            => 'active', // admin is auto-approved
            'email_verified_at' => now(),
        ]);

        // Link admin to chama
        $chama->update([ 
            'mpesa_consumer_key'    => $request->mpesa_consumer_key
            ? Crypt::encryptString($request->mpesa_consumer_key) : null,    
            'mpesa_consumer_secret' => $request->mpesa_consumer_secret
            ? Crypt::encryptString($request->mpesa_consumer_secret) : null,
            'mpesa_passkey'         => $request->mpesa_passkey
            ? Crypt::encryptString($request->mpesa_passkey) : null,]);

        $chama->update(['admin_id' => $admin->id]);     
    

        // Assign free plan + 14-day Premium trial
        $premiumPlan = Plan::where('slug', 'premium')->first();
        $freePlan    = Plan::where('slug', 'free')->first();

        if ($premiumPlan) {
            $chama->update([
                'plan_id'       => $premiumPlan->id,
                'trial_ends_at' => now()->addDays(14),
            ]);

            Subscription::create([
                'chama_id'      => $chama->id,
                'plan_id'       => $premiumPlan->id,
                'status'        => 'trial',
                'billing_cycle' => 'monthly',
                'starts_at'     => now(),
                'ends_at'       => now()->addDays(14),
                'trial_ends_at' => now()->addDays(14),
                'auto_renew'    => false,
            ]);
        } elseif ($freePlan) {
            $chama->update(['plan_id' => $freePlan->id]);
        }

        // Audit log
        AuditLog::create([
            'user_id'     => $admin->id,
            'action'      => 'chama.registered',
            'description' => "{$request->chama_name} registered on SmartChama. Chama code: {$code}.",
            'ip_address'  => $request->ip(),
        ]);

        // Auto-login the admin
        Auth::login($admin);

        return redirect()->route('dashboard')
            ->with('success', "Welcome to SmartChama! Your chama code is {$code} — share it with your members so they can join.");
    }

    // ── Show settings page ────────────────────────────────────────
    public function settings()
    {
        $chama = Auth::user()->chama;
        return view('chama.settings', compact('chama'));
    }

    // ── Update settings ───────────────────────────────────────────
    public function updateSettings(ChamaSettingsRequest $request)
    {
        $chama = Auth::user()->chama;

        if ($request->hasFile('chama_logo')) {
            $logoPath = $request->file('chama_logo')->store('chama_logos', 'logo_' . $chama->id . '_' . $request->file('chama_logo')->extension(), 'private');
            $chama->update(['logo_path' => $logoPath]);
        }

        $chama->update([
            'name'                   => $request->name,
            'description'            => $request->description,
            'category'               => $request->category,
            'location'               => $request->location,
            'meeting_day'            => $request->meeting_day,
            'contribution_amount'    => $request->contribution_amount,
            'contribution_frequency' => $request->contribution_frequency,
            'phone'                  => $request->phone,
            'mpesa_type'             => $request->mpesa_type,
            'mpesa_shortcode'        => $request->mpesa_shortcode,
            'mpesa_account_name'     => $request->mpesa_account_name,

            // Only update API keys if provided (don't overwrite with empty)
            'mpesa_consumer_key'    => $request->filled('mpesa_consumer_key')
                ? Crypt::encryptString($request->mpesa_consumer_key) : $chama->mpesa_consumer_key,
            'mpesa_consumer_secret' => $request->filled('mpesa_consumer_secret')
                ? Crypt::encryptString($request->mpesa_consumer_secret) : $chama->mpesa_consumer_secret,
            'mpesa_passkey'         => $request->filled('mpesa_passkey')
                ? Crypt::encryptString($request->mpesa_passkey) : $chama->mpesa_passkey,
        ]);

        AuditLog::log(
            'chama.settings_updated',
            Auth::user()->name . " updated {$chama->name}'s settings."
        );

        return back()->with('success', 'Chama settings updated successfully.');
    }

    // ── Regenerate chama code ─────────────────────────────────────
    public function regenerateCode(Request $request)
    {
        $chama   = Auth::user()->chama;
        $newCode = $this->generateChamaCode($chama->name);

        $chama->update(['code' => $newCode]);

        AuditLog::log(
            'chama.code_regenerated',
            Auth::user()->name . " regenerated the chama code to {$newCode}."
        );

        return back()->with('success', "New chama code generated: {$newCode}");
    }

    // ── Generate unique chama code ────────────────────────────────
    private function generateChamaCode(string $chamaName): string
    {
        // Take first 6 letters of chama name (uppercase, no spaces)
        $base = strtoupper(preg_replace('/[^A-Za-z]/', '', $chamaName));
        $base = substr($base, 0, 6);

        // If name is short, pad with random letters
        if (strlen($base) < 4) {
            $base = str_pad($base, 4, strtoupper(Str::random(4)), STR_PAD_RIGHT);
        }

        // Add 4 random digits
        $code = $base . rand(1000, 9999);

        // Make sure it's unique
        while (Chama::where('code', $code)->exists()) {
            $code = $base . rand(1000, 9999);
        }

        return $code;
    }
}