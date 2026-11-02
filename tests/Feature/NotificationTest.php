<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\RoomBookingStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Faculty;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use App\Services\SystemNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $mahasiswa;

    protected User $superAdmin;

    protected User $picRuangan;

    protected User $teknisi;

    protected Room $room;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $campus = Campus::create([
            'name' => 'Kampus 1 Al-Khairiyah',
            'code' => 'K1',
            'address' => 'Cilegon',
        ]);

        $building = Building::create([
            'campus_id' => $campus->id,
            'name' => 'Gedung Rektorat',
            'code' => 'G-REK',
            'total_floors' => 2,
        ]);

        $faculty = Faculty::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
        ]);

        $this->picRuangan = User::factory()->create([
            'role' => UserRole::PicRuangan,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'building_id' => $building->id,
            'faculty_id' => $faculty->id,
            'pic_user_id' => $this->picRuangan->id,
            'name' => 'Lab Jaringan',
            'code' => 'LAB-JAR',
            'floor' => 1,
            'capacity' => 30,
            'type' => 'laboratory',
        ]);

        $category = Category::create([
            'code' => 'ELK',
            'name' => 'Elektronik',
            'useful_life_years' => 5,
        ]);

        $this->asset = Asset::create([
            'asset_code' => 'AST/2026/ELK-001',
            'name' => 'AC Daikin 2PK',
            'category_id' => $category->id,
            'room_id' => $this->room->id,
            'purchase_date' => now()->subYear(),
            'purchase_cost' => 8000000,
            'useful_life_years' => 5,
            'condition' => AssetCondition::Good,
            'status' => AssetStatus::Available,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->teknisi = User::factory()->create([
            'role' => UserRole::Teknisi,
            'is_active' => true,
        ]);
    }

    public function test_service_returns_notifications_for_civitas_approved_loans(): void
    {
        LoanTransaction::create([
            'transaction_code' => 'TRX-TEST-001',
            'borrower_id' => $this->mahasiswa->id,
            'loan_date' => now(),
            'expected_return_date' => now()->addDays(3),
            'status' => LoanStatus::Approved->value,
            'purpose' => 'Praktikum',
        ]);

        $service = new SystemNotificationService;
        $notifications = $service->getNotificationsFor($this->mahasiswa);

        $this->assertNotEmpty($notifications);
        $this->assertEquals('Peminjaman Disetujui', $notifications[0]['title']);
        $this->assertEquals('success', $notifications[0]['type']);
    }

    public function test_service_returns_notifications_for_pic_pending_bookings(): void
    {
        RoomBooking::create([
            'booking_code' => 'BK-TEST-001',
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'purpose' => 'Rapat BEM',
            'status' => RoomBookingStatus::Pending->value,
        ]);

        $service = new SystemNotificationService;
        $notifications = $service->getNotificationsFor($this->picRuangan);

        $this->assertNotEmpty($notifications);
        $this->assertEquals('Permohonan Reservasi Ruang', $notifications[0]['title']);
        $this->assertEquals('warning', $notifications[0]['type']);
    }

    public function test_service_returns_notifications_for_teknisi_assigned_tickets(): void
    {
        MaintenanceTicket::create([
            'ticket_code' => 'TCK-TEST-001',
            'asset_id' => $this->asset->id,
            'technician_id' => $this->teknisi->id,
            'reporter_id' => $this->mahasiswa->id,
            'priority' => 'critical',
            'status' => MaintenanceStatus::InProgress->value,
            'issue_description' => 'AC Mati total dan mengeluarkan asap',
        ]);

        $service = new SystemNotificationService;
        $notifications = $service->getNotificationsFor($this->teknisi);

        $this->assertNotEmpty($notifications);
        $this->assertEquals('Tiket Darurat Ditugaskan', $notifications[0]['title']);
        $this->assertEquals('danger', $notifications[0]['type']);
    }

    public function test_shared_inertia_notifications_prop(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('notifications')
            ->has('unread_notifications_count')
        );
    }
}
