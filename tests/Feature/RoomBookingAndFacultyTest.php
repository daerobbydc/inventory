<?php

namespace Tests\Feature;

use App\Enums\RoomBookingStatus;
use App\Enums\UserRole;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomBookingAndFacultyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $adminFakultas;

    protected User $mahasiswa;

    protected User $otherMahasiswa;

    protected Room $room;

    protected Faculty $faculty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faculty = Faculty::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
            'dean_name' => 'Dr. Ir. Ahmad Sudrajat, M.T.',
            'phone' => '0254-391234',
            'email' => 'ft@unival.ac.id',
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->adminFakultas = User::factory()->create([
            'role' => UserRole::AdminFakultas,
            'faculty_id' => $this->faculty->id,
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'faculty_id' => $this->faculty->id,
            'is_active' => true,
        ]);

        $this->otherMahasiswa = User::factory()->create([
            'role' => UserRole::Mahasiswa,
            'is_active' => true,
        ]);

        $campus = Campus::create([
            'code' => 'KMP-01',
            'name' => 'Kampus Utama',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        $building = Building::create([
            'campus_id' => $campus->id,
            'code' => 'GDG-A',
            'name' => 'Gedung Rektorat',
            'total_floors' => 3,
        ]);

        $this->room = Room::create([
            'building_id' => $building->id,
            'faculty_id' => $this->faculty->id,
            'code' => 'AUD-01',
            'name' => 'Auditorium Utama Unival',
            'floor_number' => 1,
            'type' => 'auditorium',
            'capacity' => 200,
        ]);
    }

    public function test_super_admin_can_create_and_update_faculty(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('faculties.store'), [
            'code' => 'FEB',
            'name' => 'Fakultas Ekonomi dan Bisnis',
            'dean_name' => 'Dr. Siti Maryam',
            'phone' => '0254-391235',
            'email' => 'feb@unival.ac.id',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('faculties', [
            'code' => 'FEB',
            'name' => 'Fakultas Ekonomi dan Bisnis',
        ]);

        $faculty = Faculty::where('code', 'FEB')->first();

        $updateResponse = $this->actingAs($this->superAdmin)->put(route('faculties.update', $faculty->id), [
            'code' => 'FEB-UNIVAL',
            'name' => 'Fakultas Ekonomi & Bisnis Terpadu',
            'dean_name' => 'Dr. Siti Maryam, M.M.',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('faculties', [
            'code' => 'FEB-UNIVAL',
            'name' => 'Fakultas Ekonomi & Bisnis Terpadu',
        ]);
    }

    public function test_non_super_admin_cannot_manage_faculties(): void
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('faculties.store'), [
            'code' => 'FAI',
            'name' => 'Fakultas Agama Islam',
        ]);

        $response->assertStatus(403);
    }

    public function test_cannot_delete_faculty_with_existing_rooms(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('faculties.destroy', $this->faculty->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('faculties', ['id' => $this->faculty->id]);
    }

    public function test_user_can_submit_room_booking_request(): void
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('room-bookings.store'), [
            'room_id' => $this->room->id,
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'attendees_count' => 50,
            'purpose' => 'Seminar Nasional Himpunan Mahasiswa',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('room_bookings', [
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'status' => RoomBookingStatus::Pending->value,
            'purpose' => 'Seminar Nasional Himpunan Mahasiswa',
        ]);
    }

    public function test_room_booking_anti_conflict_validation(): void
    {
        $date = now()->addDays(2)->toDateString();

        // Existing approved booking: 09:00 - 12:00
        RoomBooking::create([
            'booking_code' => 'RSRV-2026-9991',
            'room_id' => $this->room->id,
            'user_id' => $this->superAdmin->id,
            'booking_date' => $date,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'purpose' => 'Acara Senat',
            'attendees_count' => 100,
            'status' => RoomBookingStatus::Approved,
        ]);

        // Attempt overlapping booking: 11:00 - 14:00 (overlaps with 09:00-12:00)
        $response = $this->actingAs($this->mahasiswa)->post(route('room-bookings.store'), [
            'room_id' => $this->room->id,
            'booking_date' => $date,
            'start_time' => '11:00',
            'end_time' => '14:00',
            'attendees_count' => 30,
            'purpose' => 'Rapat Ormawa',
        ]);

        $response->assertSessionHasErrors('start_time');

        // Non-overlapping booking should succeed: 13:00 - 16:00
        $successResponse = $this->actingAs($this->mahasiswa)->post(route('room-bookings.store'), [
            'room_id' => $this->room->id,
            'booking_date' => $date,
            'start_time' => '13:00',
            'end_time' => '16:00',
            'attendees_count' => 30,
            'purpose' => 'Rapat Ormawa Siang',
        ]);

        $successResponse->assertSessionDoesntHaveErrors();
    }

    public function test_staff_can_approve_and_reject_room_booking(): void
    {
        $booking = RoomBooking::create([
            'booking_code' => 'RSRV-2026-9992',
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'booking_date' => now()->addDays(3)->toDateString(),
            'start_time' => '08:00',
            'end_time' => '11:00',
            'purpose' => 'Praktikum Pengganti',
            'attendees_count' => 40,
            'status' => RoomBookingStatus::Pending,
        ]);

        $approveResponse = $this->actingAs($this->adminFakultas)->post(route('room-bookings.approve', $booking->id), [
            'approval_notes' => 'Disetujui untuk kegiatan akademik.',
        ]);

        $approveResponse->assertSessionHas('success');
        $this->assertEquals(RoomBookingStatus::Approved, $booking->fresh()->status);
        $this->assertEquals($this->adminFakultas->id, $booking->fresh()->approver_id);

        $rejectResponse = $this->actingAs($this->adminFakultas)->post(route('room-bookings.reject', $booking->id), [
            'approval_notes' => 'Dibatalkan karena ada jadwal pemeliharaan AC.',
        ]);

        $rejectResponse->assertSessionHas('success');
        $this->assertEquals(RoomBookingStatus::Rejected, $booking->fresh()->status);
    }

    public function test_non_staff_cannot_approve_or_reject_room_booking(): void
    {
        $booking = RoomBooking::create([
            'booking_code' => 'RSRV-2026-9993',
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'booking_date' => now()->addDays(4)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'purpose' => 'Diskusi Mahasiswa',
            'attendees_count' => 15,
            'status' => RoomBookingStatus::Pending,
        ]);

        $response = $this->actingAs($this->otherMahasiswa)->post(route('room-bookings.approve', $booking->id));
        $response->assertStatus(403);
    }

    public function test_user_cannot_view_other_users_room_booking_detail(): void
    {
        $booking = RoomBooking::create([
            'booking_code' => 'RSRV-2026-9994',
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'booking_date' => now()->addDays(5)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'purpose' => 'Kegiatan Mahasiswa',
            'attendees_count' => 15,
            'status' => RoomBookingStatus::Pending,
        ]);

        // Other student cannot view
        $forbiddenResponse = $this->actingAs($this->otherMahasiswa)->get(route('room-bookings.show', $booking->id));
        $forbiddenResponse->assertStatus(403);

        // Owner can view
        $ownerResponse = $this->actingAs($this->mahasiswa)->get(route('room-bookings.show', $booking->id));
        $ownerResponse->assertStatus(200);

        // Staff can view
        $staffResponse = $this->actingAs($this->adminFakultas)->get(route('room-bookings.show', $booking->id));
        $staffResponse->assertStatus(200);
    }

    public function test_borrower_can_cancel_their_room_booking(): void
    {
        $booking = RoomBooking::create([
            'booking_code' => 'RSRV-2026-9995',
            'room_id' => $this->room->id,
            'user_id' => $this->mahasiswa->id,
            'booking_date' => now()->addDays(6)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'purpose' => 'Acara Dibatalkan',
            'attendees_count' => 20,
            'status' => RoomBookingStatus::Pending,
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('room-bookings.cancel', $booking->id));
        $response->assertSessionHas('success');
        $this->assertEquals(RoomBookingStatus::Cancelled, $booking->fresh()->status);
    }
}
