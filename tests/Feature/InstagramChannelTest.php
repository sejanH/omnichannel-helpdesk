<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstagramChannelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\OmnichannelSeeder::class);
    }

    public function test_admin_can_configure_instagram_channel()
    {
        $admin = User::where('role', 'admin')->first();
        $channel = Channel::where('type', 'instagram')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('channels.update', $channel->id), [
            'name' => 'Instagram Support Official',
            'is_active' => '1',
            'configuration' => [
                'instagram_account_id' => '178414099999999',
                'page_access_token' => 'SECRET_INSTAGRAM_TOKEN_123',
                'verify_token' => 'ig_secret_verify',
            ],
        ]);

        $response->assertRedirect(route('channels.index'));
        $channel->refresh();

        $this->assertEquals('178414099999999', $channel->configuration['instagram_account_id']);
        $this->assertEquals('SECRET_INSTAGRAM_TOKEN_123', $channel->configuration['page_access_token']);
    }

    public function test_instagram_webhook_verification()
    {
        $response = $this->get('/api/v1/webhooks/instagram?hub_mode=subscribe&hub_verify_token=omnihelp_secret&hub_challenge=CHALLENGE_CODE_123');

        $response->assertStatus(200);
        $response->assertSee('CHALLENGE_CODE_123');
    }

    public function test_incoming_instagram_webhook_creates_ticket()
    {
        $payload = [
            'object' => 'instagram',
            'entry' => [
                [
                    'id' => '178414092817263',
                    'messaging' => [
                        [
                            'sender' => ['id' => '10029384756'],
                            'recipient' => ['id' => '178414092817263'],
                            'timestamp' => 1600000000,
                            'message' => ['mid' => 'm_99281', 'text' => 'Hi, I need help with my Instagram order!'],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/webhooks/instagram', $payload);
        $response->assertStatus(200);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Instagram DM from IG User (4756)',
        ]);

        $this->assertDatabaseHas('messages', [
            'content' => 'Hi, I need help with my Instagram order!',
        ]);
    }
}
