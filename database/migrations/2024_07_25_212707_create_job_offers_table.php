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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name')->nullable();
            $table->enum('organization_type', Enums\OrganizationType::getArray());
            $table->bigInteger('organization_id')->nullable();
            $table->string('country')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_occupation')->nullable();
            $table->string('responsible_phone')->nullable();
            $table->string('responsible_email')->nullable();
            $table->text('job_title')->nullable();
            $table->text('job_details')->nullable();
            $table->string('is_remote')->nullable();
            $table->string('job_location')->nullable();
            $table->string('keywords')->nullable();
            $table->string('attached_file')->nullable();
            $table->text('application_link')->nullable();
            $table->unsignedInteger('job_duration')->nullable();
            $table->string('remuneration')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedInteger('workload')->nullable();
            $table->enum('recruiting_type', ['SchoolManaged', 'RecruiterManaged'])->nullable();
            $table->string('application_email')->nullable();
            $table->enum('status', Enums\OfferStatus::getArray());
            $table->boolean('applyable')->nullable();
            $table->date('expire_at')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
