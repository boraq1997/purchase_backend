<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id');
            $table->enum('report_type', ['full_review', 'procurement_recommendation'])->default('full_review');
            $table->json('data')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('generated_by')->nullable();
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};