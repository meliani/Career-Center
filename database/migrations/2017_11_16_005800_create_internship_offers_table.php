<?php

use App\Enums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInternshipOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internship_offers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('year_id')->unsigned()->nullable();
            $table->enum('internship_level', array_map(fn($level) => $level->value, Enums\InternshipLevel::cases()));
            $table->string('organization_name')->nullable();
            $table->enum('organization_type', array_map(fn($type) => $type->value, Enums\OrganizationType::cases()));
            $table->bigInteger('organization_id')->nullable();
            $table->string('country')->nullable();
            $table->enum('internship_type', ['OnSite', 'Remote'])->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_occupation')->nullable();
            $table->string('responsible_phone')->nullable();
            $table->string('responsible_email')->nullable();
            $table->text('project_title')->nullable();
            $table->text('project_details')->nullable();
            $table->string('internship_location')->nullable();
            $table->string('keywords')->nullable();
            $table->string('attached_file')->nullable();
            $table->text('application_link')->nullable();
            $table->unsignedInteger('internship_duration')->nullable();
            $table->string('remuneration')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedInteger('workload')->nullable();
            $table->enum('recruiting_type', ['SchoolManaged', 'RecruiterManaged'])->nullable();
            $table->string('application_email')->nullable();
            $table->unsignedInteger('number_of_students_requested')->nullable();
            $table->enum('status', array_map(fn($status) => $status->value, Enums\OfferStatus::cases()));
            $table->boolean('applyable')->nullable();
            $table->date('expire_at')->nullable();
            $table->bigInteger('expertise_field_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('internship_offers');
    }
}
