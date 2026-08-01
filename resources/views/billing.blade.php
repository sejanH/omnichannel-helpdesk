@extends('layouts.app')

@section('title', 'Subscription & Billing — OmniDesk Helpdesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-950 p-6 md:p-10 space-y-8">

    <!-- Top Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Subscription & Billing</h1>
                @if($subscription->onTrial())
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/30 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Trial Active ({{ $subscription->daysLeftOnTrial() }} Days Left)
                    </span>
                @elseif($subscription->subscription_status === 'active')
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Active Subscription
                    </span>
                @else
                    <span class="px-3 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/30 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                        {{ ucfirst($subscription->subscription_status) }}
                    </span>
                @endif
            </div>
            <p class="text-slate-400 text-xs md:text-sm mt-1">Manage your company subscription plan, agent seat quotas, payment method, and billing receipts.</p>
        </div>

        @if($subscription->subscription_status === 'active')
            <form action="{{ route('billing.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription? Your account will switch to read-only at the end of the billing period.');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-semibold text-xs rounded-xl transition cursor-pointer">
                    Cancel Subscription
                </button>
            </form>
        @elseif($subscription->subscription_status === 'canceled')
            <form action="{{ route('billing.resume') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition cursor-pointer">
                    Reactivate Subscription
                </button>
            </form>
        @endif
    </div>

    <!-- Alert Banners -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-medium flex items-center gap-2">
            <x-icon name="check" class="text-lg text-emerald-400" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning') || session('status'))
        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-medium flex items-center gap-2">
            <x-icon name="alert-triangle" class="text-lg text-amber-400" />
            <span>{{ session('warning') ?? session('status') }}</span>
        </div>
    @endif

    <!-- Current Quota & Payment Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Plan Status Card -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-mono uppercase text-[10px] tracking-wider text-indigo-400 font-bold">Current Tier</span>
                <x-icon name="credit-card" class="text-lg text-indigo-400" />
            </div>
            <div>
                <div class="text-2xl font-black text-white">{{ $planDetails['name'] }}</div>
                <div class="text-sm font-bold text-slate-300 mt-0.5">{{ $planDetails['price'] }}</div>
            </div>
            <div class="text-xs text-slate-400 border-t border-slate-800/80 pt-3">
                @if($subscription->subscription_ends_at)
                    Next Renewal: <span class="text-slate-200 font-semibold">{{ $subscription->subscription_ends_at->format('M d, Y') }}</span>
                @else
                    Trial Expiration: <span class="text-amber-400 font-semibold">{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A' }}</span>
                @endif
            </div>
        </div>

        <!-- Agent Seat Utilization Card -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-mono uppercase text-[10px] tracking-wider text-cyan-400 font-bold">Agent Seats</span>
                <x-icon name="users" class="text-lg text-cyan-400" />
            </div>
            <div>
                <div class="flex justify-between text-xs text-slate-300 font-bold mb-1">
                    <span>Used Seats</span>
                    <span>{{ $agentCount }} / {{ $subscription->max_agent_seats }} Seats</span>
                </div>
                @php
                    $agentPct = min(100, round(($agentCount / max(1, $subscription->max_agent_seats)) * 100));
                @endphp
                <div class="w-full bg-slate-800 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-cyan-500 h-full transition-all duration-500" style="width: {{ $agentPct }}%;"></div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 border-t border-slate-800/80 pt-3">
                {{ max(0, $subscription->max_agent_seats - $agentCount) }} available agent licenses remaining on your current plan.
            </p>
        </div>

        <!-- Saved Payment Method Card -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span class="font-mono uppercase text-[10px] tracking-wider text-emerald-400 font-bold">Payment Method</span>
                <x-icon name="lock" class="text-lg text-emerald-400" />
            </div>
            <div>
                @if($subscription->pm_last_four)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-7 rounded bg-slate-800 border border-slate-700 flex items-center justify-center font-mono text-xs font-bold text-slate-200 uppercase">
                            {{ $subscription->pm_type ?? 'Card' }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white">•••• •••• •••• {{ $subscription->pm_last_four }}</div>
                            <div class="text-[11px] text-emerald-400">Stripe Verified Card</div>
                        </div>
                    </div>
                @else
                    <div class="text-sm font-semibold text-slate-400">No payment method on file</div>
                    <div class="text-[11px] text-slate-500">Add a credit card below to ensure uninterrupted service</div>
                @endif
            </div>
            <button onclick="openPaymentModal('{{ $subscription->subscription_plan }}')" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition cursor-pointer">
                {{ $subscription->pm_last_four ? 'Update Card Details' : 'Add Payment Card' }}
            </button>
        </div>

    </div>

    <!-- License Key Verification Card -->
    <div class="glass-panel p-6 rounded-2xl border border-indigo-500/30 bg-indigo-950/20 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <x-icon name="key" class="text-indigo-400 text-lg" />
                    <span>Activate Granted License Key</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Have an official license granted for <strong>{{ request()->getHost() }}</strong>? Enter your key below to unlock your workspace.</p>
            </div>
        </div>
        <form action="{{ route('billing.verify_license') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <input type="text" name="license_key" placeholder="OMNI-XXXX-YYYY-ZZZZ" required class="flex-1 w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-cyan-300 font-mono focus:outline-none focus:border-indigo-500">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer whitespace-nowrap">
                Verify & Activate License
            </button>
        </form>
    </div>

    <!-- Available Subscription Plans Section -->
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-white">Choose a Plan for Your Business</h2>
            <p class="text-xs text-slate-400 mt-1">Upgrade or downgrade anytime. Changes apply immediately to your workspace quotas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Starter Plan -->
            <div class="glass-panel p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative {{ $subscription->subscription_plan === 'starter' ? 'border-indigo-500 bg-indigo-500/5 shadow-xl shadow-indigo-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700' }}">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">Starter</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300">Small Teams</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-white">$49</span>
                        <span class="text-slate-400 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>3</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>2</strong> Omnichannel Outlets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>Web Chat & Email</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>30-Day Message Retention</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'starter')
                        <button disabled class="w-full py-3 bg-slate-800 text-slate-400 font-bold text-xs rounded-xl cursor-default border border-slate-700">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('starter')" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition cursor-pointer">Switch to Starter ($49/mo)</button>
                    @endif
                </div>
            </div>

            <!-- Pro Plan (Featured) -->
            <div class="glass-panel p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative shadow-2xl {{ $subscription->subscription_plan === 'pro' ? 'border-indigo-500 bg-indigo-500/10 shadow-indigo-500/20' : 'border-indigo-500/50 bg-slate-900/90 hover:border-indigo-400' }}">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-full shadow">
                    Most Popular Choice
                </div>
                <div class="space-y-6">
                    <div class="flex justify-between items-center pt-1">
                        <h3 class="text-lg font-bold text-white">Pro Business</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Growing Businesses</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-white">$149</span>
                        <span class="text-slate-400 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>10</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>10</strong> Omnichannel Channels</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>WhatsApp & Reverb WebSockets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>Custom Live Chat Builder Studio</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>Unlimited Message Retention</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'pro')
                        <button disabled class="w-full py-3 bg-indigo-600/30 text-indigo-300 font-bold text-xs rounded-xl cursor-default border border-indigo-500/40">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('pro')" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer">Switch to Pro ($149/mo)</button>
                    @endif
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="glass-panel p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative {{ $subscription->subscription_plan === 'enterprise' ? 'border-indigo-500 bg-indigo-500/5 shadow-xl shadow-indigo-500/10' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700' }}">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">Enterprise</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300">Large Scale</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-white">$299</span>
                        <span class="text-slate-400 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>50</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span><strong>50</strong> Omnichannel Outlets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>Priority SLA Monitor & Escalation</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-400 text-sm" /> <span>Dedicated Account Manager</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'enterprise')
                        <button disabled class="w-full py-3 bg-slate-800 text-slate-400 font-bold text-xs rounded-xl cursor-default border border-slate-700">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('enterprise')" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition cursor-pointer">Switch to Enterprise ($299/mo)</button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Billing Invoices History Table -->
    <div class="space-y-4 pt-4">
        <h2 class="text-lg font-bold text-white">Billing Receipts & Invoice History</h2>

        <div class="glass-panel rounded-2xl border border-slate-800 bg-slate-900/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase font-mono text-[10px]">
                        <tr>
                            <th class="p-4">Invoice ID</th>
                            <th class="p-4">Plan / Description</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-4 font-mono text-slate-400">{{ $invoice->stripe_invoice_id ?? 'INV-'.$invoice->id }}</td>
                                <td class="p-4 font-semibold text-white">{{ $invoice->plan_name }}</td>
                                <td class="p-4 text-slate-400">{{ $invoice->created_at->format('M d, Y') }}</td>
                                <td class="p-4 font-bold text-white">${{ number_format($invoice->amount_cents / 100, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $invoice->status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <button onclick="alert('Downloading invoice {{ $invoice->stripe_invoice_id ?? $invoice->id }} PDF receipt...')" class="text-indigo-400 hover:text-indigo-300 font-semibold cursor-pointer">
                                        Download PDF
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    No billing invoices recorded yet. Select a plan above to initialize subscription billing.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Payment Card & Plan Confirmation Modal -->
<div id="payment-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="glass-panel w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-bold text-white">Stripe Card & Plan Upgrade</h3>
                <p class="text-xs text-slate-400">Secured with 256-bit Stripe Payment Gateway</p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <x-icon name="x" class="text-xl" />
            </button>
        </div>

        <form action="{{ route('billing.plan.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="plan" id="modal-selected-plan" value="pro">

            <div class="p-3 bg-indigo-500/10 border border-indigo-500/30 rounded-xl text-xs text-indigo-300 flex items-center justify-between">
                <span>Selected Plan: <strong id="modal-plan-title">Pro Business Plan</strong></span>
                <span class="font-bold text-white" id="modal-plan-price">$149/month</span>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-300">Cardholder Name</label>
                <input type="text" name="card_name" value="{{ $user->name }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-300">Credit Card Number</label>
                <div class="relative">
                    <input type="text" placeholder="4242 •••• •••• 4242" required maxlength="19" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                    <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-500 font-mono">VISA / MC</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Expires (MM/YY)</label>
                    <input type="text" placeholder="12/28" required maxlength="5" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">CVC Code</label>
                    <input type="text" placeholder="123" required maxlength="4" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
            </div>

            <input type="hidden" name="card_last4" value="4242">

            <div class="pt-2 flex items-center gap-3">
                <button type="button" onclick="closePaymentModal()" class="flex-1 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                    Confirm & Charge Card
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(plan) {
        document.getElementById('modal-selected-plan').value = plan;
        
        const titles = {
            starter: 'Starter Plan ($49/mo)',
            pro: 'Pro Business Plan ($149/mo)',
            enterprise: 'Enterprise Plan ($299/mo)'
        };
        const prices = {
            starter: '$49/month',
            pro: '$149/month',
            enterprise: '$299/month'
        };

        document.getElementById('modal-plan-title').textContent = titles[plan] || plan;
        document.getElementById('modal-plan-price').textContent = prices[plan] || '';

        const modal = document.getElementById('payment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
