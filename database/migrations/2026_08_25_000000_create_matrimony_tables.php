<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('managed_by_relation', ['self', 'parent', 'guardian', 'sibling', 'relative'])->default('self');

            $table->string('full_name');
            $table->string('display_name')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->enum('marital_status', ['never_married', 'divorced', 'widowed', 'separated'])->default('never_married');

            $table->string('religion');
            $table->string('sect')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('nationality');
            $table->string('country');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('visa_status')->nullable();
            $table->string('education_level');
            $table->text('education_details')->nullable();
            $table->string('occupation');
            $table->text('occupation_details')->nullable();
            $table->text('about_me')->nullable();

            $table->string('income_range')->nullable();
            $table->text('physical_description')->nullable();
            $table->text('family_details')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->unsignedTinyInteger('preferred_age_min')->nullable();
            $table->unsignedTinyInteger('preferred_age_max')->nullable();
            $table->string('preferred_country')->nullable();
            $table->text('preferred_partner_details')->nullable();

            $table->enum('photo_visibility', ['public', 'private'])->default('private');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'suspended'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->unsignedTinyInteger('profile_completion')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'is_active', 'gender']);
        });

        Schema::create('matrimony_profile_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matrimony_profile_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('matrimony_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matrimony_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requester_profile_id')->nullable()->constrained('matrimony_profiles')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined', 'withdrawn', 'expired'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['matrimony_profile_id', 'requested_by']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('matrimony_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matrimony_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'matrimony_profile_id']);
        });

        Schema::create('matrimony_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_id']);
        });

        Schema::create('matrimony_profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matrimony_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('viewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['matrimony_profile_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrimony_profile_views');
        Schema::dropIfExists('matrimony_blocks');
        Schema::dropIfExists('matrimony_favorites');
        Schema::dropIfExists('matrimony_interests');
        Schema::dropIfExists('matrimony_profile_photos');
        Schema::dropIfExists('matrimony_profiles');
    }
};
