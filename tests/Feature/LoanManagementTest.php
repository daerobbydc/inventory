<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected User $student;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'role' => UserRole::PicRuangan,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $campus = Campus::create(['code' => 'KMP-01', 'name' => 'Kampus 1']);
        $building = Building::create(['campus_id' => $campus->id, 'code' => 'GDG-01', 'name' => 'Gedung 1']);
        $room = Room::create(['building_id' => $building->id, 'code' => 'R-01', 'name' => 'Ruang Lab']);
        $category = Category::create(['code' => 'AV', 'name' => 'Audio Visual', 'is_consumable' => false]);

        $this->asset = Asset::create([
            'asset_code' => 'AST/TEST/CAM-01',
            'name' => 'Kamera Mirrorless Sony',
            'category_id' => $category->id,
            'room_id' => $room->id,
            'purchase_cost' => 12000000,
            'useful_life_years' => 3,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
            'qr_code_token' => 'QR-CAM-TEST',
        ]);
    }

    public function test_user_can_view_loan_index_and_create_page(): void
    {
        $response = $this->actingAs($this->student)->get(route('loans.index'));
        $response->assertOk();

        $responseCreate = $this->actingAs($this->student)->get(route('loans.create'));
        $responseCreate->assertOk();
    }

    public function test_user_can_submit_loan_request(): void
    {
        $payload = [
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'purpose' => 'Praktikum mata kuliah multimedia',
            'asset_ids' => [$this->asset->id],
        ];

        $response = $this->actingAs($this->student)->post(route('loans.store'), $payload);

        $response->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('loan_transactions', [
            'borrower_id' => $this->student->id,
            'status' => LoanStatus::Pending->value,
            'purpose' => 'Praktikum mata kuliah multimedia',
        ]);

        $this->assertDatabaseHas('loan_items', [
            'asset_id' => $this->asset->id,
        ]);
    }

    public function test_staff_can_approve_and_handover_loan(): void
    {
        $loan = LoanTransaction::create([
            'transaction_code' => 'LOAN/TEST/001',
            'borrower_id' => $this->student->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(2)->toDateString(),
            'status' => LoanStatus::Pending,
            'purpose' => 'Keperluan seminar mahasiswa',
        ]);

        LoanItem::create([
            'loan_transaction_id' => $loan->id,
            'asset_id' => $this->asset->id,
            'initial_condition' => AssetCondition::Good,
        ]);

        // Staff approves
        $responseApprove = $this->actingAs($this->staff)->post(route('loans.approve', $loan->id), [
            'approval_notes' => 'Disetujui untuk pemakaian seminar',
        ]);

        $responseApprove->assertRedirect(route('loans.show', $loan->id));
        $this->assertEquals(LoanStatus::Approved, $loan->fresh()->status);

        // Staff confirms physical handover
        $responseHandover = $this->actingAs($this->staff)->post(route('loans.handover', $loan->id));
        $responseHandover->assertRedirect(route('loans.show', $loan->id));

        $this->assertEquals(LoanStatus::Borrowed, $loan->fresh()->status);
        $this->assertEquals(AssetStatus::Borrowed, $this->asset->fresh()->status);
    }

    public function test_staff_can_process_return_restoring_asset_to_available(): void
    {
        $loan = LoanTransaction::create([
            'transaction_code' => 'LOAN/TEST/002',
            'borrower_id' => $this->student->id,
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(1)->toDateString(),
            'status' => LoanStatus::Borrowed,
            'purpose' => 'Uji coba return',
        ]);

        $item = LoanItem::create([
            'loan_transaction_id' => $loan->id,
            'asset_id' => $this->asset->id,
            'initial_condition' => AssetCondition::Good,
        ]);

        $this->asset->update(['status' => AssetStatus::Borrowed]);

        // Process return
        $responseReturn = $this->actingAs($this->staff)->post(route('loans.return', $loan->id), [
            'return_conditions' => [
                $item->id => AssetCondition::Good->value,
            ],
            'return_notes' => 'Semua aksesori lengkap dan berfungsi normal.',
        ]);

        $responseReturn->assertRedirect(route('loans.show', $loan->id));

        $this->assertEquals(LoanStatus::Returned, $loan->fresh()->status);
        $this->assertNotNull($loan->fresh()->actual_return_date);
        $this->assertEquals(AssetStatus::Available, $this->asset->fresh()->status);
    }
}
