<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramService
{
    protected string $pageAccessToken;

    public function __construct()
    {
        $config = \App\Models\Channel::getCachedConfig('instagram');
        $this->pageAccessToken = $config['page_access_token'] ?? env('INSTAGRAM_PAGE_TOKEN', '');
    }

    /**
     * Send outgoing Instagram Direct message via Meta Graph API
     */
    public function sendMessage(string $recipientIgsid, string $messageText): ?string
    {
        if (empty($this->pageAccessToken)) {
            Log::warning("Instagram Page Access Token missing. Skipping outbound API call for IGSID: {$recipientIgsid}");
            return null;
        }

        $url = "https://graph.facebook.com/v19.0/me/messages?access_token={$this->pageAccessToken}";

        $response = Http::post($url, [
            'recipient' => ['id' => $recipientIgsid],
            'message' => ['text' => $messageText],
        ]);

        if ($response->successful()) {
            $msgId = $response->json('message_id');
            return $msgId ? (string)$msgId : 'ig_mid_' . uniqid();
        }

        Log::error('Instagram Direct API send message failed:', ['body' => $response->body()]);
        return null;
    }
}
