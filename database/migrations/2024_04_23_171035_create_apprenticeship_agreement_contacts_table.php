<?php

use App\Enums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprenticeshipAgreementContactsTable extends Migration
{
    public function up()
    {
        Schema::create('apprenticeship_agreement_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('title', Enums\Title::getArray());
            $table->string('first_name');
            $table->string('last_name');
            $table->string('function')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            /* apprenticeships: This table will store general information about each apprenticeship. Fields might include id, student_id, year_id, project_id, status, announced_at, validated_at, assigned_department, received_at, signed_at, observations, created_at, updated_at, and deleted_at. */
            $table->enum('role', Enums\OrganizationContactRole::getArray());
            $table->foreignId('organization_id')->constrained()->onDelete('restrict');
            $table->foreignId('apprenticeship_id')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apprenticeship_agreement_contacts');
    }
}
