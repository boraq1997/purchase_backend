<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('review_stage', [
                'initial',
                'stores_reviewed',
                'needs_reviewed',
                'report_generated',
                'finalized'
            ])->default('initial')->after('status_type');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('review_stage');
        });
    }
};