<?php

use App\Enums;
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
        if (Schema::hasTable('students')) {
            return;
        }
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_pfe')->nullable();
            $table->string('organization_name', 191);
            $table->string('adresse', 255);
            $table->string('city', 191);
            $table->string('country', 191);
            $table->string('office_location', 255)->nullable();
            $table->string('parrain_titre', 191);
            $table->string('parrain_nom', 191);
            $table->string('parrain_prenom', 191);
            $table->string('parrain_fonction', 191);
            $table->string('parrain_tel', 191);
            $table->string('parrain_mail', 191);
            $table->string('encadrant_ext_titre', 191);
            $table->string('encadrant_ext_nom', 191);
            $table->string('encadrant_ext_prenom', 191);
            $table->string('encadrant_ext_fonction', 191);
            $table->string('encadrant_ext_tel', 191);
            $table->string('encadrant_ext_mail', 191);
            $table->text('title');
            $table->text('description');
            $table->text('keywords');
            $table->date('starting_at');
            $table->date('ending_at');
            $table->string('remuneration', 191)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('load', 191)->nullable();
            $table->string('int_adviser_name', 191)->nullable();
            $table->unsignedInteger('student_id')->nullable();
            $table->unsignedInteger('year_id')->nullable();
            $table->string('status', 255)->nullable();
            $table->dateTime('announced_at')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->enum('assigned_department', Enums\Department::getArray())->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('signed_at')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
