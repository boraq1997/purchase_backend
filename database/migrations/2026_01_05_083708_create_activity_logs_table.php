<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            /**
             * =============================
             * Actor (Who did the action)
             * =============================
             */
            $table->string('actor_type'); // Admin, User, Student
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('actor_role')->nullable();

            /**
             * =============================
             * Action (What happened)
             * =============================
             */
            $table->string('action'); // create, update, delete, login
            $table->string('action_label')->nullable(); // human readable
            $table->string('status')->default('success'); // success, failed
            $table->string('severity')->default('info'); // info, warning, critical

            /**
             * =============================
             * Subject (On what)
             * =============================
             */
            $table->string('subject_type')->nullable(); // Model name
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_identifier')->nullable(); // student_code, slug

            /**
             * =============================
             * Change tracking
             * =============================
             */
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changed_fields')->nullable();

            /**
             * =============================
             * Request context
             * =============================
             */
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // web, mobile
            $table->string('platform')->nullable(); // ios, android, web
            $table->string('request_id')->nullable();

            /**
             * =============================
             * System scope
             * =============================
             */
            $table->string('module')->nullable(); // students, institutes
            $table->string('route')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST

            /**
             * =============================
             * Performance & metadata
             * =============================
             */
            $table->integer('duration_ms')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            /**
             * =============================
             * Indexes (for performance)
             * =============================
             */
            $table->index(['actor_type', 'actor_id']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};