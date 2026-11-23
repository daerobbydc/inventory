<?php

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
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('fiscal_year', 20)->default('2026')->index();
            $table->string('budget_source')->default('Dana Yayasan Al-Khairiyah');
            $table->string('urgency', 20)->default('reguler'); // reguler, urgent, critical
            $table->decimal('total_estimated_cost', 15, 2)->default(0);
            $table->text('justification')->nullable();
            $table->string('status', 30)->default('submitted')->index(); // draft, submitted, under_review, approved, rejected, ordered, received, completed
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('purchase_order_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('receipt_date')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_request_id')->constrained('procurement_requests')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('item_name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price_estimate', 15, 2)->default(0);
            $table->decimal('total_price_estimate', 15, 2)->default(0);
            $table->foreignId('target_room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->boolean('is_converted_to_asset')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
        Schema::dropIfExists('procurement_requests');
    }
};
