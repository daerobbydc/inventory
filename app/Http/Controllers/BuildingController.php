<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuildingController extends Controller
{
    /**
     * Store a newly created building in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data gedung.');

        $validated = $request->validate([
            'campus_id' => ['required', 'exists:campuses,id'],
            'code' => ['required', 'string', 'max:20', 'unique:buildings,code'],
            'name' => ['required', 'string', 'max:255'],
            'total_floors' => ['required', 'integer', 'min:1', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $building = Building::create($validated);

        return back()->with('success', "Gedung {$building->name} ({$building->code}) berhasil ditambahkan ke sistem.");
    }

    /**
     * Update the specified building in storage.
     */
    public function update(Request $request, Building $building): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data gedung.');

        $validated = $request->validate([
            'campus_id' => ['required', 'exists:campuses,id'],
            'code' => ['required', 'string', 'max:20', Rule::unique('buildings', 'code')->ignore($building->id)],
            'name' => ['required', 'string', 'max:255'],
            'total_floors' => ['required', 'integer', 'min:1', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $building->update($validated);

        return back()->with('success', "Data gedung {$building->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified building from storage.
     */
    public function destroy(Request $request, Building $building): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data gedung.');

        if ($building->rooms()->exists()) {
            return back()->with('error', "Gagal menghapus: Gedung masih memiliki {$building->rooms()->count()} ruangan terdaftar. Pindahkan atau hapus ruangan terlebih dahulu.");
        }

        $name = $building->name;
        $building->delete();

        return back()->with('success', "Gedung {$name} berhasil dihapus dari sistem.");
    }
}
