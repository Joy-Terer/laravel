<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // ── Show edit form ────────────────────────────────────────────
    public function edit()
    {
        return view('profile.edit');
    }

    // ── Update profile info ───────────────────────────────────────
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $user->update($request->only('name', 'email', 'phone'));

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'profile.updated',
            'description' => "{$user->name} updated their profile information.",
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Update password ───────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'password.changed',
            'description' => Auth::user()->name . " changed their password.",
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    // ── Delete account ────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Your account has been permanently deleted.');
    }
}