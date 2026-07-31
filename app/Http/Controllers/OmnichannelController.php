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
    public function tickets(?Ticket $ticket = null)
    {
        $tickets = Ticket::with(['channel', 'contact', 'latestMessage', 'assignedAgent'])
            ->withCount(['messages as unread_messages_count' => function ($query) {
                $query->whereNull('read_at')->where('sender_type', 'customer');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        $channels = Channel::all();
        $agents = User::all();
        $cannedResponses = CannedResponse::all();

        $activeTicket = $ticket;

        return view('tickets', compact('tickets', 'channels', 'agents', 'cannedResponses', 'activeTicket'));
    }

    /**
     * Fetch Messages for a specific ticket
     */
    public function getMessages(Ticket $ticket)
    {
        $ticket->load(['channel', 'contact', 'assignedAgent']);
        
        // Mark unread customer messages as read
        $ticket->messages()->whereNull('read_at')->where('sender_type', 'customer')->update(['read_at' => now()]);

        $messages = $ticket->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'ticket' => $ticket,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message or internal note
     */
    public function sendMessage(Request $request, Ticket $ticket)
    {
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

            if ($contact && in_array($channelType, ['whatsapp', 'telegram', 'facebook'])) {
                \App\Jobs\SendExternalMessageJob::dispatch($message, $channelType, $contact);
            }
        }

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Resolve a ticket
     */
    public function resolveTicket(Ticket $ticket)
    {
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'system',
            'sender_name' => 'System',
            'content' => 'Ticket marked as resolved by ' . (auth()->user()?->name ?? 'Agent'),
            'is_internal_note' => true,
        ]);
        
        broadcast(new MessageSent($message))->toOthers();

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

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'ticket' => $ticket->load(['assignedAgent']),
            'message' => $message
        ]);
    }
}
