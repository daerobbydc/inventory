<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $campus = Campus::create(['code' => 'KMP-01', 'name' => 'Kampus Pusat']);
        $building = Building::create(['campus_id' => $campus->id, 'code' => 'GDG-01', 'name' => 'Gedung Rektorat']);
        $room = Room::create(['building_id' => $building->id, 'code' => 'R-01', 'name' => 'Ruang Sarpras']);
        $category = Category::create(['code' => 'ELK', 'name' => 'Elektronik & Server', 'useful_life_years' => 4]);

        Asset::create([
            'asset_code' => 'AST/2026/SRV-01',
            'name' => 'Server Dell PowerEdge R740',
            'category_id' => $category->id,
            'room_id' => $room->id,
            'purchase_date' => now()->subYears(2),
            'purchase_cost' => 40000000,
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
        ]);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_reports_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Reports/Index')
            ->has('valuation')
            ->has('byCategory')
            ->has('byBuilding')
            ->has('conditionStats')
            ->where('valuation.total_assets', 1)
        );
    }

    public function test_user_can_view_printable_bmn_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.print'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Reports/Print')
            ->has('assets', 1)
            ->has('summary')
            ->where('summary.total_items', 1)
        );
    }

    public function test_user_can_view_settings_and_master_data(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Settings/Index')
            ->has('stats')
            ->has('campuses')
            ->has('buildings')
            ->has('rooms')
            ->has('categories')
            ->has('syncStatus')
        );
    }
}
