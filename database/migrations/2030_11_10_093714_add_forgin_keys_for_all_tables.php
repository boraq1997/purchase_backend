<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /** users */
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();
        });

        /** departments */
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_user_id')->references('id')->on('users')->nullOnDelete();
        });

        /** committees */
        Schema::table('committees', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->cascadeOnDelete();
            $table->foreign('manager_user_id')->references('id')->on('users')->nullOnDelete();
        });

        /** committee_user */
        Schema::table('committee_user', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('committee_id')->references('id')->on('committees')->cascadeOnDelete();
        });

        /** vendors */
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        /** purchase_requests */
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('committee_id')->references('id')->on('committees')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('status_action_by')->references('id')->on('users')->nullOnDelete();
        });

        /** request_items */
        Schema::table('request_items', function (Blueprint $table) {
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
        });

        /** estimates */
        Schema::table('estimates', function (Blueprint $table) {
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('request_item_id')->references('id')->on('request_items')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });


        /** estimate_items */
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->foreign('request_item_id')->references('id')->on('request_items')->nullOnDelete();
        });

        /** procurements */
        Schema::table('procurements', function (Blueprint $table) {
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        /** procurement_items */
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->foreign('procurement_id')->references('id')->on('procurements')->cascadeOnDelete();
            $table->foreign('request_item_id')->references('id')->on('request_items')->nullOnDelete();
            $table->foreign('estimate_item_id')->references('id')->on('estimate_items')->nullOnDelete();
            $table->foreign('estimate_id')->references('id')->on('estimates')->nullOnDelete(); // ← جديد
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();         // ← جديد
        });

        /** warehouse_checks */
        Schema::table('warehouse_checks', function (Blueprint $table) {
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('request_item_id')->references('id')->on('request_items')->cascadeOnDelete();
            $table->foreign('checked_by')->references('id')->on('users')->nullOnDelete();
        });

        /** needs_assessments */
        Schema::table('needs_assessments', function (Blueprint $table) {
            // purchase_request_id → purchase_requests.id
            $table->foreign('purchase_request_id')
                ->references('id')
                ->on('purchase_requests')
                ->cascadeOnDelete();

            // request_item_id → request_items.id
            $table->foreign('request_item_id')
                ->references('id')
                ->on('request_items')
                ->cascadeOnDelete();

            // assessed_by → users.id
            $table->foreign('assessed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        /** reports */
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->cascadeOnDelete();
            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $tables = [
            'users', 'departments', 'committees', 'committee_user', 'purchase_requests',
            'request_items', 'estimates', 'estimate_items', 'procurements', 'procurement_items',
            'warehouse_checks', 'needs_assessments', 'reports'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeignKeys();
            });
        }
    }
};