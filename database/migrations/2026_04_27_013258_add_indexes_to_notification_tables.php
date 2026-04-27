<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes to tables that are frequently queried or filtered.
     *
     * notification_logs: indexed by user_id (FK lookups), category, channel (filtering),
     *                    and created_at (sorting newest-to-oldest).
     * categories / channels: indexed by name (used in validation and seeding lookups).
     */
    public function up(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category');
            $table->index('channel');
            $table->index('created_at');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category']);
            $table->dropIndex(['channel']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
