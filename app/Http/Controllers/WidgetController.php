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
            'welcome_message' => '',
            'logo_url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=OmniHelp',
            'theme' => 'dark',
            'launcher_icon' => 'chat',
            'require_prechat' => false,
        ], $channel->configuration ?? []);

        $isSecure = request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https';

        return response()->json([
            'channel_id' => $channel->id,
            'channel_name' => $channel->name,
            'is_active' => (bool)$channel->is_active,
            'configuration' => $config,
            'reverb' => [
                'key' => config('reverb.apps.apps.0.key', env('REVERB_APP_KEY', env('VITE_REVERB_APP_KEY', 'omnihelp-key'))),
                'host' => env('VITE_REVERB_HOST') ?: request()->getHost(),
                'port' => (int) (env('VITE_REVERB_PORT') ?: ($isSecure ? 443 : env('REVERB_PORT', 8080))),
                'scheme' => env('VITE_REVERB_SCHEME') ?: ($isSecure ? 'https' : 'http'),
            ],
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
            'phone' => 'nullable|string|max:50',
        ]);

        $channelId = $request->input('channel_id');
        $channel = $channelId ? Channel::find($channelId) : Channel::where('type', 'web_chat')->first();

        if (!$channel) {
            return response()->json(['error' => 'Web chat channel not configured.'], 400);
        }

        $sessionToken = $request->input('session_token') ?: 'vis_' . Str::random(32);
        $name = trim($request->input('name') ?: '');
        $email = strtolower(trim($request->input('email') ?: ''));
        $phone = preg_replace('/[^0-9+]/', '', $request->input('phone') ?: '');

        if (empty($email) && empty($phone) && empty($request->input('session_token'))) {
            return response()->json([
                'error' => 'Either an email address or a phone number must be provided to start a chat session.'
            ], 422);
        }

        // Multi-Anchor Priority Contact Resolver
        $contact = null;

        // 1. Primary Lookup: Match by Email
        if (!empty($email)) {
            $contact = Contact::where('email', $email)->first();
        }

        // 2. Secondary Lookup: Match by Phone (E.164 formatted)
        if (!$contact && !empty($phone)) {
            $contact = Contact::where('phone', $phone)->first();
        }

        // 3. Tertiary Lookup: Match by Widget Session Token
        if (!$contact && !empty($sessionToken)) {
            $contact = Contact::whereJsonContains('external_ids->widget_session', $sessionToken)->first();
        }

        if (!$contact) {
            // Create New Contact Profile
            $contactName = !empty($name) ? $name : 'Guest ' . rand(1000, 9999);
            $contactEmail = !empty($email) ? $email : 'visitor_' . Str::random(8) . '@widget.guest';

            $contact = Contact::create([
                'name' => $contactName,
                'email' => $contactEmail,
                'phone' => !empty($phone) ? $phone : null,
                'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($contactName),
                'notes' => 'Created via Web Live Chat Widget',
                'external_ids' => ['widget_session' => $sessionToken],
            ]);
        } else {
            // Update & merge missing fields into existing Contact profile
            $updates = [];

            // Fill missing phone
            if (!empty($phone) && empty($contact->phone)) {
                $updates['phone'] = $phone;
            }

            // Fill missing/guest placeholder email
            if (!empty($email) && (empty($contact->email) || str_ends_with($contact->email, '@widget.guest'))) {
                $updates['email'] = $email;
            }

            // Upgrade generic Guest name if a real name was provided
            if (!empty($name) && (empty($contact->name) || str_starts_with($contact->name, 'Guest'))) {
                $updates['name'] = $name;
            }

            // Sync session token into external_ids JSON
            $externalIds = $contact->external_ids ?? [];
            if (empty($externalIds['widget_session']) || $externalIds['widget_session'] !== $sessionToken) {
                $externalIds['widget_session'] = $sessionToken;
                $updates['external_ids'] = $externalIds;
            }

            if (!empty($updates)) {
                $contact->update($updates);
            }
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

            broadcast(new \App\Events\TicketCreated($ticket));
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

    /**
     * Submit customer rating & feedback survey, ending the chat session.
     */
    public function submitRating(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $ticket = Ticket::findOrFail($request->input('ticket_id'));
        $rating = (int) $request->input('rating');
        $comment = trim($request->input('comment') ?: '');

        $ticket->update([
            'status' => 'closed',
            'resolved_at' => now(),
            'rating' => $rating,
            'feedback_comment' => $comment,
        ]);

        // Add internal system log message to ticket thread
        $stars = str_repeat('⭐', $rating);
        $noteText = "Customer closed the ticket and rated support {$stars} ({$rating}/5).";
        if ($comment) {
            $noteText .= " Feedback: \"{$comment}\"";
        }

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'system',
            'sender_name' => 'Customer Survey',
            'content' => $noteText,
            'is_internal_note' => true,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'ticket' => $ticket,
        ]);
    }
}
