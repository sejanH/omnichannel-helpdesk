@extends('layouts.app')

@section('title', 'Reports & Performance Analytics — OmniHelp')

@section('content')
<div class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-8 bg-slate-950 text-slate-100">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Agent Performance & CSAT Reports</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Admin Restricted</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Monitor agent resolution metrics, customer satisfaction (CSAT) ratings, and recent feedback.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('agents.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-slate-300 rounded-xl text-xs font-semibold border border-slate-800 transition flex items-center gap-2">
                <x-icon name="users" class="text-sm text-indigo-400" />
                <span>Manage Agents</span>
            </a>
        </div>
    </div>

    <!-- Top KPI Performance Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Average CSAT Score -->
        <div class="glass-panel p-5 rounded-2xl border border-amber-500/30 bg-amber-950/10 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-amber-300">Average CSAT Rating</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">⭐</div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white">{{ number_format($avgCsat, 1) }}</span>
                <span class="text-xs text-slate-400">/ 5.0</span>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                <span>Based on {{ $totalRatingsCount }} customer reviews</span>
                <span class="text-amber-400 font-semibold">{{ $totalRatingsCount > 0 ? 'Active' : 'No Reviews' }}</span>
            </div>
        </div>

        <!-- Overall Resolution Rate -->
        <div class="glass-panel p-5 rounded-2xl border border-emerald-500/30 bg-emerald-950/10 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-emerald-300">Resolution Rate</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <x-icon name="check-circle" class="text-lg" />
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white">{{ $resolutionRate }}%</span>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                <span>{{ $resolvedTickets }} of {{ $totalTickets }} tickets resolved</span>
                <span class="text-emerald-400 font-semibold">High Efficiency</span>
            </div>
        </div>

        <!-- Total Tickets Handled -->
        <div class="glass-panel p-5 rounded-2xl border border-indigo-500/30 bg-indigo-950/10 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-indigo-300">Total Volume</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <x-icon name="ticket" class="text-lg" />
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white">{{ $totalTickets }}</span>
                <span class="text-xs text-slate-400">Tickets</span>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                <span>{{ $openTickets }} Open • {{ $inProgressTickets }} In Progress</span>
                <span class="text-indigo-400 font-semibold">Live Feed</span>
            </div>
        </div>

        <!-- Active Agents Roster -->
        <div class="glass-panel p-5 rounded-2xl border border-cyan-500/30 bg-cyan-950/10 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-cyan-300">Support Team</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <x-icon name="users" class="text-lg" />
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white">{{ count($agentStats) }}</span>
                <span class="text-xs text-slate-400">Agents</span>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-1">
                <span>Monitoring Active Roster</span>
                <span class="text-cyan-400 font-semibold">Online</span>
            </div>
        </div>
    </div>

    <!-- Rating Distribution & CSAT Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Star Rating Breakdown Card -->
        <div class="glass-panel p-6 rounded-2xl border border-slate-800 space-y-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>⭐</span>
                    <span>CSAT Rating Distribution</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Breakdown of 1 to 5 star ratings submitted by customers.</p>
            </div>

            <div class="space-y-3 pt-2">
                @for ($star = 5; $star >= 1; $star--)
                    @php
                        $count = $ratingDistribution[$star] ?? 0;
                        $pct = $totalRatingsCount > 0 ? round(($count / $totalRatingsCount) * 100) : 0;
                    @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs font-medium">
                            <span class="text-slate-300 flex items-center gap-1 font-mono">
                                <span>{{ $star }}</span>
                                <span class="text-amber-400 text-xs">⭐</span>
                            </span>
                            <span class="text-slate-400">{{ $count }} ratings ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-900 rounded-full overflow-hidden border border-slate-800">
                            <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Agent Leaderboard Table (Occupies 2 cols) -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-2xl border border-slate-800 space-y-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <x-icon name="trophy" class="text-indigo-400 text-lg" />
                    <span>Agent Performance Leaderboard</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Individual performance metrics and customer satisfaction ratings per support agent.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-800 font-mono text-[11px] uppercase tracking-wider">
                            <th class="pb-3 font-semibold">Agent</th>
                            <th class="pb-3 font-semibold text-center">Assigned</th>
                            <th class="pb-3 font-semibold text-center">Resolved</th>
                            <th class="pb-3 font-semibold text-center">Rate</th>
                            <th class="pb-3 font-semibold text-center">Avg CSAT</th>
                            <th class="pb-3 font-semibold text-center">Messages</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($agentStats as $agent)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($agent->name) }}" class="w-8 h-8 rounded-full border border-indigo-500/30 shrink-0" alt="Avatar">
                                        <div>
                                            <div class="font-bold text-white text-xs">{{ $agent->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $agent->email }} • <span class="uppercase text-indigo-400 font-semibold">{{ $agent->role }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center font-semibold text-slate-300">{{ $agent->assigned_tickets }}</td>
                                <td class="py-3 text-center font-semibold text-emerald-400">{{ $agent->resolved_tickets }}</td>
                                <td class="py-3 text-center font-bold text-indigo-300">{{ $agent->resolution_rate }}%</td>
                                <td class="py-3 text-center">
                                    @if($agent->ratings_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <span>⭐</span>
                                            <span>{{ number_format($agent->avg_rating, 1) }}</span>
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-500">Unrated</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-slate-400 font-mono">{{ $agent->messages_sent }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-500">No agent performance records available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Customer Feedback Feed -->
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 space-y-4">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <x-icon name="message-square" class="text-emerald-400 text-lg" />
                <span>Recent Customer Feedback & Reviews</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">Live customer rating reviews and feedback comments submitted from chat widgets.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            @forelse($recentFeedback as $fb)
                <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-white">{{ $fb->contact->name ?? 'Customer' }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">#{{ $fb->ticket_number }}</span>
                        </div>
                        <div class="flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            <span>⭐</span>
                            <span>{{ $fb->rating }}/5</span>
                        </div>
                    </div>

                    @if($fb->feedback_comment)
                        <p class="text-xs text-slate-300 italic bg-slate-950 p-2.5 rounded-lg border border-slate-800/60">
                            "{{ $fb->feedback_comment }}"
                        </p>
                    @else
                        <p class="text-[11px] text-slate-500 italic">No written comment provided.</p>
                    @endif

                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-800/40">
                        <span>Agent: <strong class="text-indigo-300">{{ $fb->assignedAgent->name ?? 'Unassigned' }}</strong></span>
                        <span>{{ $fb->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-8 text-center text-slate-500 text-xs">
                    No customer ratings or feedback reviews have been submitted yet.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
