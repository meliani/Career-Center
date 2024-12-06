<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parrain_id')->nullable()->references('id')->on('internship_agreement_contacts')->nullOnDelete();
            $table->foreignId('external_supervisor_id')->nullable()->references('id')->on('internship_agreement_contacts')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['parrain_id']);
            $table->dropForeign(['external_supervisor_id']);
            $table->dropColumn(['organization_id', 'parrain_id', 'external_supervisor_id']);
        });
    }
};
