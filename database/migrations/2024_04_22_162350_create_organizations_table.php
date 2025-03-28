<?php

use App\Enums\OrganizationStatus;
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
            $table->string('address', 255)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('parent_organization')->nullable();
            $table->foreign('parent_organization')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null')->nullable();
            $table->enum('status', OrganizationStatus::getValues())->nullable();
            $table->unsignedBigInteger('created_by_student_id')->nullable();
            $table->foreign('created_by_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('set null')->nullable();
            $table->unsignedBigInteger('industry_information_id')->nullable();
            $table->foreign('industry_information_id')
                ->references('id')
                ->on('industry_information')
                ->onDelete('set null')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
