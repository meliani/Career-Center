<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('title', ['Mr', 'Mrs']);
            $table->string('name');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('department', ['SC', 'RIM']);
            $table->enum('role', ['ProgramCoordinator', 'Professor', 'InternshipSupervisor', 'HeadOfDepartment', 'Administrator', 'SuperAdministrator'])->default('Professor');
            $table->string('email')->unique();
            $table->enum('program_coordinator', ['SESNUM', 'SUD']);
            $table->number('is_enabled')->default('0');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
