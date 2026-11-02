<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoleSpecificDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $adminFakultas;

    protected User $teknisi;

    protected User $picRuangan;

    protected User $mahasiswa;

    protected User $dosen;

    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $campus = Campus::create([
            'name' => 'Kampus 1 Al-Khairiyah',
            'code' => 'K1',
            'address' => 'Jl. H. Enggus Arja No. 1, Cilegon, Banten',
        ]);

        $building = Building::create([
            'campus_id' => $campus->id,
            'name' => 'Gedung Rektorat',
            'code' => 'G-REK',
            'total_floors' => 3,
        ]);

        $faculty = Faculty::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'dean_name' => 'Dr. Ir. Ahmad Sudrajat, M.T.',
        ]);

        $this->picRuangan = User::factory()->create([
            'role' => UserRole::PicRuangan,
            'is_active' => true,
        ]);

        $this->room = Room::create([
            'building_id' => $building->id,
            'faculty_id' => $faculty->id,
            'pic_user_id' => $this->picRuangan->id,
            'name' => 'Lab Komputer 1',
            'code' => 'LAB-KOMP-1',
            'floor' => 2,
            'capacity' => 40,
            'type' => 'laboratory',
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->adminFakultas = User::factory()->create([
            'role' => UserRole::AdminFakultas,
            'faculty_id' => $faculty->id,
            'is_active' => true,
        ]);

        $this->teknisi = User::factory()->create([
            'role' => UserRole::Teknisi,
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $this->dosen = User::factory()->create([
            'role' => UserRole::Dosen,
            'faculty_id' => $faculty->id,
            'is_active' => true,
        ]);
    }

    public function test_mahasiswa_sees_civitas_dashboard(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'civitas')
            ->has('civitasData')
            ->has('civitasData.myLoans')
            ->has('civitasData.myBookings')
            ->has('civitasData.myTickets')
            ->where('civitasData.activeLoansCount', 0)
        );
    }

    public function test_dosen_sees_civitas_dashboard(): void
    {
        $response = $this->actingAs($this->dosen)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'civitas')
            ->has('civitasData')
        );
    }

    public function test_teknisi_sees_teknisi_dashboard(): void
    {
        $response = $this->actingAs($this->teknisi)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'teknisi')
            ->has('teknisiData')
            ->has('teknisiData.myAssignedTickets')
            ->has('teknisiData.urgentTickets')
            ->has('teknisiData.metrics.assignedCount')
        );
    }

    public function test_pic_ruangan_sees_pic_ruangan_dashboard(): void
    {
        $response = $this->actingAs($this->picRuangan)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'pic_ruangan')
            ->has('picData')
            ->has('picData.myRooms')
            ->has('picData.todayBookings')
            ->has('picData.metrics.roomsCount')
        );
    }

    public function test_super_admin_sees_executive_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'executive')
            ->has('metrics')
            ->has('metrics.totalAssets')
            ->has('distributionByFaculty')
            ->has('utilizationData')
        );
    }

    public function test_admin_fakultas_sees_executive_dashboard(): void
    {
        $response = $this->actingAs($this->adminFakultas)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('dashboardType', 'executive')
            ->has('metrics')
            ->has('distributionByFaculty')
        );
    }
}
