<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $technician;

    protected User $reporter;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->technician = User::factory()->create([
            'role' => UserRole::Teknisi,
            'is_active' => true,
        ]);

        $this->reporter = User::factory()->create([
            'role' => UserRole::Dosen,
            'is_active' => true,
        ]);

        $campus = Campus::create(['code' => 'KMP-01', 'name' => 'Kampus Pusat']);
        $building = Building::create(['campus_id' => $campus->id, 'code' => 'GDG-01', 'name' => 'Gedung C']);
        $room = Room::create(['building_id' => $building->id, 'code' => 'R-01', 'name' => 'Lab Jaringan']);
        $category = Category::create(['code' => 'ELK', 'name' => 'Elektronik & Pendingin', 'useful_life_years' => 5]);

        $this->asset = Asset::create([
            'asset_code' => 'AST/2026/AC-01',
            'name' => 'AC Daikin Inverter 2 PK',
            'category_id' => $category->id,
            'room_id' => $room->id,
            'purchase_date' => now()->subYears(1),
            'purchase_cost' => 7500000,
            'useful_life_years' => 5,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
        ]);
    }

    public function test_guest_cannot_access_maintenance_routes(): void
    {
        $response = $this->get(route('maintenance.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_maintenance_index_with_metrics(): void
    {
        MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0001',
            'asset_id' => $this->asset->id,
            'reporter_id' => $this->reporter->id,
            'technician_id' => $this->technician->id,
            'issue_description' => 'AC mati total, tidak keluar hembusan dingin.',
            'priority' => MaintenancePriority::Critical,
            'status' => MaintenanceStatus::Reported,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('maintenance.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Maintenance/Index')
            ->has('tickets.data', 1)
            ->has('metrics')
            ->where('metrics.total', 1)
            ->where('metrics.reported', 1)
        );
    }

    public function test_user_can_view_create_maintenance_form(): void
    {
        $response = $this->actingAs($this->reporter)->get(route('maintenance.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Maintenance/Create')
            ->has('assets')
            ->has('technicians')
            ->has('priorities')
        );
    }

    public function test_user_can_submit_damage_report_ticket(): void
    {
        $response = $this->actingAs($this->reporter)->post(route('maintenance.store'), [
            'asset_id' => $this->asset->id,
            'issue_description' => 'Proyektor mengeluarkan asap kecil dan lampu indikator menyala merah berkedip.',
            'priority' => MaintenancePriority::Critical->value,
            'technician_id' => $this->technician->id,
        ]);

        $ticket = MaintenanceTicket::first();

        $this->assertNotNull($ticket);
        $this->assertEquals($this->asset->id, $ticket->asset_id);
        $this->assertEquals($this->reporter->id, $ticket->reporter_id);
        $this->assertEquals(MaintenanceStatus::Reported, $ticket->status);
        $this->assertEquals(MaintenancePriority::Critical, $ticket->priority);

        // Asset status should be set to Maintenance and Damaged
        $this->asset->refresh();
        $this->assertEquals(AssetStatus::Maintenance, $this->asset->status);
        $this->assertEquals(AssetCondition::HeavyDamage, $this->asset->condition);

        $response->assertRedirect(route('maintenance.show', $ticket));
    }

    public function test_user_can_view_ticket_detail(): void
    {
        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0002',
            'asset_id' => $this->asset->id,
            'reporter_id' => $this->reporter->id,
            'technician_id' => $this->technician->id,
            'issue_description' => 'Kerusakan pada remote AC dan filter kotor.',
            'priority' => MaintenancePriority::Medium,
            'status' => MaintenanceStatus::InProgress,
        ]);

        $response = $this->actingAs($this->reporter)->get(route('maintenance.show', $ticket));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Maintenance/Show')
            ->where('ticket.id', $ticket->id)
            ->where('ticket.ticket_code', 'TCK/202611/0002')
        );
    }

    public function test_technician_or_admin_can_update_ticket_status_and_resolution(): void
    {
        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TCK/202611/0003',
            'asset_id' => $this->asset->id,
            'reporter_id' => $this->reporter->id,
            'issue_description' => 'Pipa freon bocor.',
            'priority' => MaintenancePriority::High,
            'status' => MaintenanceStatus::Reported,
        ]);

        // Mark as In Progress with assigned technician
        $response = $this->actingAs($this->superAdmin)->put(route('maintenance.update', $ticket), [
            'status' => MaintenanceStatus::InProgress->value,
            'technician_id' => $this->technician->id,
            'resolution_notes' => 'Sedang dilakukan pengelasan pipa freon.',
        ]);

        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals(MaintenanceStatus::InProgress, $ticket->status);
        $this->assertEquals($this->technician->id, $ticket->technician_id);

        // Mark as Completed with cost
        $response2 = $this->actingAs($this->superAdmin)->put(route('maintenance.update', $ticket), [
            'status' => MaintenanceStatus::Completed->value,
            'cost' => 350000,
            'resolution_notes' => 'Pengisian freon R32 selesai, AC kembali dingin normal.',
        ]);

        $response2->assertRedirect();
        $ticket->refresh();
        $this->assertEquals(MaintenanceStatus::Completed, $ticket->status);
        $this->assertEquals('350000.00', $ticket->cost);
        $this->assertNotNull($ticket->completed_at);

        // Asset condition should return to Available and Good
        $this->asset->refresh();
        $this->assertEquals(AssetStatus::Available, $this->asset->status);
        $this->assertEquals(AssetCondition::Good, $this->asset->condition);
    }
}
