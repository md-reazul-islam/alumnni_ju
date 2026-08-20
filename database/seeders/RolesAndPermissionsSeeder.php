<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-administrators' => 'system',
            'manage-alumni' => 'alumni',
            'manage-users' => 'system',
            'manage-events' => 'events',
            'manage-jobs' => 'career',
            'manage-news' => 'content',
            'manage-stories' => 'content',
            'manage-announcements' => 'content',
            'manage-sliders' => 'content',
            'manage-gallery' => 'content',
            'manage-library' => 'content',
            'manage-marketplace' => 'marketplace',
            'manage-donations' => 'finance',
            'manage-scholarships' => 'finance',
            'manage-mentorship' => 'mentorship',
            'manage-community' => 'community',
            'manage-reports' => 'reports',
            'manage-settings' => 'system',
            'view-audit-logs' => 'system',
            'moderate-alumni-profiles' => 'moderation',
            'moderate-community' => 'moderation',
            'moderate-comments' => 'moderation',
            'moderate-reports' => 'moderation',
        ];

        foreach ($permissions as $slug => $group) {
            Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => Str::headline($slug), 'group' => $group]
            );
        }

        $roles = [
            Role::SUPER_ADMIN => [
                'name' => 'Super Administrator',
                'description' => 'Full system access across every module.',
                'permissions' => array_keys($permissions),
            ],
            Role::ALUMNI_ADMIN => [
                'name' => 'Alumni Administrator',
                'description' => 'Manages alumni, events, jobs, news, stories, announcements, and community content.',
                'permissions' => [
                    'manage-alumni', 'manage-events', 'manage-jobs', 'manage-news',
                    'manage-stories', 'manage-announcements', 'manage-community', 'manage-gallery', 'manage-library',
                    'manage-marketplace',
                ],
            ],
            Role::MODERATOR => [
                'name' => 'Moderator',
                'description' => 'Moderates alumni profiles, community posts, comments, and reports.',
                'permissions' => [
                    'moderate-alumni-profiles', 'moderate-community', 'moderate-comments', 'moderate-reports',
                ],
            ],
            Role::ALUMNI => [
                'name' => 'Alumni',
                'description' => 'Verified graduate with access to networking, events, jobs, and community features.',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'description' => $data['description']]
            );

            $permissionIds = Permission::whereIn('slug', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
