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
            $table->unsignedBigInteger('procurement_id');
            $table->unsignedBigInteger('request_item_id')->nullable();
            $table->unsignedBigInteger('estimate_item_id')->nullable();
            $table->unsignedBigInteger('estimate_id')->nullable();
            $table->string('item_name');
            $table->unsignedBigInteger('unit_id')->nullable();
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