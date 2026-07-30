<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;

    public function __construct()
    {
        $config = \App\Models\Channel::getCachedConfig('telegram');
        $this->botToken = $config['bot_token'] ?? env('TELEGRAM_BOT_TOKEN', '');
    }

    /**
     * Send outgoing Telegram message via Bot API
     */
    public function sendMessage(string $chatId, string $messageText)
    {
        if (empty($this->botToken)) {
            Log::warning("Telegram Bot Token missing. Skipping outbound API call for chat: {$chatId}");
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $messageText,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }
}
