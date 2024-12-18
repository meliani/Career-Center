<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeslots', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_available')->default(false);
            $table->bigInteger('year_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('timeslots', function (Blueprint $table) {
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timeslots');
    }
}
