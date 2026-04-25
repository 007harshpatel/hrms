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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('experience')->nullable();
            $table->string('portfolio')->nullable();
            $table->text('coverLetter')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Request, 1=Ready For Interview, 3=Requirement Not Match, 4=Interview Schedule, 5=Reject By Interviewer, 6=Reject By HR, 7=Ready For Join, 8=Joined, 9=Reject By Candidate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
