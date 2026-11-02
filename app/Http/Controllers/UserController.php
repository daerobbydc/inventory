<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang menambahkan pengguna.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'nip_nim' => ['nullable', 'string', 'max:50', 'unique:users,nip_nim'],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string'],
            'department' => ['nullable', 'string', 'max:100'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nip_nim' => $validated['nip_nim'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'department' => $validated['department'],
            'faculty_id' => $validated['faculty_id'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', "Pengguna {$validated['name']} berhasil ditambahkan ke sistem.");
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengubah data pengguna.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'nip_nim' => ['nullable', 'string', 'max:50', 'unique:users,nip_nim,'.$user->id],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string'],
            'department' => ['nullable', 'string', 'max:100'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
        ]);

        $user->update($validated);

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengubah status akun.');

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun pengguna {$user->name} telah {$statusStr}.");
    }

    /**
     * Send simulated WhatsApp notification.
     */
    public function testWhatsApp(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang menguji gateway WhatsApp.');

        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        // Simulating WhatsApp Gateway API (Fonnte/Wablas)
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);

        return back()->with('success', "📱 [WhatsApp Gateway UNIVAL] Notifikasi simulasi berhasil dikirim ke nomor +{$cleanPhone}. Pesan: \"{$validated['message']}\"");
    }
}
