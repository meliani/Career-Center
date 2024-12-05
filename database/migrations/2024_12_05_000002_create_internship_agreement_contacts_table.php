<?php

use App\Enums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('internship_agreement_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->enum('contact_type', Enums\EntrepriseContactCategory::getValues());
            $table->enum('title', Enums\Title::getValues());
            $table->string('first_name');
            $table->string('last_name');
            $table->string('function')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('internship_agreement_contacts');
    }
};
