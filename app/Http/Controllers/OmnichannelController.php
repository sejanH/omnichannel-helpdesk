<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\CannedResponse;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class OmnichannelController extends Controller
{
    /**
     * Agent Workspace Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
            'total_contacts' => Contact::count(),
            'trashed_tickets' => auth()->check() && auth()->user()->hasPermissionTo('delete-tickets') ? Ticket::onlyTrashed()->count() : 0,
        ];

        // Chart data: tickets by channel
        $ticketsByChannel = Ticket::selectRaw('channels.name as channel_name, COUNT(tickets.id) as count')
            ->join('channels', 'tickets.channel_id', '=', 'channels.id')
            ->groupBy('channels.name')
            ->get();

        return view('dashboard', compact('stats', 'ticketsByChannel'));
    }

    /**
     * Agent Workspace Tickets
     */
    public function tickets(Request $request, ?Ticket $ticket = null)
    {
        $filter = $request->query('filter');

        $query = Ticket::with(['channel', 'contact', 'latestMessage', 'assignedAgent'])
            ->withCount(['messages as unread_messages_count' => function ($q) {
                $q->whereNull('read_at')->where('sender_type', 'customer');
            }]);

        if ($filter === 'trash') {
            if (!auth()->check() || !auth()->user()->hasPermissionTo('delete-tickets')) {
                abort(403, 'Unauthorized. You do not have permission to access the ticket trash bin.');
            }
            $query->onlyTrashed();
        }

        $tickets = $query->orderBy('updated_at', 'desc')->get();

        $channels = Channel::all();
        $agents = User::all();
        $cannedResponses = CannedResponse::all();
        $trashedCount = auth()->check() && auth()->user()->hasPermissionTo('delete-tickets') ? Ticket::onlyTrashed()->count() : 0;

        $activeTicket = $ticket;

        return view('tickets', compact('tickets', 'channels', 'agents', 'cannedResponses', 'activeTicket', 'trashedCount'));
    }

    /**
     * Fetch Messages for a specific ticket
     */
    public function getMessages($id)
    {
        $ticket = Ticket::withTrashed()->with(['channel', 'contact', 'assignedAgent'])->findOrFail($id);
        
        // Mark unread customer messages as read if ticket is active
        if (!$ticket->trashed()) {
            $ticket->messages()->whereNull('read_at')->where('sender_type', 'customer')->update(['read_at' => now()]);
        }

        $messages = $ticket->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'ticket' => $ticket,
            'sla_status' => $ticket->sla_status,
            'is_trashed' => $ticket->trashed(),
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message or internal note
     */
    public function sendMessage(Request $request, Ticket $ticket)
    {
        if ($ticket->trashed()) {
            return response()->json(['error' => 'Cannot send messages on trashed tickets.'], 422);
        }

        $request->validate([
            'content' => 'required|string',
            'is_internal_note' => 'boolean',
        ]);

        $isInternal = $request->boolean('is_internal_note', false);

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'agent',
            'sender_id' => auth()->id() ?? 1,
            'sender_name' => auth()->user()?->name ?? 'Agent Sarah',
            'content' => $request->input('content'),
            'is_internal_note' => $isInternal,
        ]);

        $ticket->update([
            'last_activity_at' => now(),
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        // Dispatch outbound external API message if not an internal note
        if (!$isInternal) {
            $channelType = strtolower($ticket->channel->type ?? '');
            $contact = $ticket->contact;

            if ($contact && in_array($channelType, ['whatsapp', 'telegram', 'facebook', 'instagram'])) {
                \App\Jobs\SendExternalMessageJob::dispatch($message, $channelType, $contact);
            }
        }

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Resolve or Re-open a ticket
     */
    public function resolveTicket(Ticket $ticket)
    {
        if ($ticket->trashed()) {
            return response()->json(['error' => 'Cannot resolve trashed tickets.'], 422);
        }

        $newStatus = in_array($ticket->status, ['resolved', 'closed']) ? 'open' : 'resolved';

        $ticket->update([
            'status' => $newStatus,
            'resolved_at' => $newStatus === 'resolved' ? now() : null,
        ]);

        $actionText = $newStatus === 'resolved' ? 'marked as resolved' : 're-opened';

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'system',
            'sender_name' => 'System',
            'content' => "Ticket {$actionText} by " . (auth()->user()?->name ?? 'Agent'),
            'is_internal_note' => true,
        ]);
        
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'ticket' => $ticket, 'message' => $message]);
    }

    /**
     * Assign ticket to an agent
     */
    public function assignAgent(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_agent_id' => 'nullable|exists:users,id',
        ]);

        $agentId = $request->input('assigned_agent_id');
        $agent = $agentId ? User::find($agentId) : null;

        $ticket->update([
            'assigned_agent_id' => $agentId,
        ]);

        $assignedName = $agent ? $agent->name : 'Unassigned';
        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'system',
            'sender_name' => 'System',
            'content' => 'Ticket assigned to ' . $assignedName . ' by ' . (auth()->user()?->name ?? 'Admin'),
            'is_internal_note' => true,
        ]);

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket->load(['assignedAgent']),
            'message' => $message
        ]);
    }

    /**
     * Update ticket category, priority, and tags
     */
    public function updateTicketMetadata(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        if (isset($validated['category'])) {
            $ticket->category = $validated['category'];
        }

        if (isset($validated['priority'])) {
            $ticket->priority = $validated['priority'];
            $policy = \App\Models\SlaPolicy::where('priority', $validated['priority'])->first();
            if ($policy) {
                $ticket->due_at = now()->addMinutes($policy->resolution_target_minutes);
            }
        }

        if (isset($validated['tags'])) {
            $ticket->tags = array_values(array_unique($validated['tags']));
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'sla_status' => $ticket->sla_status,
        ]);
    }

    /**
     * Move Ticket to Trash Bin
     */
    public function destroyTicket(Ticket $ticket)
    {
        if (!auth()->check() || !auth()->user()->hasPermissionTo('delete-tickets')) {
            abort(403, 'Unauthorized. You do not have permission to delete tickets.');
        }

        $ticket->delete();

        return response()->json(['success' => true, 'message' => 'Ticket moved to trash bin successfully.']);
    }

    /**
     * Restore Ticket from Trash Bin
     */
    public function restoreTicket($id)
    {
        if (!auth()->check() || !auth()->user()->hasPermissionTo('delete-tickets')) {
            abort(403, 'Unauthorized. You do not have permission to restore trashed tickets.');
        }

        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $ticket->restore();

        return response()->json(['success' => true, 'message' => 'Ticket restored successfully.']);
    }

    /**
     * Permanently Delete Ticket
     */
    public function forceDeleteTicket($id)
    {
        if (!auth()->check() || !auth()->user()->hasPermissionTo('delete-tickets')) {
            abort(403, 'Unauthorized. You do not have permission to permanently delete tickets.');
        }

        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $ticket->forceDelete();

        return response()->json(['success' => true, 'message' => 'Ticket permanently deleted.']);
    }
}
