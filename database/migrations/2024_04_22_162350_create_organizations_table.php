<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address', 255);
            $table->string('city');
            $table->string('country');
            $table->string('office_location', 255)->nullable();
            $table->unsignedBigInteger('central_organization')->nullable();
            $table->foreign('central_organization')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
