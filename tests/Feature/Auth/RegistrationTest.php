<?php

namespace Tests\Feature\Auth;

use App\Models\Degree;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_alumni_can_register_and_are_placed_in_pending_status(): void
    {
        $department = Department::factory()->create();
        $degree = Degree::factory()->create();

        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'country' => 'United States',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_id' => 'STU-2024-001',
            'department_id' => $department->id,
            'degree_id' => $degree->id,
            'admission_year' => 2018,
            'graduation_year' => 2022,
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.pending'));

        $user = User::where('email', 'jane.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAlumni());
        $this->assertTrue($user->isPending());
        $this->assertNotNull($user->alumniProfile);
        $this->assertEquals('STU-2024-001', $user->alumniProfile->student_id);
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $department = Department::factory()->create();
        $degree = Degree::factory()->create();

        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'country' => 'United States',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_id' => 'STU-2024-002',
            'department_id' => $department->id,
            'degree_id' => $degree->id,
            'admission_year' => 2018,
            'graduation_year' => 2022,
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
    }
}
