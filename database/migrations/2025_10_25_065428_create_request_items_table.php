<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id');
            $table->string('item_name');
            $table->string('specifications')->nullable();
            $table->integer('quantity')->default(1);
            $table->foreignId('unit_id')->nullable();
            $table->decimal('estimated_unit_price', 12, 2)->nullable();
            $table->decimal('total_estimated_price', 14, 2)->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};