<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle WhatsApp Webhook Verification (GET) & Incoming Messages (POST)
     */
    public function handleWhatsApp(Request $request)
    {
        // 1. Meta Webhook Verification Challenge (GET)
        if ($request->isMethod('get')) {
            $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'omnidesk_secret');
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // 2. Incoming WhatsApp Message Payload (POST)
        $payload = $request->all();
        Log::info('Incoming WhatsApp Webhook Payload:', $payload);

        $entry = $payload['entry'][0]['changes'][0]['value'] ?? null;
        if ($entry && isset($entry['messages'][0])) {
            $msgData = $entry['messages'][0];
            $senderPhone = $msgData['from'] ?? null;
            $senderName = $entry['contacts'][0]['profile']['name'] ?? $senderPhone;
            $content = $msgData['text']['body'] ?? '[Media Message]';

            if ($senderPhone) {
                $channel = Channel::firstOrCreate(['type' => 'whatsapp'], ['name' => 'WhatsApp Cloud']);
                $contact = Contact::firstOrCreate(['phone' => $senderPhone], ['name' => $senderName, 'channel_id' => $channel->id]);

                $ticket = Ticket::firstOrCreate([
                    'contact_id' => $contact->id,
                    'channel_id' => $channel->id,
                    'status' => 'open',
                ], [
                    'ticket_number' => 'TCK-' . rand(1000, 9999),
                    'subject' => 'WhatsApp Chat from ' . $senderName,
                    'priority' => 'high',
                    'last_activity_at' => now(),
                ]);

                $message = Message::create([
                    'ticket_id' => $ticket->id,
                    'sender_type' => 'customer',
                    'sender_id' => $contact->id,
                    'sender_name' => $senderName,
                    'content' => $content,
                ]);

                broadcast(new MessageSent($message))->toOthers();
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Telegram Bot Incoming Webhook (POST)
     */
    public function handleTelegram(Request $request)
    {
        $payload = $request->all();
        Log::info('Incoming Telegram Webhook Payload:', $payload);

        $msgData = $payload['message'] ?? null;
        if ($msgData) {
            $chatId = (string) $msgData['chat']['id'];
            $senderName = trim(($msgData['from']['first_name'] ?? '') . ' ' . ($msgData['from']['last_weight'] ?? ''));
            if (empty($senderName)) {
                $senderName = $msgData['from']['username'] ?? 'Telegram User';
            }
            $content = $msgData['text'] ?? '[Media Message]';

            $channel = Channel::firstOrCreate(['type' => 'telegram'], ['name' => 'Telegram Bot']);
            $contact = Contact::firstOrCreate(['notes' => 'telegram:' . $chatId], [
                'name' => $senderName,
                'channel_id' => $channel->id,
                'email' => 'tg_' . $chatId . '@telegram.user',
            ]);

            $ticket = Ticket::firstOrCreate([
                'contact_id' => $contact->id,
                'channel_id' => $channel->id,
                'status' => 'open',
            ], [
                'ticket_number' => 'TCK-' . rand(1000, 9999),
                'subject' => 'Telegram Chat from ' . $senderName,
                'priority' => 'medium',
                'last_activity_at' => now(),
            ]);

            $message = Message::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'customer',
                'sender_id' => $contact->id,
                'sender_name' => $senderName,
                'content' => $content,
            ]);

            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Facebook Messenger Webhook Verification (GET) & Messages (POST)
     */
    public function handleFacebook(Request $request)
    {
        if ($request->isMethod('get')) {
            $verifyToken = env('FACEBOOK_VERIFY_TOKEN', 'omnidesk_secret');
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $payload = $request->all();
        Log::info('Incoming Facebook Messenger Webhook:', $payload);

        $entry = $payload['entry'][0]['messaging'][0] ?? null;
        if ($entry && isset($entry['message'])) {
            $psid = (string) $entry['sender']['id'];
            $content = $entry['message']['text'] ?? '[Media Message]';

            $channel = Channel::firstOrCreate(['type' => 'facebook'], ['name' => 'Facebook Messenger']);
            $contact = Contact::firstOrCreate(['notes' => 'facebook:' . $psid], [
                'name' => 'FB User (' . substr($psid, -4) . ')',
                'channel_id' => $channel->id,
                'email' => 'fb_' . $psid . '@facebook.user',
            ]);

            $ticket = Ticket::firstOrCreate([
                'contact_id' => $contact->id,
                'channel_id' => $channel->id,
                'status' => 'open',
            ], [
                'ticket_number' => 'TCK-' . rand(1000, 9999),
                'subject' => 'Facebook Chat from ' . $contact->name,
                'priority' => 'medium',
                'last_activity_at' => now(),
            ]);

            $message = Message::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'customer',
                'sender_id' => $contact->id,
                'sender_name' => $contact->name,
                'content' => $content,
            ]);

            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }
}
