<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('final_year_internship_contacts');
        Schema::dropIfExists('apprenticeship_agreement_contacts');
    }

    public function down()
    {
        // Optionally, recreate the old tables if necessary
    }
};
