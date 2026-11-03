<?php

namespace Tests\Feature;

use App\Enums\ProcurementStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Faculty;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $adminFakultasA;

    protected User $adminFakultasB;

    protected User $mahasiswa;

    protected Faculty $facultyA;

    protected Faculty $facultyB;

    protected Category $category;

    protected Room $roomA;

    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $campus = Campus::create([
            'name' => 'Kampus Utama',
            'code' => 'KMP-01',
            'address' => 'Jl. Engku Putri No. 1, Cilegon',
        ]);

        $building = Building::create([
            'campus_id' => $campus->id,
            'name' => 'Gedung Rektorat & Kuliah',
            'code' => 'GD-01',
            'total_floors' => 4,
        ]);

        $this->facultyA = Faculty::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'dean_name' => 'Dr. Ir. Ahmad Sudrajat, M.T.',
        ]);

        $this->facultyB = Faculty::create([
            'code' => 'FEB',
            'name' => 'Fakultas Ekonomi & Bisnis',
            'dean_name' => 'Dr. Hj. Siti Maryam, S.E., M.M.',
        ]);

        $this->roomA = Room::create([
            'building_id' => $building->id,
            'faculty_id' => $this->facultyA->id,
            'name' => 'Lab Multimedia 1',
            'code' => 'FT-LAB-01',
            'floor' => 2,
            'capacity' => 40,
        ]);

        $this->category = Category::create([
            'name' => 'Peralatan Komputer',
            'code' => 'KOMP',
            'useful_life_years' => 5,
        ]);

        $this->vendor = Vendor::create([
            'name' => 'PT Mitra Komputindo Kampus',
            'contact_person' => 'Budi Santoso',
            'phone' => '08123456789',
            'email' => 'sales@mitrakomputindo.co.id',
            'address' => 'Jakarta Barat',
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->adminFakultasA = User::factory()->create([
            'role' => UserRole::AdminFakultas,
            'faculty_id' => $this->facultyA->id,
            'is_active' => true,
        ]);

        $this->adminFakultasB = User::factory()->create([
            'role' => UserRole::AdminFakultas,
            'faculty_id' => $this->facultyB->id,
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'faculty_id' => $this->facultyA->id,
            'is_active' => true,
        ]);
    }

    public function test_faculty_staff_can_view_procurements_index_and_submit_proposal(): void
    {
        $response = $this->actingAs($this->adminFakultasA)->get(route('procurements.index'));
        $response->assertOk();

        $createResponse = $this->actingAs($this->adminFakultasA)->get(route('procurements.create'));
        $createResponse->assertOk();

        $postData = [
            'title' => 'Pengadaan 25 Unit PC Desktop Lab Teknik',
            'faculty_id' => $this->facultyA->id,
            'fiscal_year' => '2026',
            'budget_source' => 'Dana Yayasan Al-Khairiyah',
            'urgency' => 'urgent',
            'justification' => 'Peningkatan rasio PC praktikum mahasiswa prodi informatika.',
            'items' => [
                [
                    'item_name' => 'PC Desktop Core i7 16GB',
                    'category_id' => $this->category->id,
                    'brand' => 'Dell',
                    'model' => 'Optiplex 7090',
                    'specifications' => 'RAM 16GB, SSD 512GB, Monitor 24 Inch',
                    'quantity' => 25,
                    'unit_price_estimate' => 12000000,
                    'target_room_id' => $this->roomA->id,
                ],
            ],
        ];

        $storeResponse = $this->actingAs($this->adminFakultasA)->post(route('procurements.store'), $postData);
        $storeResponse->assertRedirect(route('procurements.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('procurement_requests', [
            'title' => 'Pengadaan 25 Unit PC Desktop Lab Teknik',
            'faculty_id' => $this->facultyA->id,
            'user_id' => $this->adminFakultasA->id,
            'total_estimated_cost' => 300000000, // 25 * 12,000,000
            'status' => ProcurementStatus::Submitted->value,
        ]);

        $this->assertDatabaseHas('procurement_items', [
            'item_name' => 'PC Desktop Core i7 16GB',
            'quantity' => 25,
            'unit_price_estimate' => 12000000,
            'total_price_estimate' => 300000000,
            'target_room_id' => $this->roomA->id,
        ]);
    }

    public function test_super_admin_can_review_and_approve_proposal(): void
    {
        $procurement = ProcurementRequest::create([
            'request_number' => 'PR/FT/2026/0001',
            'faculty_id' => $this->facultyA->id,
            'user_id' => $this->adminFakultasA->id,
            'title' => 'Pengadaan Proyektor Ruang Seminar',
            'fiscal_year' => '2026',
            'budget_source' => 'Dana Yayasan Al-Khairiyah',
            'urgency' => 'reguler',
            'total_estimated_cost' => 15000000,
            'status' => ProcurementStatus::Submitted,
        ]);

        // 1. Mark Under Review
        $reviewResponse = $this->actingAs($this->superAdmin)->post(
            route('procurements.status', $procurement->id),
            [
                'status' => 'under_review',
                'approval_notes' => 'Sedang diverifikasi standar daya listrik gedung.',
            ]
        );
        $reviewResponse->assertRedirect();
        $this->assertEquals(ProcurementStatus::UnderReview, $procurement->fresh()->status);

        // 2. Approve
        $approveResponse = $this->actingAs($this->superAdmin)->post(
            route('procurements.status', $procurement->id),
            [
                'status' => 'approved',
                'approval_notes' => 'Disetujui penuh dengan plafon anggaran TA 2026.',
            ]
        );
        $approveResponse->assertRedirect();
        $this->assertEquals(ProcurementStatus::Approved, $procurement->fresh()->status);
        $this->assertEquals($this->superAdmin->id, $procurement->fresh()->approver_id);
    }

    public function test_faculty_isolation_prevents_unauthorized_faculty_viewing(): void
    {
        $procurementA = ProcurementRequest::create([
            'request_number' => 'PR/FT/2026/0001',
            'faculty_id' => $this->facultyA->id,
            'user_id' => $this->adminFakultasA->id,
            'title' => 'Alat Praktikum Teknik Mesin',
            'fiscal_year' => '2026',
            'budget_source' => 'Dana Yayasan Al-Khairiyah',
            'total_estimated_cost' => 50000000,
            'status' => ProcurementStatus::Submitted,
        ]);

        // Admin Fakultas A can view
        $responseA = $this->actingAs($this->adminFakultasA)->get(route('procurements.show', $procurementA->id));
        $responseA->assertOk();

        // Admin Fakultas B cannot view Faculty A's proposal
        $responseB = $this->actingAs($this->adminFakultasB)->get(route('procurements.show', $procurementA->id));
        $responseB->assertForbidden();

        // Super Admin can view any faculty proposal
        $responseSuper = $this->actingAs($this->superAdmin)->get(route('procurements.show', $procurementA->id));
        $responseSuper->assertOk();
    }

    public function test_purchasing_details_and_physical_receipt_can_be_updated(): void
    {
        $procurement = ProcurementRequest::create([
            'request_number' => 'PR/FT/2026/0002',
            'faculty_id' => $this->facultyA->id,
            'user_id' => $this->adminFakultasA->id,
            'title' => 'Pengadaan Switch Network Lab',
            'fiscal_year' => '2026',
            'budget_source' => 'Dana Yayasan Al-Khairiyah',
            'total_estimated_cost' => 10000000,
            'status' => ProcurementStatus::Approved,
        ]);

        $updateResponse = $this->actingAs($this->superAdmin)->post(
            route('procurements.purchase', $procurement->id),
            [
                'vendor_id' => $this->vendor->id,
                'purchase_order_number' => 'PO/2026/11/008',
                'invoice_number' => 'INV/MKP/9921',
                'receipt_date' => now()->toDateString(),
                'status' => 'received',
            ]
        );

        $updateResponse->assertRedirect();
        $fresh = $procurement->fresh();
        $this->assertEquals(ProcurementStatus::Received, $fresh->status);
        $this->assertEquals($this->vendor->id, $fresh->vendor_id);
        $this->assertEquals('PO/2026/11/008', $fresh->purchase_order_number);
    }

    public function test_one_click_convert_to_assets_generates_catalog_records_and_completes_request(): void
    {
        $procurement = ProcurementRequest::create([
            'request_number' => 'PR/FT/2026/0003',
            'faculty_id' => $this->facultyA->id,
            'user_id' => $this->adminFakultasA->id,
            'vendor_id' => $this->vendor->id,
            'title' => 'Pengadaan 3 Unit Proyektor Seminar',
            'fiscal_year' => '2026',
            'budget_source' => 'Dana Yayasan Al-Khairiyah',
            'total_estimated_cost' => 24000000,
            'status' => ProcurementStatus::Received,
            'receipt_date' => now()->toDateString(),
        ]);

        ProcurementItem::create([
            'procurement_request_id' => $procurement->id,
            'category_id' => $this->category->id,
            'item_name' => 'Proyektor Epson EB-E500',
            'brand' => 'Epson',
            'model' => 'EB-E500',
            'quantity' => 3,
            'unit_price_estimate' => 8000000,
            'total_price_estimate' => 24000000,
            'target_room_id' => $this->roomA->id,
            'is_converted_to_asset' => false,
        ]);

        $initialAssetCount = Asset::count();

        $convertResponse = $this->actingAs($this->superAdmin)->post(
            route('procurements.convert-to-assets', $procurement->id)
        );

        $convertResponse->assertRedirect();
        $convertResponse->assertSessionHas('success');

        // Verify 3 new assets were created
        $this->assertEquals($initialAssetCount + 3, Asset::count());

        $this->assertDatabaseHas('assets', [
            'name' => 'Proyektor Epson EB-E500 (#1)',
            'room_id' => $this->roomA->id,
            'vendor_id' => $this->vendor->id,
            'purchase_cost' => 8000000,
        ]);

        // Verify proposal status is completed
        $this->assertEquals(ProcurementStatus::Completed, $procurement->fresh()->status);
        $this->assertTrue($procurement->items()->first()->is_converted_to_asset);
    }

    public function test_non_staff_user_cannot_create_procurement(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('procurements.create'));
        $response->assertForbidden();

        $postResponse = $this->actingAs($this->mahasiswa)->post(route('procurements.store'), [
            'title' => 'Ilegal Request',
        ]);
        $postResponse->assertForbidden();
    }
}
