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
    public function sendMessage(string $recipientPsid, string $messageText)
    {
        if (empty($this->pageAccessToken)) {
            Log::warning("Facebook Page Access Token missing. Skipping outbound call for PSID: {$recipientPsid}");
            return false;
        }

        $url = "https://graph.facebook.com/v19.0/me/messages?access_token={$this->pageAccessToken}";

        $response = Http::post($url, [
            'recipient' => ['id' => $recipientPsid],
            'message' => ['text' => $messageText],
        ]);

        return $response->successful();
    }
}
