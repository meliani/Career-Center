<?php

use App\Enums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprenticeshipsTable extends Migration
{
    public function up()
    {
        Schema::create('apprenticeships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('student_id');
            $table->unsignedInteger('year_id');
            $table->unsignedInteger('project_id')->nullable();
            $table->string('status', 255)->nullable();
            $table->dateTime('announced_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->enum('assigned_department', Enums\Department::toArray())->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('signed_at')->nullable();
            $table->text('observations')->nullable();
            $table->foreignId('organization_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->dateTime('starting_at')->nullable();
            $table->dateTime('ending_at')->nullable();
            $table->decimal('remuneration', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->integer('workload')->nullable();
            $table->foreignId('parrain_id');
            $table->foreignId('supervisor_id');
            $table->foreignId('tutor_id')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apprenticeships');
    }
}