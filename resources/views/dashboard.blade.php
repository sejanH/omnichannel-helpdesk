@extends('layouts.app')

@section('title', 'Dashboard Overview — OmniDesk Helpdesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-4 sm:p-6 md:p-8 space-y-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Welcome Back, {{ auth()->user()->name ?? 'Agent' }} 👋</h1>
        <p class="text-xs text-slate-500 mt-1">Here is a quick overview of your helpdesk metrics today.</p>
    </div>

    <!-- Stats Grid (2 columns on mobile, 4 on desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        <!-- Total Tickets -->
        <a href="{{ route('tickets', ['filter' => 'all']) }}" class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-500 transition group cursor-pointer relative overflow-hidden block">
            <div class="absolute top-0 right-0 p-3 sm:p-4 opacity-10 group-hover:opacity-20 transition">
                <x-icon name="ticket" class="text-3xl sm:text-[4rem] text-indigo-600" />
            </div>
            <div class="relative z-10">
                <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1 group-hover:text-indigo-600 transition flex items-center justify-between">
                    <span>Total Tickets</span>
                    <x-icon name="arrow-up-right" class="text-xs text-slate-400 group-hover:text-indigo-600 transition" />
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_tickets'] ?? 0 }}</div>
            </div>
        </a>

        <!-- Open Tickets -->
        <a href="{{ route('tickets', ['filter' => 'open']) }}" class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-500 transition group cursor-pointer relative overflow-hidden block">
            <div class="absolute top-0 right-0 p-3 sm:p-4 opacity-10 group-hover:opacity-20 transition">
                <x-icon name="clock" class="text-3xl sm:text-[4rem] text-amber-500" />
            </div>
            <div class="relative z-10">
                <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1 group-hover:text-amber-600 transition flex items-center justify-between">
                    <span>Open Tickets</span>
                    <x-icon name="arrow-up-right" class="text-xs text-slate-400 group-hover:text-amber-600 transition" />
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['open_tickets'] ?? 0 }}</div>
            </div>
        </a>

        <!-- Resolved Tickets -->
        <a href="{{ route('tickets', ['filter' => 'resolved']) }}" class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-500 transition group cursor-pointer relative overflow-hidden block">
            <div class="absolute top-0 right-0 p-3 sm:p-4 opacity-10 group-hover:opacity-20 transition">
                <x-icon name="circle-check" class="text-3xl sm:text-[4rem] text-emerald-500" />
            </div>
            <div class="relative z-10">
                <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1 group-hover:text-emerald-600 transition flex items-center justify-between">
                    <span>Resolved Tickets</span>
                    <x-icon name="arrow-up-right" class="text-xs text-slate-400 group-hover:text-emerald-600 transition" />
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['resolved_tickets'] ?? 0 }}</div>
            </div>
        </a>

        <!-- Total Contacts -->
        <a href="{{ route('tickets') }}" class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-500 transition group cursor-pointer relative overflow-hidden block">
            <div class="absolute top-0 right-0 p-3 sm:p-4 opacity-10 group-hover:opacity-20 transition">
                <x-icon name="users" class="text-3xl sm:text-[4rem] text-cyan-600" />
            </div>
            <div class="relative z-10">
                <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mb-1 group-hover:text-cyan-600 transition flex items-center justify-between">
                    <span>Total Contacts</span>
                    <x-icon name="arrow-up-right" class="text-xs text-slate-400 group-hover:text-cyan-600 transition" />
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-cyan-600">{{ $stats['total_contacts'] ?? 0 }}</div>
            </div>
        </a>
    </div>

    <!-- Charts & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tickets by Channel Chart -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Tickets by Channel</h3>
            <div class="relative h-60 sm:h-64 w-full flex items-center justify-center">
                <canvas id="channelChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('tickets') }}" class="block p-3.5 sm:p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-indigo-500 hover:shadow-md transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition shrink-0">
                            <x-icon name="messages" class="text-lg" />
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold text-xs text-slate-900">Go to Workspace</div>
                            <div class="text-[11px] text-slate-500 truncate">View and reply to active tickets</div>
                        </div>
                        <x-icon name="chevron-right" class="text-lg text-slate-400 ml-auto group-hover:text-indigo-600 transition shrink-0" />
                    </div>
                </a>

                <a href="{{ route('kb.index') }}" class="block p-3.5 sm:p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-500 hover:shadow-md transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition shrink-0">
                            <x-icon name="help" class="text-lg" />
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold text-xs text-slate-900">Knowledge Base & FAQ</div>
                            <div class="text-[11px] text-slate-500 truncate">Search help articles & FAQs</div>
                        </div>
                        <x-icon name="chevron-right" class="text-lg text-slate-400 ml-auto group-hover:text-cyan-600 transition shrink-0" />
                    </div>
                </a>

                <a href="{{ route('docs.show') }}" class="block p-3.5 sm:p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-500 hover:shadow-md transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition shrink-0">
                            <x-icon name="book" class="text-lg" />
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold text-xs text-slate-900">Read Documentation</div>
                            <div class="text-[11px] text-slate-500 truncate">Learn how to connect WhatsApp, FB, Telegram</div>
                        </div>
                        <x-icon name="chevron-right" class="text-lg text-slate-400 ml-auto group-hover:text-emerald-600 transition shrink-0" />
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('channelChart');
        
        const chartDataRaw = {!! json_encode($ticketsByChannel ?? []) !!};
        
        const labels = chartDataRaw.map(item => item.channel_name);
        const data = chartDataRaw.map(item => item.count);
        
        if (labels.length === 0) {
            labels.push('No Data');
            data.push(1);
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#4f46e5', // Indigo
                        '#10b981', // Emerald
                        '#0891b2', // Cyan
                        '#f59e0b', // Amber
                        '#ef4444', // Rose
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: window.innerWidth < 640 ? 'bottom' : 'right',
                        labels: {
                            color: '#475569',
                            boxWidth: 12,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush