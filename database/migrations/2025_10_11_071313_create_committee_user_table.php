<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_user', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('committee_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_user');
    }
};