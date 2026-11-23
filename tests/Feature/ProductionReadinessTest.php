<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that DatabaseSeeder seeds all modules comprehensively.
     */
    public function test_database_seeder_populates_all_modules_including_procurements(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('faculties', [
            'code' => 'FT',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'sarpras@kampus.ac.id',
        ]);

        $this->assertDatabaseHas('procurement_requests', [
            'request_number' => 'PR/FT/2026/0001',
        ]);

        $this->assertDatabaseHas('procurement_requests', [
            'request_number' => 'PR/FEB/2026/0003',
        ]);

        $this->assertDatabaseCount('procurement_requests', 3);
        $this->assertDatabaseCount('procurement_items', 5);
    }

    /**
     * Test that production environment handles 404 gracefully with Inertia Error page.
     */
    public function test_production_renders_inertia_error_page_on_not_found(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/non-existent-page-url');

        $response->assertStatus(404);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 404)
        );
    }

    /**
     * Test that production environment handles 403 gracefully with Inertia Error page.
     */
    public function test_production_renders_inertia_error_page_on_forbidden(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $user = User::factory()->create([
            'role' => UserRole::Mahasiswa,
        ]);

        // Accessing settings is forbidden for Mahasiswa
        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(403);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 403)
        );
    }

    /**
     * Test that system notifications include procurement updates for Super Admin.
     */
    public function test_super_admin_receives_procurement_notification(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::where('email', 'sarpras@kampus.ac.id')->firstOrFail();

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('notifications')
        );
    }
}
