<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ReferenceDataSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@alumni.test'],
            [
                'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => 'password',
                'status' => User::STATUS_VERIFIED,
            ]
        );

        // email_verified_at is deliberately excluded from $fillable (it must never be
        // mass-assignable from user input), so trusted internal writes use forceFill().
        $admin->forceFill(['email_verified_at' => now()])->save();
    }
}
