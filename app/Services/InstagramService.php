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
    public function sendMessage(string $recipientIgsid, string $messageText)
    {
        if (empty($this->pageAccessToken)) {
            Log::warning("Instagram Page Access Token missing. Skipping outbound API call for IGSID: {$recipientIgsid}");
            return false;
        }

        $url = "https://graph.facebook.com/v19.0/me/messages?access_token={$this->pageAccessToken}";

        $response = Http::post($url, [
            'recipient' => ['id' => $recipientIgsid],
            'message' => ['text' => $messageText],
        ]);

        return $response->successful();
    }
}
