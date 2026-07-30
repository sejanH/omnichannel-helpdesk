<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    /**
     * Return configuration for the live chat widget.
     */
    public function getConfig(Request $request)
    {
        $channelId = $request->query('channel_id');
        
        $channel = null;
        if ($channelId) {
            $channel = Channel::find($channelId);
        }
        
        if (!$channel) {
            $channel = Channel::where('type', 'web_chat')->first();
        }

        if (!$channel) {
            return response()->json([
                'error' => 'No active web chat channel found.'
            ], 444);
        }

        $config = array_merge([
            'widget_color' => '#6366f1',
            'position' => 'bottom-right',
            'title' => 'Customer Support',
            'subtitle' => 'We typically reply in under 5 minutes',
            'welcome_message' => '👋 Hello! How can our support team help you today?',
            'logo_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniDesk',
            'theme' => 'dark',
            'launcher_icon' => 'chat',
            'require_prechat' => false,
        ], $channel->configuration ?? []);

        return response()->json([
            'channel_id' => $channel->id,
            'channel_name' => $channel->name,
            'is_active' => (bool)$channel->is_active,
            'configuration' => $config,
        ]);
    }

    /**
     * Update widget configuration from Dashboard settings.
     */
    public function updateConfig(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'widget_color' => 'required|string|max:30',
            'position' => 'required|string|in:bottom-right,bottom-left,top-right,top-left',
            'title' => 'required|string|max:100',
            'subtitle' => 'nullable|string|max:150',
            'welcome_message' => 'nullable|string|max:500',
            'logo_url' => 'nullable|string|max:500',
            'theme' => 'required|string|in:dark,light,auto',
            'launcher_icon' => 'required|string|in:chat,sparkles,help,message',
            'require_prechat' => 'nullable|boolean',
        ]);

        $existingConfig = $channel->configuration ?? [];
        $mergedConfig = array_merge($existingConfig, [
            'widget_color' => $validated['widget_color'],
            'position' => $validated['position'],
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? '',
            'welcome_message' => $validated['welcome_message'] ?? '',
            'logo_url' => $validated['logo_url'] ?? '',
            'theme' => $validated['theme'],
            'launcher_icon' => $validated['launcher_icon'],
            'require_prechat' => (bool)($validated['require_prechat'] ?? false),
        ]);

        $channel->update([
            'configuration' => $mergedConfig,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Widget configuration updated successfully!',
            'channel' => $channel->fresh(),
        ]);
    }

    /**
     * Initialize widget session for visitor.
     */
    public function initSession(Request $request)
    {
        $request->validate([
            'channel_id' => 'nullable|exists:channels,id',
            'session_token' => 'nullable|string',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
        ]);

        $channelId = $request->input('channel_id');
        $channel = $channelId ? Channel::find($channelId) : Channel::where('type', 'web_chat')->first();

        if (!$channel) {
            return response()->json(['error' => 'Web chat channel not configured.'], 400);
        }

        $sessionToken = $request->input('session_token') ?: 'vis_' . Str::random(32);
        $name = $request->input('name') ?: 'Guest ' . rand(1000, 9999);
        $email = $request->input('email');

        // Find or create Contact
        $contact = null;
        if ($email) {
            $contact = Contact::where('email', $email)->first();
        }

        if (!$contact) {
            $contact = Contact::create([
                'name' => $name,
                'email' => $email ?: 'visitor_' . Str::random(8) . '@widget.guest',
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($name),
                'notes' => 'Created via Live Chat Widget',
                'external_ids' => ['widget_session' => $sessionToken],
            ]);
        }

        // Find active open/in_progress ticket for this contact & channel
        $ticket = Ticket::where('contact_id', $contact->id)
            ->where('channel_id', $channel->id)
            ->whereIn('status', ['open', 'in_progress', 'pending'])
            ->latest()
            ->first();

        if (!$ticket) {
            $ticketNumber = 'TCK-' . rand(2000, 9999);
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'subject' => 'Web Live Chat Session (' . $name . ')',
                'status' => 'open',
                'priority' => 'medium',
                'channel_id' => $channel->id,
                'contact_id' => $contact->id,
                'last_activity_at' => now(),
            ]);

            // Add welcome message if new ticket
            $welcomeMsg = $channel->configuration['welcome_message'] ?? '👋 Hello! How can our support team help you today?';
            if (!empty($welcomeMsg)) {
                Message::create([
                    'ticket_id' => $ticket->id,
                    'sender_type' => 'system',
                    'sender_name' => $channel->configuration['title'] ?? 'Support Team',
                    'content' => $welcomeMsg,
                    'created_at' => now(),
                ]);
            }
        }

        $messages = Message::where('ticket_id', $ticket->id)
            ->where('is_internal_note', false)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'session_token' => $sessionToken,
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'contact' => $contact,
            'messages' => $messages,
        ]);
    }

    /**
     * Get messages for an active ticket.
     */
    public function getMessages(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        if (!$ticketId) {
            return response()->json(['messages' => []]);
        }

        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            return response()->json(['messages' => []], 404);
        }

        $messages = Message::where('ticket_id', $ticket->id)
            ->where('is_internal_note', false)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'ticket_id' => $ticket->id,
            'status' => $ticket->status,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message from floating widget.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'content' => 'required|string|max:2000',
            'sender_name' => 'nullable|string|max:100',
        ]);

        $ticket = Ticket::findOrFail($request->input('ticket_id'));
        $senderName = $request->input('sender_name') ?: ($ticket->contact ? $ticket->contact->name : 'Visitor');

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'customer',
            'sender_id' => $ticket->contact_id,
            'sender_name' => $senderName,
            'content' => $request->input('content'),
            'is_internal_note' => false,
        ]);

        $ticket->update([
            'last_activity_at' => now(),
            'status' => $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'open' : $ticket->status,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
