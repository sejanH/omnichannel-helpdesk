<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Models\Channel;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Display Subscription & Billing Dashboard (Admin Only)
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Subscription and billing management is restricted to administrators only.');
        }

        $user = Auth::user();
        $subscription = $user->getOrProvisionSubscription();

        $planDetails = $subscription->getPlanDetails();
        $invoices = $user->invoices;
        $agentCount = User::count();
        $channelCount = Channel::count();

        return view('billing', compact('user', 'subscription', 'planDetails', 'invoices', 'agentCount', 'channelCount'));
    }

    /**
     * Update Subscription Plan & Payment Method (Admin Only)
     */
    public function updatePlan(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can change the subscription plan.');
        }

        $request->validate([
            'plan' => 'required|in:starter,pro,enterprise',
            'card_name' => 'nullable|string',
            'card_last4' => 'nullable|string|max:4',
        ]);

        $user = Auth::user();
        $subscription = $user->getOrProvisionSubscription();

        $newPlan = $request->input('plan');
        $last4 = $request->input('card_last4', '4242');
        $cardType = 'visa';

        $seatsAndChannels = match($newPlan) {
            'starter' => ['seats' => 3, 'channels' => 2, 'cents' => 4900],
            'enterprise' => ['seats' => 50, 'channels' => 50, 'cents' => 29900],
            default => ['seats' => 10, 'channels' => 10, 'cents' => 14900],
        };

        $subscription->update([
            'subscription_plan' => $newPlan,
            'subscription_status' => 'active',
            'pm_type' => $cardType,
            'pm_last_four' => $last4,
            'max_agent_seats' => $seatsAndChannels['seats'],
            'max_channels' => $seatsAndChannels['channels'],
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $user->flushUserCache();

        // Create billing invoice record
        BillingInvoice::create([
            'user_id' => $user->id,
            'stripe_invoice_id' => 'in_' . \Illuminate\Support\Str::random(14),
            'plan_name' => strtoupper($newPlan) . ' Subscription',
            'amount_cents' => $seatsAndChannels['cents'],
            'currency' => 'usd',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Your subscription has been updated to the ' . ucfirst($newPlan) . ' Plan!');
    }

    /**
     * Cancel Subscription (Admin Only)
     */
    public function cancel()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can cancel the subscription.');
        }

        $user = Auth::user();
        $subscription = $user->getOrProvisionSubscription();

        $subscription->update([
            'subscription_status' => 'canceled',
            'subscription_ends_at' => now(),
        ]);

        $user->flushUserCache();

        return redirect()->back()->with('status', 'Your subscription has been canceled. Your account is now in read-only mode.');
    }

    /**
     * Resume Subscription (Admin Only)
     */
    public function resume()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can resume the subscription.');
        }

        $user = Auth::user();
        $subscription = $user->getOrProvisionSubscription();

        $subscription->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $user->flushUserCache();

        return redirect()->back()->with('success', 'Your subscription has been reactivated successfully!');
    }

    /**
     * Handle Automated Stripe Webhook Events (Public Endpoint)
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;

        if ($eventType === 'customer.subscription.updated' || $eventType === 'invoice.payment_succeeded') {
            $customerStripeId = $payload['data']['object']['customer'] ?? null;
            $sub = Subscription::where('stripe_id', $customerStripeId)->first();

            if ($sub) {
                $sub->update([
                    'subscription_status' => 'active',
                    'subscription_ends_at' => now()->addMonth(),
                ]);
            }
        } elseif ($eventType === 'invoice.payment_failed') {
            $customerStripeId = $payload['data']['object']['customer'] ?? null;
            $sub = Subscription::where('stripe_id', $customerStripeId)->first();

            if ($sub) {
                $sub->update(['subscription_status' => 'past_due']);
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify and Activate License Key (Admin Only)
     */
    public function verifyLicense(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can activate license keys.');
        }

        $request->validate([
            'license_key' => 'required|string',
        ]);

        $key = trim($request->input('license_key'));
        $result = \App\Services\LicenseService::verifyAndApplyLicense($key, $request->getHost());

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('warning', $result['message']);
        }
    }
}
