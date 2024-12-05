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
            $table->enum('role', Enums\OrganizationContactRole::getArray());
            $table->foreignId('organization_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apprenticeship_agreement_contacts');
    }
}
