<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $adminFakultas;

    protected User $picRuangan;

    protected User $technician;

    protected User $student;

    protected User $otherStudent;

    protected Asset $asset;

    protected Room $room;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->adminFakultas = User::factory()->create([
            'role' => UserRole::AdminFakultas,
            'is_active' => true,
        ]);

        $this->picRuangan = User::factory()->create([
            'role' => UserRole::PicRuangan,
            'is_active' => true,
        ]);

        $this->technician = User::factory()->create([
            'role' => UserRole::Teknisi,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $this->otherStudent = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $campus = Campus::create(['code' => 'KMP-01', 'name' => 'Kampus 1']);
        $building = Building::create(['campus_id' => $campus->id, 'code' => 'GDG-01', 'name' => 'Gedung A']);
        $this->room = Room::create(['building_id' => $building->id, 'code' => 'R-101', 'name' => 'Lab 101']);
        $this->category = Category::create(['code' => 'ELK', 'name' => 'Elektronik', 'is_consumable' => false]);

        $this->asset = Asset::create([
            'asset_code' => 'AST/TEST/001',
            'name' => 'Laptop Asus ROG',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'purchase_cost' => 15000000,
            'useful_life_years' => 4,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
        ]);
    }

    public function test_student_cannot_access_settings_and_user_management(): void
    {
        $responseSettings = $this->actingAs($this->student)->get(route('settings.index'));
        $responseSettings->assertForbidden();

        $responseUserStore = $this->actingAs($this->student)->post(route('users.store'), [
            'name' => 'Hacker User',
            'email' => 'hacker@kampus.ac.id',
            'password' => 'password123',
            'role' => UserRole::SuperAdmin->value,
        ]);
        $responseUserStore->assertForbidden();
    }

    public function test_student_cannot_create_or_delete_assets(): void
    {
        $responseCreate = $this->actingAs($this->student)->get(route('assets.create'));
        $responseCreate->assertForbidden();

        $responseStore = $this->actingAs($this->student)->post(route('assets.store'), [
            'asset_code' => 'AST/ILLEGAL/01',
            'name' => 'Illegal Asset',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'useful_life_years' => 3,
            'condition' => AssetCondition::Good->value,
            'status' => AssetStatus::Available->value,
        ]);
        $responseStore->assertForbidden();

        $responseDelete = $this->actingAs($this->student)->delete(route('assets.destroy', $this->asset->id));
        $responseDelete->assertForbidden();
    }

    public function test_student_can_view_asset_catalog_and_details(): void
    {
        $responseIndex = $this->actingAs($this->student)->get(route('assets.index'));
        $responseIndex->assertOk();

        $responseShow = $this->actingAs($this->student)->get(route('assets.show', $this->asset->id));
        $responseShow->assertOk();
    }

    public function test_student_cannot_approve_or_handover_loans(): void
    {
        $loan = LoanTransaction::create([
            'transaction_code' => 'LOAN/TEST/001',
            'borrower_id' => $this->student->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'status' => LoanStatus::Pending,
            'purpose' => 'Kegiatan mahasiswa',
        ]);

        $responseApprove = $this->actingAs($this->student)->post(route('loans.approve', $loan->id), [
            'approval_notes' => 'Self approved',
        ]);
        $responseApprove->assertForbidden();

        $responseHandover = $this->actingAs($this->student)->post(route('loans.handover', $loan->id));
        $responseHandover->assertForbidden();
    }

    public function test_student_cannot_view_another_students_loan(): void
    {
        $otherLoan = LoanTransaction::create([
            'transaction_code' => 'LOAN/TEST/OTHER',
            'borrower_id' => $this->otherStudent->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'status' => LoanStatus::Pending,
            'purpose' => 'Private loan other student',
        ]);

        // Student tries to view other student's loan
        $response = $this->actingAs($this->student)->get(route('loans.show', $otherLoan->id));
        $response->assertForbidden();

        // But student CAN view their own loan
        $myLoan = LoanTransaction::create([
            'transaction_code' => 'LOAN/TEST/MINE',
            'borrower_id' => $this->student->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'status' => LoanStatus::Pending,
            'purpose' => 'My personal loan',
        ]);

        $responseMyLoan = $this->actingAs($this->student)->get(route('loans.show', $myLoan->id));
        $responseMyLoan->assertOk();
    }

    public function test_student_cannot_update_maintenance_tickets(): void
    {
        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TCK/TEST/001',
            'asset_id' => $this->asset->id,
            'reporter_id' => $this->student->id,
            'issue_description' => 'Layar laptop bergaris',
            'priority' => MaintenancePriority::Medium,
            'status' => MaintenanceStatus::Reported,
        ]);

        // Student tries to mark ticket completed
        $response = $this->actingAs($this->student)->put(route('maintenance.update', $ticket->id), [
            'status' => MaintenanceStatus::Completed->value,
            'cost' => 500000,
        ]);
        $response->assertForbidden();

        // Technician CAN update ticket
        $responseTech = $this->actingAs($this->technician)->put(route('maintenance.update', $ticket->id), [
            'status' => MaintenanceStatus::InProgress->value,
            'technician_id' => $this->technician->id,
            'resolution_notes' => 'Sedang memeriksa fleksibel LCD',
        ]);
        $responseTech->assertRedirect();
        $this->assertEquals(MaintenanceStatus::InProgress, $ticket->fresh()->status);
    }

    public function test_student_cannot_access_disposals_or_reports(): void
    {
        $responseDisposals = $this->actingAs($this->student)->get(route('disposals.index'));
        $responseDisposals->assertForbidden();

        $responseReports = $this->actingAs($this->student)->get(route('reports.index'));
        $responseReports->assertForbidden();

        $responseScanner = $this->actingAs($this->student)->get(route('stock-opname.scanner'));
        $responseScanner->assertForbidden();
    }

    public function test_pic_ruangan_can_manage_assets_and_loans_but_not_settings(): void
    {
        $responseAssets = $this->actingAs($this->picRuangan)->get(route('assets.create'));
        $responseAssets->assertOk();

        $responseDisposals = $this->actingAs($this->picRuangan)->get(route('disposals.index'));
        $responseDisposals->assertOk();

        $responseReports = $this->actingAs($this->picRuangan)->get(route('reports.index'));
        $responseReports->assertOk();

        // Cannot access Settings
        $responseSettings = $this->actingAs($this->picRuangan)->get(route('settings.index'));
        $responseSettings->assertForbidden();
    }

    public function test_super_admin_has_full_access(): void
    {
        $this->actingAs($this->superAdmin)->get(route('dashboard'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('assets.create'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('disposals.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('reports.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('stock-opname.scanner'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('settings.index'))->assertOk();
    }
}
