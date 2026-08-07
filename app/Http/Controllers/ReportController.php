<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display Agent Performance & CSAT Analytics Dashboard (Admin Only)
     */
    public function index(Request $request)
    {
        // Enforce Admin-only access
        if (!auth()->user()->hasPermissionTo('view-reports')) {
            return redirect()->route('tickets')->with('warning', 'Access restricted to administrators.');
        }

        // Overall CSAT Rating Analytics
        $avgCsat = Ticket::whereNotNull('rating')->avg('rating') ?: 0;
        $totalRatingsCount = Ticket::whereNotNull('rating')->count();

        // Rating distribution breakdown (5 star, 4 star, etc.)
        $ratingDistribution = [
            5 => Ticket::where('rating', 5)->count(),
            4 => Ticket::where('rating', 4)->count(),
            3 => Ticket::where('rating', 3)->count(),
            2 => Ticket::where('rating', 2)->count(),
            1 => Ticket::where('rating', 1)->count(),
        ];

        // Overall Ticket Metrics
        $totalTickets = Ticket::count();
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])->count();
        $openTickets = Ticket::where('status', 'open')->count();
        $inProgressTickets = Ticket::where('status', 'in_progress')->count();
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0;

        // Per-Agent Performance Breakdown Table
        $agents = User::all();
        $agentStats = [];

        foreach ($agents as $agent) {
            $assignedCount = Ticket::where('assigned_agent_id', $agent->id)->count();
            $agentResolvedCount = Ticket::where('assigned_agent_id', $agent->id)->whereIn('status', ['resolved', 'closed'])->count();
            $agentAvgRating = Ticket::where('assigned_agent_id', $agent->id)->whereNotNull('rating')->avg('rating') ?: 0;
            $agentRatingsCount = Ticket::where('assigned_agent_id', $agent->id)->whereNotNull('rating')->count();
            $agentResolutionRate = $assignedCount > 0 ? round(($agentResolvedCount / $assignedCount) * 100, 1) : 0;

            // Total messages sent by this agent
            $messagesCount = Message::where('sender_type', 'agent')->where('sender_id', $agent->id)->count();

            $agentStats[] = (object) [
                'id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'role' => $agent->role,
                'status' => $agent->status,
                'assigned_tickets' => $assignedCount,
                'resolved_tickets' => $agentResolvedCount,
                'resolution_rate' => $agentResolutionRate,
                'avg_rating' => round($agentAvgRating, 1),
                'ratings_count' => $agentRatingsCount,
                'messages_sent' => $messagesCount,
            ];
        }

        // Recent Rated Tickets with Customer Comments
        $recentFeedback = Ticket::with(['contact', 'assignedAgent', 'channel'])
            ->whereNotNull('rating')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'avgCsat',
            'totalRatingsCount',
            'ratingDistribution',
            'totalTickets',
            'resolvedTickets',
            'openTickets',
            'inProgressTickets',
            'resolutionRate',
            'agentStats',
            'recentFeedback'
        ));
    }
}
