<?php

namespace App\Services;

use App\Models\AlumniProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AlumniRegistrationService
{
    public function __construct(protected ProfileCompletionCalculator $completionCalculator)
    {
    }

    public function register(array $data, ?UploadedFile $photo): User
    {
        return DB::transaction(function () use ($data, $photo) {
            $avatarPath = $photo?->store('avatars', 'public');

            $user = User::create([
                'role_id' => Role::where('slug', Role::ALUMNI)->value('id'),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'avatar' => $avatarPath,
                'status' => User::STATUS_PENDING,
            ]);

            $profile = AlumniProfile::create([
                'user_id' => $user->id,
                'student_id' => $data['student_id'],
                'department_id' => $data['department_id'],
                'program_id' => $data['program_id'] ?? null,
                'degree_id' => $data['degree_id'],
                'campus_id' => $data['campus_id'] ?? null,
                'major' => $data['major'] ?? null,
                'admission_year' => $data['admission_year'],
                'graduation_year' => $data['graduation_year'],
                'batch' => $data['batch'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'country' => $data['country'],
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'organization' => $data['organization'] ?? null,
                'industry' => $data['industry'] ?? null,
                'employment_type' => $data['employment_type'] ?? null,
                'work_location' => $data['work_location'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'website_url' => $data['website_url'] ?? null,
                'profile_visibility' => AlumniProfile::VISIBILITY_ALUMNI,
            ]);

            if (! empty($data['interests'])) {
                $profile->interests()->sync($data['interests']);
            }

            $this->completionCalculator->refresh($profile);

            event(new Registered($user));

            return $user;
        });
    }
}
