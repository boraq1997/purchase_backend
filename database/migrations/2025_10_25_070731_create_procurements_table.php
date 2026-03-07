<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id');
            //$table->foreignId('estimate_id')->nullable();
            $table->string('reference_no')->nullable()->unique();
            $table->date('purchase_date')->nullable();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};