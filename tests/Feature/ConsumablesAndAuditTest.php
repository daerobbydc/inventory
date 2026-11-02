<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\StockOpnameItemStatus;
use App\Enums\StockOpnameStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Consumable;
use App\Models\Room;
use App\Models\StockOpnameSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsumablesAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Category $category;

    protected Room $room1;

    protected Room $room2;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $campus = Campus::create(['code' => 'KMP-01', 'name' => 'Kampus Pusat']);
        $building = Building::create(['campus_id' => $campus->id, 'code' => 'GDG-01', 'name' => 'Gedung A']);
        $this->room1 = Room::create(['building_id' => $building->id, 'code' => 'R-101', 'name' => 'Lab Komputer 1']);
        $this->room2 = Room::create(['building_id' => $building->id, 'code' => 'R-102', 'name' => 'Ruang Dosen']);

        $this->category = Category::create([
            'code' => 'ATK',
            'name' => 'Alat Tulis Kantor',
            'useful_life_years' => 1,
            'is_consumable' => true,
        ]);

        $this->asset = Asset::create([
            'asset_code' => 'AST/2026/TEST-01',
            'name' => 'Proyektor Epson EB-X500',
            'category_id' => $this->category->id,
            'room_id' => $this->room1->id,
            'purchase_date' => now()->subMonths(6),
            'purchase_cost' => 6500000,
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
        ]);
    }

    public function test_user_can_view_consumables_catalog(): void
    {
        Consumable::create([
            'item_code' => 'ATK-HVS-01',
            'name' => 'Kertas HVS A4 80 GSM',
            'category_id' => $this->category->id,
            'unit' => 'rim',
            'current_stock' => 50,
            'minimum_stock' => 10,
            'unit_price' => 55000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('consumables.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Consumables/Index')
            ->has('consumables.data', 1)
            ->where('metrics.total_items', 1)
        );
    }

    public function test_user_can_create_consumable_item(): void
    {
        $response = $this->actingAs($this->admin)->post(route('consumables.store'), [
            'item_code' => 'ATK-SPD-01',
            'name' => 'Spidol Whiteboard Hitam',
            'category_id' => $this->category->id,
            'unit' => 'box',
            'current_stock' => 20,
            'minimum_stock' => 5,
            'unit_price' => 85000,
            'notes' => 'Keperluan kelas perkuliahan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consumables', [
            'item_code' => 'ATK-SPD-01',
            'current_stock' => 20,
        ]);
    }

    public function test_user_can_record_stock_in_and_stock_out(): void
    {
        $consumable = Consumable::create([
            'item_code' => 'ATK-TINTA-01',
            'name' => 'Tinta Printer Epson Black',
            'category_id' => $this->category->id,
            'unit' => 'botol',
            'current_stock' => 10,
            'minimum_stock' => 3,
            'unit_price' => 95000,
        ]);

        // Stock in (+5)
        $this->actingAs($this->admin)->post(route('consumables.transaction', $consumable), [
            'type' => 'in',
            'quantity' => 5,
            'reference_number' => 'PO/2026/01',
            'notes' => 'Pembelian rutin',
        ]);

        $consumable->refresh();
        $this->assertEquals(15, $consumable->current_stock);

        // Stock out (-4)
        $this->actingAs($this->admin)->post(route('consumables.transaction', $consumable), [
            'type' => 'out',
            'quantity' => 4,
            'reference_number' => 'REQ/2026/01',
            'notes' => 'Permintaan tendik',
        ]);

        $consumable->refresh();
        $this->assertEquals(11, $consumable->current_stock);
    }

    public function test_stock_out_fails_if_quantity_exceeds_stock(): void
    {
        $consumable = Consumable::create([
            'item_code' => 'ATK-LIMIT-01',
            'name' => 'Buku Catatan Inventaris',
            'category_id' => $this->category->id,
            'unit' => 'buku',
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit_price' => 25000,
        ]);

        $response = $this->actingAs($this->admin)->post(route('consumables.transaction', $consumable), [
            'type' => 'out',
            'quantity' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $consumable->refresh();
        $this->assertEquals(2, $consumable->current_stock);
    }

    public function test_user_can_access_stock_opname_scanner(): void
    {
        $response = $this->actingAs($this->admin)->get(route('stock-opname.scanner'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('StockOpname/Scanner')
            ->has('session')
            ->has('rooms')
            ->has('stats')
        );
    }

    public function test_qr_scanner_lookup_detects_matching_or_misplaced_asset(): void
    {
        // 1. Lookup in correct room (room1)
        $resMatch = $this->actingAs($this->admin)->getJson(route('stock-opname.lookup', [
            'code' => $this->asset->asset_code,
            'room_id' => $this->room1->id,
        ]));

        $resMatch->assertOk();
        $resMatch->assertJson([
            'is_match' => true,
            'verdict' => 'match',
        ]);

        // 2. Lookup in different room (room2)
        $resMisplaced = $this->actingAs($this->admin)->getJson(route('stock-opname.lookup', [
            'code' => $this->asset->asset_code,
            'room_id' => $this->room2->id,
        ]));

        $resMisplaced->assertOk();
        $resMisplaced->assertJson([
            'is_match' => false,
            'verdict' => 'misplaced',
        ]);
    }

    public function test_auditor_can_verify_and_relocate_misplaced_asset(): void
    {
        $session = StockOpnameSession::create([
            'session_code' => 'SO/TEST/001',
            'name' => 'Sensus Uji Coba',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'status' => StockOpnameStatus::Ongoing,
            'created_by_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('stock-opname.verify'), [
            'stock_opname_session_id' => $session->id,
            'asset_id' => $this->asset->id,
            'scanned_room_id' => $this->room2->id, // found in room2 instead of room1
            'condition' => AssetCondition::Good->value,
            'status' => StockOpnameItemStatus::Misplaced->value,
            'notes' => 'Aset dipindahkan permanen ke Ruang Dosen',
            'relocate_to_here' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('stock_opname_items', [
            'stock_opname_session_id' => $session->id,
            'asset_id' => $this->asset->id,
            'expected_room_id' => $this->room1->id,
            'scanned_room_id' => $this->room2->id,
            'status' => StockOpnameItemStatus::Misplaced->value,
        ]);

        // Asset room should be relocated to room2
        $this->asset->refresh();
        $this->assertEquals($this->room2->id, $this->asset->room_id);

        // Movement record should be created
        $this->assertDatabaseHas('asset_movements', [
            'asset_id' => $this->asset->id,
            'from_room_id' => $this->room1->id,
            'to_room_id' => $this->room2->id,
        ]);
    }

    public function test_user_can_propose_and_print_asset_disposal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('disposals.store'), [
            'asset_id' => $this->asset->id,
            'disposal_method' => 'lelang',
            'salvage_value' => 500000,
            'disposal_reason' => 'Perangkat motherboard terbakar dan suku cadang diskontinu.',
        ]);

        $response->assertRedirect();
        $this->asset->refresh();
        $this->assertEquals(AssetStatus::Disposed, $this->asset->status);
        $this->assertEquals('500000.00', $this->asset->salvage_value);

        // Test print Berita Acara
        $printRes = $this->actingAs($this->admin)->get(route('disposals.print', $this->asset));
        $printRes->assertOk();
        $printRes->assertInertia(fn ($page) => $page
            ->component('Disposals/Print')
            ->where('asset.id', $this->asset->id)
        );
    }

    public function test_super_admin_can_manage_users_and_test_whatsapp(): void
    {
        // 1. Create User
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Fahri Hidayat',
            'email' => 'fahri@unival.ac.id',
            'password' => 'password123',
            'nip_nim' => '199801012022011005',
            'phone' => '081299887766',
            'role' => UserRole::Teknisi->value,
            'department' => 'Biro Sarpras',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'fahri@unival.ac.id']);

        $user = User::where('email', 'fahri@unival.ac.id')->first();

        // 2. Toggle Status
        $this->actingAs($this->admin)->post(route('users.toggle-status', $user));
        $user->refresh();
        $this->assertFalse($user->is_active);

        // 3. WhatsApp Gateway Simulation
        $waRes = $this->actingAs($this->admin)->post(route('users.test-whatsapp'), [
            'phone' => '081299887766',
            'message' => 'Tes notifikasi WhatsApp Sarpras',
        ]);

        $waRes->assertRedirect();
        $waRes->assertSessionHas('success');
    }
}
