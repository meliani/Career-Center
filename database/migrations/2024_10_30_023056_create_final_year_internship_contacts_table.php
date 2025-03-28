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
        Schema::create('final_year_internship_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('title', Enums\Title::getValues());
            $table->string('first_name');
            $table->string('last_name');
            $table->string('function')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('role', Enums\OrganizationContactRole::getValues());
            $table->foreignId('organization_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_year_internship_contacts');
    }
};
