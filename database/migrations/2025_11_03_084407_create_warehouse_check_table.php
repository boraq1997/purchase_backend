<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id');
            $table->foreignId('request_item_id');
            $table->enum('availability', ['available','partial','unavailable'])->default('available');
            $table->enum('item_condition', ['new', 'used', 'damaged'])->nullable();
            $table->integer('available_quantity')->default(0);
            $table->enum('recommendation', ['provide_from_stock', 'purchase_new', 'reject'])->default('purchase_new');
            $table->text('notes')->nullable();
            $table->foreignId('checked_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_checks');
    }
};