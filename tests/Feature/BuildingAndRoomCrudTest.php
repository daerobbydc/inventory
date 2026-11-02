<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingAndRoomCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $dosen;

    protected Campus $campus;

    protected Faculty $faculty;

    protected Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->campus = Campus::create([
            'name' => 'Kampus 1 Al-Khairiyah',
            'code' => 'K1',
            'address' => 'Cilegon',
        ]);

        $this->faculty = Faculty::create([
            'code' => 'FT',
            'name' => 'Fakultas Teknik',
        ]);

        $this->building = Building::create([
            'campus_id' => $this->campus->id,
            'code' => 'GDG-TEST',
            'name' => 'Gedung Uji Coba',
            'total_floors' => 3,
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
        ]);

        $this->dosen = User::factory()->create([
            'role' => UserRole::Dosen,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_create_building(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('buildings.store'), [
            'campus_id' => $this->campus->id,
            'code' => 'GDG-NEW',
            'name' => 'Gedung Laboratorium Baru',
            'total_floors' => 4,
            'description' => 'Gedung riset terpadu',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('buildings', [
            'code' => 'GDG-NEW',
            'name' => 'Gedung Laboratorium Baru',
            'total_floors' => 4,
        ]);
    }

    public function test_super_admin_can_update_building(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('buildings.update', $this->building->id), [
            'campus_id' => $this->campus->id,
            'code' => 'GDG-UPDATED',
            'name' => 'Gedung Uji Coba Update',
            'total_floors' => 5,
            'description' => 'Deskripsi baru',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('buildings', [
            'id' => $this->building->id,
            'code' => 'GDG-UPDATED',
            'name' => 'Gedung Uji Coba Update',
            'total_floors' => 5,
        ]);
    }

    public function test_super_admin_can_delete_empty_building(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('buildings.destroy', $this->building->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('buildings', [
            'id' => $this->building->id,
        ]);
    }

    public function test_cannot_delete_building_with_rooms(): void
    {
        Room::create([
            'building_id' => $this->building->id,
            'code' => 'R-OCCUPIED',
            'name' => 'Ruang Terpakai',
            'floor_number' => 1,
            'type' => 'classroom',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('buildings.destroy', $this->building->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('buildings', [
            'id' => $this->building->id,
        ]);
    }

    public function test_super_admin_can_create_room(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('rooms.store'), [
            'building_id' => $this->building->id,
            'faculty_id' => $this->faculty->id,
            'code' => 'LAB-NEW-1',
            'name' => 'Lab Komputer Baru',
            'floor_number' => 2,
            'type' => 'laboratory',
            'capacity' => 35,
            'description' => 'Fasilitas 35 PC',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'code' => 'LAB-NEW-1',
            'name' => 'Lab Komputer Baru',
            'floor_number' => 2,
            'type' => 'laboratory',
            'capacity' => 35,
        ]);
    }

    public function test_super_admin_can_update_room(): void
    {
        $room = Room::create([
            'building_id' => $this->building->id,
            'code' => 'R-TO-EDIT',
            'name' => 'Ruang Sebelum Edit',
            'floor_number' => 1,
            'type' => 'classroom',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('rooms.update', $room->id), [
            'building_id' => $this->building->id,
            'faculty_id' => $this->faculty->id,
            'code' => 'R-EDITED',
            'name' => 'Ruang Setelah Edit',
            'floor_number' => 2,
            'type' => 'office',
            'capacity' => 20,
            'description' => 'Ruang Dosen Prodi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'code' => 'R-EDITED',
            'name' => 'Ruang Setelah Edit',
            'type' => 'office',
        ]);
    }

    public function test_super_admin_can_delete_empty_room(): void
    {
        $room = Room::create([
            'building_id' => $this->building->id,
            'code' => 'R-TO-DELETE',
            'name' => 'Ruang Hapus',
            'floor_number' => 1,
            'type' => 'classroom',
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('rooms.destroy', $room->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);
    }

    public function test_non_super_admin_cannot_manage_buildings_and_rooms(): void
    {
        $response = $this->actingAs($this->dosen)->post(route('buildings.store'), [
            'campus_id' => $this->campus->id,
            'code' => 'GDG-UNAUTH',
            'name' => 'Gedung Ilegal',
            'total_floors' => 2,
        ]);

        $response->assertForbidden();

        $responseRoom = $this->actingAs($this->dosen)->post(route('rooms.store'), [
            'building_id' => $this->building->id,
            'code' => 'R-UNAUTH',
            'name' => 'Ruang Ilegal',
            'floor_number' => 1,
            'type' => 'classroom',
        ]);

        $responseRoom->assertForbidden();
    }
}
