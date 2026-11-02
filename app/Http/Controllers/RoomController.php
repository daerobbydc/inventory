<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data ruangan.');

        $validated = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'code' => ['required', 'string', 'max:30', 'unique:rooms,code'],
            'name' => ['required', 'string', 'max:255'],
            'floor_number' => ['required', 'integer', 'min:1', 'max:50'],
            'type' => ['required', 'string', 'in:classroom,laboratory,office,hall,storage,other'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $room = Room::create($validated);

        return back()->with('success', "Ruangan {$room->name} ({$room->code}) berhasil ditambahkan ke sistem.");
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data ruangan.');

        $validated = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'code' => ['required', 'string', 'max:30', Rule::unique('rooms', 'code')->ignore($room->id)],
            'name' => ['required', 'string', 'max:255'],
            'floor_number' => ['required', 'integer', 'min:1', 'max:50'],
            'type' => ['required', 'string', 'in:classroom,laboratory,office,hall,storage,other'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $room->update($validated);

        return back()->with('success', "Data ruangan {$room->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Request $request, Room $room): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data ruangan.');

        if ($room->assets()->exists()) {
            return back()->with('error', "Gagal menghapus: Ruangan masih terhubung dengan {$room->assets()->count()} unit aset inventaris. Pindahkan aset terlebih dahulu.");
        }

        if ($room->bookings()->exists()) {
            return back()->with('error', 'Gagal menghapus: Ruangan masih memiliki riwayat reservasi ruangan.');
        }

        $name = $room->name;
        $room->delete();

        return back()->with('success', "Ruangan {$name} berhasil dihapus dari sistem.");
    }
}
