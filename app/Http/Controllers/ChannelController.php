<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    /**
     * Enforce Admin Authorization
     */
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only administrators can configure omnichannel integration channels.');
        }
    }

    /**
     * Display listing and setup studio for Omnichannel Channels
     */
    public function index()
    {
        $this->checkAdmin();

        // Ensure default baseline channels exist
        $this->ensureDefaultChannelsExist();

        $channels = Channel::orderBy('id')->get();

        return view('channels', compact('channels'));
    }

    /**
     * Update configuration for a specific Channel
     */
    public function update(Request $request, Channel $channel)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'configuration' => 'required|array',
        ]);

        $channel->name = $validated['name'];
        $channel->is_active = (bool)($request->input('is_active', false));

        // Sanitize & structure channel-specific configuration
        $config = $channel->configuration ?? [];

        foreach ($request->input('configuration', []) as $key => $value) {
            if (is_string($value)) {
                $config[$key] = trim($value);
            } else {
                $config[$key] = $value;
            }
        }

        $channel->configuration = $config;
        $channel->save();

        // Flush cached channel config
        Channel::flushChannelCache($channel->type);

        return redirect()->route('channels.index')
            ->with('success', "Omnichannel settings for '{$channel->name}' saved successfully!");
    }

    /**
     * Toggle Channel Active / Inactive Status
     */
    public function toggleStatus(Channel $channel)
    {
        $this->checkAdmin();

        $channel->is_active = !$channel->is_active;
        $channel->save();

        Channel::flushChannelCache($channel->type);

        $statusText = $channel->is_active ? 'enabled' : 'disabled';
        return redirect()->route('channels.index')
            ->with('success', "Channel '{$channel->name}' has been {$statusText}.");
    }

    /**
     * Create a New Custom Channel
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:whatsapp,facebook,instagram,telegram,email,web_chat',
            'icon' => 'nullable|string|max:50',
        ]);

        $channel = Channel::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? 'message-dots',
            'is_active' => true,
            'configuration' => [],
        ]);

        Channel::flushChannelCache($channel->type);

        return redirect()->route('channels.index')
            ->with('success', "New channel '{$channel->name}' created successfully!");
    }

    /**
     * Auto-provision default channels if missing
     */
    private function ensureDefaultChannelsExist(): void
    {
        $defaultChannels = [
            [
                'slug' => 'whatsapp-business',
                'name' => 'WhatsApp Business Cloud API',
                'type' => 'whatsapp',
                'icon' => 'brand-whatsapp',
                'is_active' => true,
                'configuration' => [
                    'phone_number' => '+18005550199',
                    'phone_number_id' => '109827364519283',
                    'token' => 'EAAG_SAMPLE_WHATSAPP_TOKEN',
                    'verify_token' => 'omnihelp_secret',
                ],
            ],
            [
                'slug' => 'facebook-messenger',
                'name' => 'Facebook Messenger API',
                'type' => 'facebook',
                'icon' => 'brand-facebook',
                'is_active' => true,
                'configuration' => [
                    'page_id' => '10928374615',
                    'page_access_token' => 'EAAH_SAMPLE_FACEBOOK_TOKEN',
                    'verify_token' => 'omnihelp_secret',
                ],
            ],
            [
                'slug' => 'instagram-direct',
                'name' => 'Instagram Direct Messaging',
                'type' => 'instagram',
                'icon' => 'brand-instagram',
                'is_active' => true,
                'configuration' => [
                    'instagram_account_id' => '178414092817263',
                    'page_access_token' => 'EAAI_SAMPLE_INSTAGRAM_TOKEN',
                    'verify_token' => 'omnihelp_secret',
                ],
            ],
            [
                'slug' => 'telegram-bot',
                'name' => 'Telegram Bot API',
                'type' => 'telegram',
                'icon' => 'brand-telegram',
                'is_active' => true,
                'configuration' => [
                    'bot_username' => '@OmniSupportBot',
                    'bot_token' => '123456789:ABC_SAMPLE_TELEGRAM_TOKEN',
                ],
            ],
            [
                'slug' => 'email-desk',
                'name' => 'Email Support Desk',
                'type' => 'email',
                'icon' => 'mail',
                'is_active' => true,
                'configuration' => [
                    'support_email' => 'support@helpdesk.com',
                    'smtp_host' => 'smtp.mailtrap.io',
                    'smtp_port' => '587',
                ],
            ],
            [
                'slug' => 'web-live-chat',
                'name' => 'Web Live Chat Widget',
                'type' => 'web_chat',
                'icon' => 'globe',
                'is_active' => true,
                'configuration' => [
                    'title' => 'Customer Support',
                    'welcome_message' => 'Hello! How can we assist you today?',
                    'widget_color' => '#4f46e5',
                    'theme' => 'light',
                    'launcher_position' => 'right',
                    'require_email' => true,
                ],
            ],
        ];

        foreach ($defaultChannels as $chData) {
            Channel::firstOrCreate(
                ['type' => $chData['type']],
                $chData
            );
        }
    }
}
