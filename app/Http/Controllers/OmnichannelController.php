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
        $tickets = Ticket::with(['channel', 'contact', 'latestMessage', 'assignedAgent'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $channels = Channel::all();
        $agents = User::all();
        $cannedResponses = CannedResponse::all();

        return view('dashboard', compact('tickets', 'channels', 'agents', 'cannedResponses'));
    }

    /**
     * Fetch Messages for a specific ticket
     */
    public function getMessages(Ticket $ticket)
    {
        $ticket->load(['channel', 'contact', 'assignedAgent']);
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

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'agent',
            'sender_id' => auth()->id() ?? 1,
            'sender_name' => auth()->user()?->name ?? 'Agent Sarah',
            'content' => $request->input('content'),
            'is_internal_note' => $request->boolean('is_internal_note', false),
        ]);

        $ticket->update([
            'last_activity_at' => now(),
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['success' => true, 'message' => $message]);
    }
}
