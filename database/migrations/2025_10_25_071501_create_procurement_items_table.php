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
            $table->foreignId('procurement_id');
            $table->foreignId('request_item_id')->nullable();
            $table->foreignId('estimate_item_id')->nullable();
            $table->string('item_name');
            $table->enum('unit', [
                'piece',     // قطعة
                'box',       // صندوق
                'carton',    // كارتون
                'pack',      // حزمة
                'set',       // طقم
                'kg',        // كيلوجرام
                'g',         // غرام
                'ton',       // طن
                'meter',     // متر
                'cm',        // سنتيمتر
                'roll',      // لفة
                'liter',     // لتر
                'ml',        // ملي لتر
            ])->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->virtualAs('quantity * unit_price');
            $table->decimal('difference', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};