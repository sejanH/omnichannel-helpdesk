<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    protected string $pageAccessToken;

    public function __construct()
    {
        $config = \App\Models\Channel::getCachedConfig('facebook');
        $this->pageAccessToken = $config['page_access_token'] ?? env('FACEBOOK_PAGE_TOKEN', '');
    }

    /**
     * Send outgoing Facebook Messenger message via Meta Graph API
     */
    public function sendMessage(string $recipientPsid, string $messageText): ?string
    {
        if (empty($this->pageAccessToken)) {
            Log::warning("Facebook Page Access Token missing. Skipping outbound call for PSID: {$recipientPsid}");
            return null;
        }

        $url = "https://graph.facebook.com/v19.0/me/messages?access_token={$this->pageAccessToken}";

        $response = Http::post($url, [
            'recipient' => ['id' => $recipientPsid],
            'message' => ['text' => $messageText],
        ]);

        if ($response->successful()) {
            $msgId = $response->json('message_id');
            return $msgId ? (string)$msgId : 'fb_mid_' . uniqid();
        }

        Log::error('Facebook Messenger API send message failed:', ['body' => $response->body()]);
        return null;
    }
}
