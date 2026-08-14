<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Academic information
            $table->string('student_id')->unique();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('degree_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('major')->nullable();
            $table->unsignedSmallInteger('admission_year')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable()->index();
            $table->string('batch')->nullable();

            // Personal information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('country')->nullable()->index();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('cover_image')->nullable();

            // Professional information
            $table->string('job_title')->nullable();
            $table->string('organization')->nullable();
            $table->string('industry')->nullable()->index();
            $table->enum('employment_type', ['full_time', 'part_time', 'self_employed', 'internship', 'unemployed', 'student'])->nullable();
            $table->string('work_location')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('website_url')->nullable();

            // Profile content
            $table->text('bio')->nullable();
            $table->enum('profile_visibility', ['public', 'alumni', 'private'])->default('alumni');
            $table->unsignedTinyInteger('profile_completion')->default(0);

            // Verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
