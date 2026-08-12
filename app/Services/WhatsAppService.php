<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;

    public function __construct()
    {
        $config = \App\Models\Channel::getCachedConfig('whatsapp');
        $this->token = $config['token'] ?? env('WHATSAPP_TOKEN', '');
        $this->phoneNumberId = $config['phone_number_id'] ?? env('WHATSAPP_PHONE_ID', '');
    }

    /**
     * Send outgoing WhatsApp message via Meta Cloud API
     */
    public function sendMessage(string $toPhone, string $messageText): ?string
    {
        if (empty($this->token) || empty($this->phoneNumberId)) {
            Log::warning("WhatsApp API credentials missing. Skipping outbound API call for recipient: {$toPhone}");
            return null;
        }

        $url = "https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages";

        $response = Http::withToken($this->token)->post($url, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $messageText,
            ],
        ]);

        if ($response->successful()) {
            $msgId = $response->json('messages.0.id');
            return $msgId ? (string)$msgId : 'wamid_sent_' . uniqid();
        }

        Log::error('WhatsApp API send message failed:', ['body' => $response->body()]);
        return null;
    }
}
