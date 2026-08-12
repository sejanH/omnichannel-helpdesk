<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\OmnichannelSeeder::class);
    }

    public function test_admin_can_access_channels_page()
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get(route('channels.index'));
        $response->assertStatus(200);
        $response->assertSee('Omnichannel Integrations');
    }

    public function test_admin_can_update_whatsapp_channel_config()
    {
        $admin = User::where('role', 'admin')->first();
        $channel = Channel::where('type', 'whatsapp')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('channels.update', $channel->id), [
            'name' => 'WhatsApp Business Cloud',
            'is_active' => '1',
            'configuration' => [
                'phone_number' => '+18005559999',
                'phone_number_id' => '999888777',
                'token' => 'SECRET_WA_TOKEN_123',
                'verify_token' => 'my_custom_secret',
            ],
        ]);

        $response->assertRedirect(route('channels.index'));
        $channel->refresh();

        $this->assertEquals('+18005559999', $channel->configuration['phone_number']);
        $this->assertEquals('SECRET_WA_TOKEN_123', $channel->configuration['token']);

        // Check cached config helper
        $cached = Channel::getCachedConfig('whatsapp');
        $this->assertEquals('SECRET_WA_TOKEN_123', $cached['token']);
    }

    public function test_admin_can_toggle_channel_status()
    {
        $admin = User::where('role', 'admin')->first();
        $channel = Channel::where('type', 'telegram')->firstOrFail();

        $initialStatus = $channel->is_active;
        $response = $this->actingAs($admin)->post(route('channels.toggle', $channel->id));

        $response->assertRedirect(route('channels.index'));
        $channel->refresh();
        $this->assertEquals(!$initialStatus, $channel->is_active);
    }

    public function test_non_admin_cannot_access_channel_settings()
    {
        $agent = User::where('role', 'agent')->first();
        $response = $this->actingAs($agent)->get(route('channels.index'));
        $response->assertStatus(403);
    }
}
