<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJuryProfessorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jury_professor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jury_id');
            $table->foreignId('professor_id');
            $table->boolean('is_president')->default(false);
            $table->enum('role', ['Supervisor', 'Reviewer'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jury_professor');
    }
}