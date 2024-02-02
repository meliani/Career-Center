<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorJuryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professor_jury', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('users', 'id');
            $table->foreignId('jury_id')->constrained();
            $table->string('role');
            $table->boolean('is_president')->default(false);
            $table->timestamps();
            $table->unique(['professor_id', 'jury_id']);

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