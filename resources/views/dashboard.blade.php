@extends('layouts.app')

@section('title', 'Dashboard Overview — OmniDesk Helpdesk')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-950 p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-100">Welcome Back, {{ auth()->user()->name ?? 'Agent' }} 👋</h1>
        <p class="text-sm text-slate-400 mt-1">Here is a quick overview of your helpdesk metrics today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tickets -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <x-icon name="ticket" class="text-[4rem] text-indigo-500" />
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-slate-400 mb-1">Total Tickets</div>
                <div class="text-3xl font-bold text-white">{{ $stats['total_tickets'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <x-icon name="clock" class="text-[4rem] text-amber-500" />
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-slate-400 mb-1">Open Tickets</div>
                <div class="text-3xl font-bold text-amber-400">{{ $stats['open_tickets'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Resolved Tickets -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <x-icon name="circle-check" class="text-[4rem] text-emerald-500" />
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-slate-400 mb-1">Resolved Tickets</div>
                <div class="text-3xl font-bold text-emerald-400">{{ $stats['resolved_tickets'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Total Contacts -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <x-icon name="users" class="text-[4rem] text-cyan-500" />
            </div>
            <div class="relative z-10">
                <div class="text-sm font-semibold text-slate-400 mb-1">Total Contacts</div>
                <div class="text-3xl font-bold text-cyan-400">{{ $stats['total_contacts'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tickets by Channel Chart -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60">
            <h3 class="text-lg font-bold text-slate-100 mb-6">Tickets by Channel</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="channelChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 bg-slate-900/60">
            <h3 class="text-lg font-bold text-slate-100 mb-6">Quick Actions</h3>
            <div class="space-y-4">
                <a href="{{ route('tickets') }}" class="block p-4 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 hover:border-indigo-500 transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition">
                            <x-icon name="messages" class="text-2xl" />
                        </div>
                        <div>
                            <div class="font-bold text-slate-200">Go to Workspace</div>
                            <div class="text-xs text-slate-400">View and reply to active tickets</div>
                        </div>
                        <x-icon name="chevron-right" class="text-xl text-slate-500 ml-auto group-hover:text-indigo-400 transition" />
                    </div>
                </a>

                <a href="{{ route('docs.show') }}" class="block p-4 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 hover:border-emerald-500 transition group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition">
                            <x-icon name="book" class="text-2xl" />
                        </div>
                        <div>
                            <div class="font-bold text-slate-200">Read Documentation</div>
                            <div class="text-xs text-slate-400">Learn how to connect WhatsApp, FB, Telegram</div>
                        </div>
                        <x-icon name="chevron-right" class="text-xl text-slate-500 ml-auto group-hover:text-emerald-400 transition" />
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
        
        // Parse data from PHP
        const chartDataRaw = {!! json_encode($ticketsByChannel ?? []) !!};
        
        const labels = chartDataRaw.map(item => item.channel_name);
        const data = chartDataRaw.map(item => item.count);
        
        // Fallback if no data
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
                        '#6366f1', // Indigo
                        '#10b981', // Emerald
                        '#06b6d4', // Cyan
                        '#f59e0b', // Amber
                        '#f43f5e', // Rose
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
                        position: 'right',
                        labels: {
                            color: '#94a3b8',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
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