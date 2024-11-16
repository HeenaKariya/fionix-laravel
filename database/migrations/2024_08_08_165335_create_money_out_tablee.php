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
        Schema::create('money_out', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id');
            $table->string('from');
            $table->string('to');
            $table->string('payment_type');
            $table->datetime('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add foreign key constraints if applicable
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_out');
    }
};
