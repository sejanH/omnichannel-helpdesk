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
     * Handle WhatsApp Webhook Verification (GET), Incoming Messages & Sent Echoes/Statuses (POST)
     */
    public function handleWhatsApp(Request $request)
    {
        // 1. Meta Webhook Verification Challenge (GET)
        if ($request->isMethod('get')) {
            $config = Channel::getCachedConfig('whatsapp');
            $verifyToken = $config['verify_token'] ?? env('WHATSAPP_VERIFY_TOKEN', 'omnihelp_secret');
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Forbidden'], 403);
        }

        // 2. Incoming WhatsApp Message or Status Update Payload (POST)
        $payload = $request->all();
        Log::info('Incoming WhatsApp Webhook Payload:', $payload);

        $value = $payload['entry'][0]['changes'][0]['value'] ?? null;

        if ($value) {
            // Process Status Receipts (delivered, read)
            if (isset($value['statuses'][0])) {
                $statusData = $value['statuses'][0];
                $msgId = (string) ($statusData['id'] ?? '');
                $status = $statusData['status'] ?? '';

                if ($msgId && $status) {
                    $existingMsg = Message::where('channel_message_id', $msgId)->first();
                    if ($existingMsg) {
                        if ($status === 'delivered' && !$existingMsg->delivered_at) {
                            $existingMsg->update(['delivered_at' => now()]);
                        } elseif ($status === 'read') {
                            $existingMsg->update([
                                'delivered_at' => $existingMsg->delivered_at ?? now(),
                                'read_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // Process Messages (Incoming or Sent Echoes)
            if (isset($value['messages'][0])) {
                $msgData = $value['messages'][0];
                $channelMsgId = (string) ($msgData['id'] ?? '');
                $isEcho = (bool) ($msgData['is_echo'] ?? false);
                $senderPhone = (string) ($msgData['from'] ?? '');
                $senderName = $value['contacts'][0]['profile']['name'] ?? $senderPhone;
                $content = $msgData['text']['body'] ?? ($msgData['caption'] ?? '[Media / Attachment]');

                if ($senderPhone) {
                    // Check deduplication
                    $existingMsg = $channelMsgId ? Message::where('channel_message_id', $channelMsgId)->first() : null;

                    if (!$existingMsg) {
                        $channel = Channel::firstOrCreate(['type' => 'whatsapp'], ['name' => 'WhatsApp Cloud', 'slug' => 'whatsapp-cloud']);
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

                        $ticket->update(['last_activity_at' => now()]);

                        $senderType = $isEcho ? 'agent' : 'customer';
                        $displayName = $isEcho ? 'Agent (WhatsApp)' : $senderName;

                        $message = Message::create([
                            'ticket_id' => $ticket->id,
                            'sender_type' => $senderType,
                            'sender_id' => $isEcho ? null : $contact->id,
                            'sender_name' => $displayName,
                            'content' => $content,
                            'channel_message_id' => $channelMsgId,
                            'delivered_at' => $isEcho ? now() : null,
                        ]);

                        broadcast(new MessageSent($message))->toOthers();
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Telegram Bot Webhook (POST) - Stores incoming & sent messages
     */
    public function handleTelegram(Request $request)
    {
        $payload = $request->all();
        Log::info('Incoming Telegram Webhook Payload:', $payload);

        $msgData = $payload['message'] ?? ($payload['edited_message'] ?? null);

        if ($msgData) {
            $chatId = (string) ($msgData['chat']['id'] ?? '');
            $channelMsgId = (string) ($msgData['message_id'] ?? '');
            $senderName = trim(($msgData['from']['first_name'] ?? '') . ' ' . ($msgData['from']['last_name'] ?? ''));
            if (empty($senderName)) {
                $senderName = $msgData['from']['username'] ?? 'Telegram User';
            }
            $content = $msgData['text'] ?? '[Media Message]';

            if (!empty($chatId)) {
                $existingMsg = $channelMsgId ? Message::where('channel_message_id', $channelMsgId)->first() : null;

                if (!$existingMsg) {
                    $channel = Channel::firstOrCreate(['type' => 'telegram'], ['name' => 'Telegram Bot', 'slug' => 'telegram-bot']);
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

                    $ticket->update(['last_activity_at' => now()]);

                    $message = Message::create([
                        'ticket_id' => $ticket->id,
                        'sender_type' => 'customer',
                        'sender_id' => $contact->id,
                        'sender_name' => $senderName,
                        'content' => $content,
                        'channel_message_id' => $channelMsgId,
                    ]);

                    if ($ticket->wasRecentlyCreated) {
                        broadcast(new \App\Events\TicketCreated($ticket));
                    }

                    broadcast(new MessageSent($message))->toOthers();
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Facebook Messenger Webhook (GET/POST) - Inbound & Outbound Echoes
     */
    public function handleFacebook(Request $request)
    {
        if ($request->isMethod('get')) {
            $config = Channel::getCachedConfig('facebook');
            $verifyToken = $config['verify_token'] ?? env('FACEBOOK_VERIFY_TOKEN', 'omnihelp_secret');
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

        if ($entry) {
            // Process Receipts
            if (isset($entry['delivery'])) {
                $mids = $entry['delivery']['mids'] ?? [];
                foreach ($mids as $mid) {
                    Message::where('channel_message_id', (string)$mid)->update(['delivered_at' => now()]);
                }
            }
            if (isset($entry['read'])) {
                $watermark = $entry['read']['watermark'] ?? null;
                if ($watermark) {
                    Message::where('created_at', '<=', date('Y-m-d H:i:s', (int)($watermark / 1000)))->update(['read_at' => now()]);
                }
            }

            // Process Messages & Sent Echoes
            if (isset($entry['message'])) {
                $senderId = (string) ($entry['sender']['id'] ?? '');
                $recipientId = (string) ($entry['recipient']['id'] ?? '');
                $isEcho = (bool) ($entry['message']['is_echo'] ?? false);
                $channelMsgId = (string) ($entry['message']['mid'] ?? '');
                $content = $entry['message']['text'] ?? '[Media Message]';

                $targetPsid = $isEcho ? $recipientId : $senderId;

                if ($targetPsid) {
                    $existingMsg = $channelMsgId ? Message::where('channel_message_id', $channelMsgId)->first() : null;

                    if (!$existingMsg) {
                        $channel = Channel::firstOrCreate(['type' => 'facebook'], ['name' => 'Facebook Messenger', 'slug' => 'facebook-messenger']);
                        $contact = Contact::firstOrCreate(['notes' => 'facebook:' . $targetPsid], [
                            'name' => 'FB User (' . substr($targetPsid, -4) . ')',
                            'channel_id' => $channel->id,
                            'email' => 'fb_' . $targetPsid . '@facebook.user',
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

                        $ticket->update(['last_activity_at' => now()]);

                        $senderType = $isEcho ? 'agent' : 'customer';
                        $senderName = $isEcho ? 'Agent (Facebook Inbox)' : $contact->name;

                        $message = Message::create([
                            'ticket_id' => $ticket->id,
                            'sender_type' => $senderType,
                            'sender_id' => $isEcho ? null : $contact->id,
                            'sender_name' => $senderName,
                            'content' => $content,
                            'channel_message_id' => $channelMsgId,
                            'delivered_at' => $isEcho ? now() : null,
                        ]);

                        broadcast(new MessageSent($message))->toOthers();
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Instagram Direct Webhook (GET/POST) - Inbound & Outbound Echoes
     */
    public function handleInstagram(Request $request)
    {
        if ($request->isMethod('get')) {
            $config = Channel::getCachedConfig('instagram');
            $verifyToken = $config['verify_token'] ?? env('INSTAGRAM_VERIFY_TOKEN', 'omnihelp_secret');
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $payload = $request->all();
        Log::info('Incoming Instagram Direct Webhook:', $payload);

        $entry = $payload['entry'][0]['messaging'][0] ?? null;

        if ($entry) {
            // Process Receipts
            if (isset($entry['delivery'])) {
                $mids = $entry['delivery']['mids'] ?? [];
                foreach ($mids as $mid) {
                    Message::where('channel_message_id', (string)$mid)->update(['delivered_at' => now()]);
                }
            }

            // Process Messages & Sent Echoes
            if (isset($entry['message'])) {
                $senderId = (string) ($entry['sender']['id'] ?? '');
                $recipientId = (string) ($entry['recipient']['id'] ?? '');
                $isEcho = (bool) ($entry['message']['is_echo'] ?? false);
                $channelMsgId = (string) ($entry['message']['mid'] ?? '');
                $content = $entry['message']['text'] ?? '[Media Message]';

                $targetIgsid = $isEcho ? $recipientId : $senderId;

                if ($targetIgsid) {
                    $existingMsg = $channelMsgId ? Message::where('channel_message_id', $channelMsgId)->first() : null;

                    if (!$existingMsg) {
                        $channel = Channel::firstOrCreate(['type' => 'instagram'], ['name' => 'Instagram Direct', 'slug' => 'instagram-direct']);
                        $contact = Contact::firstOrCreate(['notes' => 'instagram:' . $targetIgsid], [
                            'name' => 'IG User (' . substr($targetIgsid, -4) . ')',
                            'channel_id' => $channel->id,
                            'email' => 'ig_' . $targetIgsid . '@instagram.user',
                        ]);

                        $ticket = Ticket::firstOrCreate([
                            'contact_id' => $contact->id,
                            'channel_id' => $channel->id,
                            'status' => 'open',
                        ], [
                            'ticket_number' => 'TCK-' . rand(1000, 9999),
                            'subject' => 'Instagram DM from ' . $contact->name,
                            'priority' => 'medium',
                            'last_activity_at' => now(),
                        ]);

                        $ticket->update(['last_activity_at' => now()]);

                        $senderType = $isEcho ? 'agent' : 'customer';
                        $senderName = $isEcho ? 'Agent (Instagram App)' : $contact->name;

                        $message = Message::create([
                            'ticket_id' => $ticket->id,
                            'sender_type' => $senderType,
                            'sender_id' => $isEcho ? null : $contact->id,
                            'sender_name' => $senderName,
                            'content' => $content,
                            'channel_message_id' => $channelMsgId,
                            'delivered_at' => $isEcho ? now() : null,
                        ]);

                        broadcast(new MessageSent($message))->toOthers();
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
