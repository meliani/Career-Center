<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('final_project_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('final_project_id');
            $table->foreignId('student_id');
            $table->timestamps();

            $table->foreign('final_project_id')->references('id')->on('final_projects')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['final_project_id', 'student_id']);
            $table->unique(['student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('final_project_student');
    }
};
