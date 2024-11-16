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
            $table->string('admin_status')->nullable();
            $table->text('admin_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('money_requests', function (Blueprint $table) {
            if (Schema::hasColumn('money_requests', 'admin_id')) {
                $table->dropForeign(['admin_id']);
            }

            if (Schema::hasColumn('money_requests', 'admin_status')) {
                $table->dropColumn('admin_status');
            }
            if (Schema::hasColumn('money_requests', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
            if (Schema::hasColumn('money_requests', 'admin_id')) {
                $table->dropColumn('admin_id');
            }
        });
    }
};
