<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Chama;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $chama = Chama::where('code', strtoupper($request->chama_code))->first();

        if (!$chama) {
            return back()->withErrors(['chama_code' => 'Invalid Chama code. Please check and try again.'])->withInput();
        }

        if ($chama->status !== 'active') {
            return back()->withErrors(['chama_code' => 'This Chama is not active. Please contact the administrator.'])->withInput();
        }

        if($chama->members()->count() >= $chama->max_members) {
            return back()->withErrors(['chama_code' => 'This Chama has reached its maximum number of members.'])->withInput();
        }

      // Create the user with pending status
        $user = User::create([
            'chama_id' => $chama->id,
            'name'     => $request->first_name . ' ' . $request->last_name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'member',
            'status'   => 'pending',
        ]);

        // If this is the first member (the admin), give chama a 14-day Premium trial
        $isFirstMember = $chama->members()->count() === 1;
 
        if ($isFirstMember) {
            $premiumPlan = Plan::where('slug', 'premium')->first();
 
            if ($premiumPlan) {
                // Assign premium plan with trial
                $chama->update([
                    'plan_id'       => $premiumPlan->id,
                    'trial_ends_at' => now()->addDays(14),
                ]);
 
                // Create trial subscription
                Subscription::create([
                    'chama_id'       => $chama->id,
                    'plan_id'        => $premiumPlan->id,
                    'status'         => 'trial',
                    'billing_cycle'  => 'monthly',
                    'starts_at'      => now(),
                    'ends_at'        => now()->addDays(14),
                    'trial_ends_at'  => now()->addDays(14),
                    'auto_renew'     => false,
                ]);
            }
        }

       AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'member.registered',
            'description' => "{$user->name} registered and is awaiting admin approval.",
            'ip_address'  => $request->ip(),
        ]);
 
        $message = $isFirstMember
            ? 'Registration successful! Your chama has been activated with a 14-day Premium trial. Please wait for approval.'
            : 'Registration successful! Your account is pending approval.';

            dd('Redirecting to pending page now...', $request->first_name, $chama->name);
 
        return redirect()->route('register.pending')
        ->with('registered_name', $request->first_name )
        ->with('registered_chama', $chama->name);

    }
}
