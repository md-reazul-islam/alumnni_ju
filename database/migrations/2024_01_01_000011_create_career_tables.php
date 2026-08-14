<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table name is "job_postings" (not "jobs") to avoid colliding with Laravel's queue jobs table.
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'internship', 'remote', 'contract', 'freelance'])->default('full_time');
            $table->string('industry')->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('application_url')->nullable();
            $table->string('application_email')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired', 'closed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'employment_type']);
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'])->default('submitted');
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->unique(['job_posting_id', 'user_id']);
        });

        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'job_posting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
