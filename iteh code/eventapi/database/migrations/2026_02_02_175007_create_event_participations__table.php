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
        Schema::create('event_participations', function (Blueprint $table) {
            $table->id("idParticipation");
            $table->unsignedBigInteger('idEvent');
            $table->unsignedBigInteger('idUser');
            $table->enum('status', ['REGISTERED', 'CANCELLED', 'ATTENDED'])
                  ->default('REGISTERED');
            $table->dateTime('registeredAt');
            $table->dateTime('cancelledAt')->nullable();
            $table->dateTime('attendanceMarkedAt')->nullable();
            $table->foreign('idEvent')
                  ->references('idEvent')
                  ->on('events')
                  ->onDelete('cascade');
            $table->foreign('idUser')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participations');
    }
};
