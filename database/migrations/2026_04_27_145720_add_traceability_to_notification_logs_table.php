<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('id');
            $table->integer('attempts')->default(1)->after('message');
            $table->string('status')->default('sent')->after('attempts');

            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn(['batch_id', 'attempts', 'status']);
        });
    }
};
