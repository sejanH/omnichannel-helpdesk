@extends('layouts.app')

@section('title', 'Subscription & Billing — OmniDesk Helpdesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 md:p-10 space-y-8">

    <!-- Top Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Subscription & Billing</h1>
                @if($subscription->onTrial())
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Trial Active ({{ $subscription->daysLeftOnTrial() }} Days Left)
                    </span>
                @elseif($subscription->subscription_status === 'active')
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Subscription
                    </span>
                @else
                    <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold font-mono rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        {{ ucfirst($subscription->subscription_status) }}
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-xs md:text-sm mt-1">Manage your company subscription plan, agent seat quotas, payment method, and billing receipts.</p>
        </div>

        @if($subscription->subscription_status === 'active')
            <form action="{{ route('billing.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription? Your account will switch to read-only at the end of the billing period.');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs rounded-xl transition cursor-pointer">
                    Cancel Subscription
                </button>
            </form>
        @elseif($subscription->subscription_status === 'canceled')
            <form action="{{ route('billing.resume') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                    Reactivate Subscription
                </button>
            </form>
        @endif
    </div>

    <!-- Alert Banners -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
            <x-icon name="check" class="text-lg text-emerald-600" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning') || session('status'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center gap-2">
            <x-icon name="alert-triangle" class="text-lg text-amber-600" />
            <span>{{ session('warning') ?? session('status') }}</span>
        </div>
    @endif

    <!-- Current Quota & Payment Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Plan Status Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-mono uppercase text-[10px] tracking-wider text-indigo-600 font-bold">Current Tier</span>
                <x-icon name="credit-card" class="text-lg text-indigo-600" />
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $planDetails['name'] }}</div>
                <div class="text-sm font-bold text-slate-700 mt-0.5">{{ $planDetails['price'] }}</div>
            </div>
            <div class="text-xs text-slate-500 border-t border-slate-100 pt-3">
                @if($subscription->subscription_ends_at)
                    Next Renewal: <span class="text-slate-800 font-semibold">{{ $subscription->subscription_ends_at->format('M d, Y') }}</span>
                @else
                    Trial Expiration: <span class="text-amber-600 font-semibold">{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A' }}</span>
                @endif
            </div>
        </div>

        <!-- Agent Seat Utilization Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-mono uppercase text-[10px] tracking-wider text-cyan-600 font-bold">Agent Seats</span>
                <x-icon name="users" class="text-lg text-cyan-600" />
            </div>
            <div>
                <div class="flex justify-between text-xs text-slate-700 font-bold mb-1">
                    <span>Used Seats</span>
                    <span>{{ $agentCount }} / {{ $subscription->max_agent_seats }} Seats</span>
                </div>
                @php
                    $agentPct = min(100, round(($agentCount / max(1, $subscription->max_agent_seats)) * 100));
                @endphp
                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-200">
                    <div class="bg-cyan-600 h-full transition-all duration-500" style="width: {{ $agentPct }}%;"></div>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 border-t border-slate-100 pt-3">
                {{ max(0, $subscription->max_agent_seats - $agentCount) }} available agent licenses remaining on your current plan.
            </p>
        </div>

        <!-- Saved Payment Method Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-mono uppercase text-[10px] tracking-wider text-emerald-600 font-bold">Payment Method</span>
                <x-icon name="lock" class="text-lg text-emerald-600" />
            </div>
            <div>
                @if($subscription->pm_last_four)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-7 rounded bg-slate-100 border border-slate-300 flex items-center justify-center font-mono text-xs font-bold text-slate-800 uppercase">
                            {{ $subscription->pm_type ?? 'Card' }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">•••• •••• •••• {{ $subscription->pm_last_four }}</div>
                            <div class="text-[11px] text-emerald-600">Stripe Verified Card</div>
                        </div>
                    </div>
                @else
                    <div class="text-sm font-semibold text-slate-700">No payment method on file</div>
                    <div class="text-[11px] text-slate-500">Add a credit card below to ensure uninterrupted service</div>
                @endif
            </div>
            <button onclick="openPaymentModal('{{ $subscription->subscription_plan }}')" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                {{ $subscription->pm_last_four ? 'Update Card Details' : 'Add Payment Card' }}
            </button>
        </div>

    </div>

    <!-- License Key Verification Card -->
    <div class="bg-indigo-50/70 p-6 rounded-2xl border border-indigo-200 space-y-4 shadow-xs">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <x-icon name="key" class="text-indigo-600 text-lg" />
                    <span>Activate Granted License Key</span>
                </h3>
                <p class="text-xs text-slate-600 mt-0.5">Have an official license granted for <strong>{{ request()->getHost() }}</strong>? Enter your key below to unlock your workspace.</p>
            </div>
        </div>
        <form action="{{ route('billing.verify_license') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <input type="text" name="license_key" placeholder="OMNI-XXXX-YYYY-ZZZZ" required class="flex-1 w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 font-mono focus:outline-none focus:border-indigo-600 shadow-2xs">
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer whitespace-nowrap">
                Verify & Activate License
            </button>
        </form>
    </div>

    <!-- Available Subscription Plans Section -->
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Choose a Plan for Your Business</h2>
            <p class="text-xs text-slate-500 mt-1">Upgrade or downgrade anytime. Changes apply immediately to your workspace quotas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Starter Plan -->
            <div class="bg-white p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative shadow-xs {{ $subscription->subscription_plan === 'starter' ? 'border-indigo-600 bg-indigo-50/30 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300' }}">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900">Starter</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">Small Teams</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900">$49</span>
                        <span class="text-slate-500 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>3</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>2</strong> Omnichannel Outlets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>Web Chat & Email</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>30-Day Message Retention</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'starter')
                        <button disabled class="w-full py-3 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-default border border-slate-200">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('starter')" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition cursor-pointer">Switch to Starter ($49/mo)</button>
                    @endif
                </div>
            </div>

            <!-- Pro Plan (Featured) -->
            <div class="bg-white p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative shadow-md {{ $subscription->subscription_plan === 'pro' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-indigo-300 hover:border-indigo-500' }}">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-indigo-600 to-cyan-600 text-white text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-full shadow-xs">
                    Most Popular Choice
                </div>
                <div class="space-y-6">
                    <div class="flex justify-between items-center pt-1">
                        <h3 class="text-lg font-bold text-slate-900">Pro Business</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Growing Businesses</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900">$149</span>
                        <span class="text-slate-500 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>10</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>10</strong> Omnichannel Channels</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>WhatsApp & Reverb WebSockets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>Custom Live Chat Builder Studio</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>Unlimited Message Retention</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'pro')
                        <button disabled class="w-full py-3 bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl cursor-default border border-indigo-200">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('pro')" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer">Switch to Pro ($149/mo)</button>
                    @endif
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="bg-white p-8 rounded-2xl border transition-all duration-200 flex flex-col justify-between relative shadow-xs {{ $subscription->subscription_plan === 'enterprise' ? 'border-indigo-600 bg-indigo-50/30 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300' }}">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900">Enterprise</h3>
                        <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">Large Scale</span>
                    </div>
                    <div>
                        <span class="text-4xl font-black text-slate-900">$299</span>
                        <span class="text-slate-500 text-xs font-medium">/ month</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>50</strong> Support Agent Seats</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span><strong>50</strong> Omnichannel Outlets</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>Priority SLA Monitor & Escalation</span></li>
                        <li class="flex items-center gap-2"><x-icon name="check" class="text-emerald-600 text-sm" /> <span>Dedicated Account Manager</span></li>
                    </ul>
                </div>
                <div class="pt-8">
                    @if($subscription->subscription_plan === 'enterprise')
                        <button disabled class="w-full py-3 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-default border border-slate-200">Current Active Plan</button>
                    @else
                        <button onclick="openPaymentModal('enterprise')" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition cursor-pointer">Switch to Enterprise ($299/mo)</button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Billing Invoices History Table -->
    <div class="space-y-4 pt-4">
        <h2 class="text-lg font-bold text-slate-900">Billing Receipts & Invoice History</h2>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-mono text-[10px]">
                        <tr>
                            <th class="p-4">Invoice ID</th>
                            <th class="p-4">Plan / Description</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-mono text-slate-500">{{ $invoice->stripe_invoice_id ?? 'INV-'.$invoice->id }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $invoice->plan_name }}</td>
                                <td class="p-4 text-slate-500">{{ $invoice->created_at->format('M d, Y') }}</td>
                                <td class="p-4 font-bold text-slate-900">${{ number_format($invoice->amount_cents / 100, 2) }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <button onclick="alert('Downloading invoice {{ $invoice->stripe_invoice_id ?? $invoice->id }} PDF receipt...')" class="text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer">
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
<div id="payment-modal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white border border-slate-200 rounded-2xl p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Stripe Card & Plan Upgrade</h3>
                <p class="text-xs text-slate-500">Secured with 256-bit Stripe Payment Gateway</p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <x-icon name="x" class="text-xl" />
            </button>
        </div>

        <form action="{{ route('billing.plan.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="plan" id="modal-selected-plan" value="pro">

            <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 flex items-center justify-between">
                <span>Selected Plan: <strong id="modal-plan-title">Pro Business Plan</strong></span>
                <span class="font-bold text-indigo-700" id="modal-plan-price">$149/month</span>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-700">Cardholder Name</label>
                <input type="text" name="card_name" value="{{ $user->name }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-700">Credit Card Number</label>
                <div class="relative">
                    <input type="text" placeholder="4242 •••• •••• 4242" required maxlength="19" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                    <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400 font-mono">VISA / MC</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Expires (MM/YY)</label>
                    <input type="text" placeholder="12/28" required maxlength="5" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">CVC Code</label>
                    <input type="text" placeholder="123" required maxlength="4" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-indigo-500 font-mono">
                </div>
            </div>

            <input type="hidden" name="card_last4" value="4242">

            <div class="pt-2 flex items-center gap-3">
                <button type="button" onclick="closePaymentModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition cursor-pointer">
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
