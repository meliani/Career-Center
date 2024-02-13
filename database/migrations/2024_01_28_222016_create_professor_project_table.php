<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums;
class CreateProfessorProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professor_project', function (Blueprint $table) {
            // $table->id();
            // professor id and jury id are the primarykey
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->enum('jury_role', Enums\JuryRole::getArray());
            $table->boolean('is_president')->default(false);
            $table->timestamps();
            $table->primary(['professor_id', 'project_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professor_project');
    }
}