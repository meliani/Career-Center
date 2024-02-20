<?php

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
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
            //  Enum department from enum class
            $table->enum('department', array_map(fn ($case) => $case->name, Department::cases()))->nullable();
            $table->enum('role', array_map(fn ($case) => $case->name, Role::cases()))->default('Professor');
            $table->string('email')->unique();
            $table->enum('assigned_program', array_map(fn ($case) => $case->name, Program::cases()))->nullable();
            $table->integer('is_enabled')->default('0');
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
