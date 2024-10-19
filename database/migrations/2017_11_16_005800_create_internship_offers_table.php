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
            $table->enum('internship_level', Enums\InternshipLevel::getArray());
            $table->string('organization_name')->nullable();
            $table->enum('organization_type', Enums\OrganizationType::getArray());
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
            $table->enum('recruting_type', ['SchoolManaged', 'RecruiterManaged'])->nullable();
            $table->string('application_email')->nullable();
            $table->unsignedInteger('number_of_students_requested')->nullable();
            $table->enum('status', Enums\OfferStatus::getArray());
            $table->boolean('applyable')->nullable();
            $table->date('expire_at')->nullable();
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
