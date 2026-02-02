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
        Schema::create('events', function (Blueprint $table) {
            $table->id('idEvent');
        $table->unsignedBigInteger('idUser');
$table->unsignedBigInteger('idCategory');

$table->foreign('idUser')
      ->references('id')
      ->on('users')
      ->onDelete('cascade');

$table->foreign('idCategory')
      ->references('idCategory')
      ->on('categories')
      ->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location');
            $table->dateTime('startAt');
            $table->dateTime('endAt');
            $table->integer('capacity')->unsigned();
              $table->enum('status', ['ACTIVE', 'CANCELLED', 'DRAFT'])
              ->default('DRAFT');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
