<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendExternalMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Message $message,
        public string $channelType,
        public Contact $contact
    ) {}

    public function handle(): void
    {
        $channelType = strtolower($this->channelType);
        
        $channelMessageId = null;

        if ($channelType === 'whatsapp' && $this->contact->phone) {
            $channelMessageId = app(\App\Services\WhatsAppService::class)->sendMessage($this->contact->phone, $this->message->content);
        } elseif ($channelType === 'telegram') {
            $chatId = str_replace('telegram:', '', $this->contact->notes ?? '');
            if (!empty($chatId)) {
                $channelMessageId = app(\App\Services\TelegramService::class)->sendMessage($chatId, $this->message->content);
            }
        } elseif ($channelType === 'facebook') {
            $psid = str_replace('facebook:', '', $this->contact->notes ?? '');
            if (!empty($psid)) {
                $channelMessageId = app(\App\Services\FacebookService::class)->sendMessage($psid, $this->message->content);
            }
        } elseif ($channelType === 'instagram') {
            $igsid = str_replace('instagram:', '', $this->contact->notes ?? '');
            if (!empty($igsid)) {
                $channelMessageId = app(\App\Services\InstagramService::class)->sendMessage($igsid, $this->message->content);
            }
        }

        if (!empty($channelMessageId)) {
            $this->message->update([
                'channel_message_id' => (string) $channelMessageId,
                'delivered_at' => now(),
            ]);
        }
    }
}
