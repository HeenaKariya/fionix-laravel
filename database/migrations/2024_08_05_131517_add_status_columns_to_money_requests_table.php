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
        Schema::table('money_requests', function (Blueprint $table) {
            $table->timestamp('admin_status_updated_at')->nullable();
            $table->timestamp('manager_status_updated_at')->nullable();
            $table->timestamp('amanager_status_updated_at')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('amanager_id')->nullable();

            // Adding foreign key constraints if necessary
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('amanager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('money_requests', function (Blueprint $table) {
            if (Schema::hasColumn('money_requests', 'admin_status_updated_at')) {
                $table->dropColumn('admin_status_updated_at');
            }
            if (Schema::hasColumn('money_requests', 'manager_status_updated_at')) {
                $table->dropColumn('manager_status_updated_at');
            }
            if (Schema::hasColumn('money_requests', 'amanager_status_updated_at')) {
                $table->dropColumn('amanager_status_updated_at');
            }
            if (Schema::hasColumn('money_requests', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
            if (Schema::hasColumn('money_requests', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
            if (Schema::hasColumn('money_requests', 'amanager_id')) {
                $table->dropForeign(['amanager_id']);
                $table->dropColumn('amanager_id');
            }
        });
    }
};
