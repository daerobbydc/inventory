<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->value,
                    'role_label' => $user->role?->label(),
                    'nip_nim' => $user->nip_nim,
                    'department' => $user->department,
                    'can_manage_assets' => $user->canManageAssets(),
                    'can_manage_loans' => $user->canManageLoans(),
                    'can_manage_room_bookings' => $user->canManageRoomBookings(),
                    'can_manage_consumables' => $user->canManageConsumables(),
                    'can_manage_disposals' => $user->canManageDisposals(),
                    'can_manage_settings' => $user->canManageSettings(),
                    'can_perform_audit' => $user->canPerformAudit(),
                    'can_update_maintenance' => $user->canUpdateMaintenance(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
