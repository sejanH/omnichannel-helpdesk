<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\OmnichannelSeeder::class);
    }

    public function test_admin_has_delete_tickets_permission()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertTrue($admin->hasPermissionTo('delete-tickets'));
    }

    public function test_admin_can_delete_and_restore_tickets()
    {
        $admin = User::where('role', 'admin')->first();
        $ticket = Ticket::first();

        $response = $this->actingAs($admin)->deleteJson("/tickets/{$ticket->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);

        $response = $this->actingAs($admin)->postJson("/tickets/{$ticket->id}/restore");
        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'deleted_at' => null]);
    }

    public function test_user_without_delete_permission_is_forbidden()
    {
        $agent = User::where('email', 'john@helpdesk.com')->first();
        $ticket = Ticket::first();

        $response = $this->actingAs($agent)->deleteJson("/tickets/{$ticket->id}");
        $response->assertStatus(403);
    }
}
