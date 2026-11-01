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

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Campus $campus;

    protected Building $building;

    protected Room $room1;

    protected Room $room2;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->campus = Campus::create([
            'code' => 'KMP-TEST',
            'name' => 'Kampus Uji Coba',
        ]);

        $this->building = Building::create([
            'campus_id' => $this->campus->id,
            'code' => 'GDG-TEST',
            'name' => 'Gedung Uji Coba',
        ]);

        $this->room1 = Room::create([
            'building_id' => $this->building->id,
            'code' => 'R-101',
            'name' => 'Ruang 101',
        ]);

        $this->room2 = Room::create([
            'building_id' => $this->building->id,
            'code' => 'R-102',
            'name' => 'Ruang 102',
        ]);

        $this->category = Category::create([
            'code' => 'ELK',
            'name' => 'Elektronik',
            'is_consumable' => false,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_visiting_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertOk();
    }

    public function test_authenticated_user_can_view_assets_index(): void
    {
        Asset::create([
            'asset_code' => 'AST/TEST/001',
            'name' => 'Laptop ThinkPad X1',
            'category_id' => $this->category->id,
            'room_id' => $this->room1->id,
            'purchase_cost' => 20000000,
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
            'qr_code_token' => 'QR-TEST-001',
        ]);

        $response = $this->actingAs($this->user)->get('/assets');

        $response->assertOk();
    }

    public function test_authenticated_user_can_create_new_asset(): void
    {
        $payload = [
            'asset_code' => 'AST/2026/9999',
            'name' => 'Monitor Dell 27 Inch 4K',
            'brand' => 'Dell',
            'model' => 'U2723QE',
            'serial_number' => 'SN-DELL-9999',
            'category_id' => $this->category->id,
            'room_id' => $this->room1->id,
            'purchase_date' => '2026-01-10',
            'purchase_cost' => 8500000,
            'useful_life_years' => 5,
            'salvage_value' => 500000,
            'condition' => AssetCondition::Good->value,
            'status' => AssetStatus::Available->value,
            'specifications' => 'IPS Black Panel, USB-C 90W PD Hub',
        ];

        $response = $this->actingAs($this->user)->post('/assets', $payload);

        $this->assertDatabaseHas('assets', [
            'asset_code' => 'AST/2026/9999',
            'name' => 'Monitor Dell 27 Inch 4K',
        ]);

        $asset = Asset::where('asset_code', 'AST/2026/9999')->firstOrFail();
        $response->assertRedirect(route('assets.show', $asset));
    }

    public function test_updating_asset_room_automatically_creates_movement_history(): void
    {
        $asset = Asset::create([
            'asset_code' => 'AST/TEST/MOV-01',
            'name' => 'Proyektor Epson EB-2250U',
            'category_id' => $this->category->id,
            'room_id' => $this->room1->id,
            'purchase_cost' => 15000000,
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
            'qr_code_token' => 'QR-MOV-01',
        ]);

        $updatePayload = [
            'asset_code' => 'AST/TEST/MOV-01',
            'name' => 'Proyektor Epson EB-2250U',
            'category_id' => $this->category->id,
            'room_id' => $this->room2->id, // Moved to Room 2!
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good->value,
            'status' => AssetStatus::Available->value,
        ];

        $response = $this->actingAs($this->user)->put("/assets/{$asset->id}", $updatePayload);

        $response->assertRedirect(route('assets.show', $asset));

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'room_id' => $this->room2->id,
        ]);

        $this->assertDatabaseHas('asset_movements', [
            'asset_id' => $asset->id,
            'from_room_id' => $this->room1->id,
            'to_room_id' => $this->room2->id,
            'moved_by_id' => $this->user->id,
        ]);
    }
}
