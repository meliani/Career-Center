<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectStudentTable extends Migration
{
    public function up()
    {
        Schema::create('project_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreignId('student_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['project_id', 'student_id']);
            $table->unique(['student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_student');
    }
}
