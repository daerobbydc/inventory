<?php

use App\Enums\StockOpnameItemStatus;
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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expected_room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignId('scanned_room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('scanned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scanned_at')->nullable();
            $table->string('status')->default(StockOpnameItemStatus::Missing->value);
            $table->string('condition')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
