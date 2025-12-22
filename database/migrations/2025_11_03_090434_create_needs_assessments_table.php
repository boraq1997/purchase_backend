<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs_assessments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_request_id');
            $table->unsignedBigInteger('request_item_id');
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('needs_status', ['needed', 'not_needed', 'modified'])->default('needed');
            $table->integer('quantity_needed_after_assessment')->nullable();
            $table->text('modified_specifications')->nullable();
            $table->text('reason')->nullable();
            $table->text('recommended_action')->nullable();
            $table->text('notes')->nullable();
            $table->enum('assessment_state', ['draft', 'final'])->default('draft');
            $table->enum('admin_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs_assessments');
    }
};