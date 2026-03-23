<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanMiddleware
{
    public function handle(Request $request, Closure $next, string $feature = ''): Response
    {
        $user  = auth()->user();
        $chama = $user?->chama;

        if (!$chama) {
            return redirect()->route('dashboard')
                ->with('error', 'No chama found for your account.');
        }

        $plan = $chama->plan;

        if (!$plan) {
            return redirect()->route('billing.plans')
                ->with('warning', 'Please select a plan to continue.');
        }

        // Check specific feature
        if ($feature) {
            $featureKey = 'has_' . $feature;

            if (isset($plan->$featureKey) && !$plan->$featureKey) {
                return redirect()->route('billing.plans')
                    ->with('upgrade', "This feature requires a higher plan. Upgrade to access {$feature}.");
            }
        }

        // Check member limit
        if ($plan->max_members !== -1) {
            $memberCount = $chama->members()->where('status', 'active')->count();

            if ($memberCount >= $plan->max_members) {
                return redirect()->route('billing.plans')
                    ->with('upgrade', "You have reached the maximum of {$plan->max_members} members on your {$plan->name} plan. Upgrade to add more members.");
            }
        }

        // Check subscription is active
        $subscription = $chama->activeSubscription();

        if (!$plan->isFree() && !$subscription) {
            return redirect()->route('billing.plans')
                ->with('warning', 'Your subscription has expired. Please renew to continue.');
        }

        return $next($request);
    }
}