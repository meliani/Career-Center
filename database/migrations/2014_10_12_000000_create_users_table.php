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
            $table->enum('department', Department::getValues())->nullable();
            $table->enum('role', Role::getValues());
            $table->string('email')->unique();
            $table->enum('assigned_program', Program::getValues())->nullable();
            $table->integer('is_enabled')->nullable();
            $table->integer('can_supervise')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
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
