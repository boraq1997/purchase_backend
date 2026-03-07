<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('estimate_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('purchase_price', 14, 2)->default(0);
            $table->decimal('estimate_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->virtualAs('quantity * purchase_price');
            $table->decimal('difference', 14, 2)->virtualAs('purchase_price - estimate_price');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};