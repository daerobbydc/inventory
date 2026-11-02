<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data fakultas.');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:faculties,code'],
            'name' => ['required', 'string', 'max:255'],
            'dean_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $faculty = Faculty::create($validated);

        return back()->with('success', "Fakultas {$faculty->name} ({$faculty->code}) berhasil didaftarkan ke sistem.");
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data fakultas.');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('faculties', 'code')->ignore($faculty->id)],
            'name' => ['required', 'string', 'max:255'],
            'dean_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $faculty->update($validated);

        return back()->with('success', "Data fakultas {$faculty->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Request $request, Faculty $faculty): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengelola data fakultas.');

        if ($faculty->rooms()->exists()) {
            return back()->with('error', "Gagal menghapus: Masih ada {$faculty->rooms()->count()} ruangan kampus yang terhubung dengan fakultas ini.");
        }

        $name = $faculty->name;
        $faculty->delete();

        return back()->with('success', "Fakultas {$name} berhasil dihapus dari sistem.");
    }
}
