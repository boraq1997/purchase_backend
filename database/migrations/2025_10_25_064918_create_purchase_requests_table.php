<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('department_id')->nullable();
            $table->foreignId('committee_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_estimated_cost', 14, 2)->nullable();
            $table->enum('status_type', ['draft', 'pending', 'approved', 'rejected', 'completed'])->default('draft');
            $table->foreignId('status_action_by')->nullable();
            $table->enum('status_role', ['alameen', 'alameenAssestant'])->nullable();
            $table->timestamp('status_date')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};