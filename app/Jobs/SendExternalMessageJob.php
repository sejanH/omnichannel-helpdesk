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
        
        if ($channelType === 'whatsapp' && $this->contact->phone) {
            app(\App\Services\WhatsAppService::class)->sendMessage($this->contact->phone, $this->message->content);
        } elseif ($channelType === 'telegram') {
            $chatId = str_replace('telegram:', '', $this->contact->notes ?? '');
            if (!empty($chatId)) {
                app(\App\Services\TelegramService::class)->sendMessage($chatId, $this->message->content);
            }
        } elseif ($channelType === 'facebook') {
            $psid = str_replace('facebook:', '', $this->contact->notes ?? '');
            if (!empty($psid)) {
                app(\App\Services\FacebookService::class)->sendMessage($psid, $this->message->content);
            }
        } elseif ($channelType === 'instagram') {
            $igsid = str_replace('instagram:', '', $this->contact->notes ?? '');
            if (!empty($igsid)) {
                app(\App\Services\InstagramService::class)->sendMessage($igsid, $this->message->content);
            }
        }
    }
}
