<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip_nim')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->after('nip_nim');
            $table->string('role')->default(UserRole::Mahasiswa->value)->after('phone');
            $table->string('department')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip_nim', 'phone', 'role', 'department', 'is_active']);
        });
    }
};
