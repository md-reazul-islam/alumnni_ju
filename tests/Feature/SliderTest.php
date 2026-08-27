<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SliderTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_slide(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.sliders.store'), [
            'title' => 'Homecoming 2026',
            'subtitle' => 'Join us for a weekend of celebration.',
            'image' => UploadedFile::fake()->image('hero.jpg', 1200, 400),
            'button_text' => 'Register Now',
            'button_url' => '/events',
        ]);

        $response->assertRedirect(route('admin.sliders.index'));
        $this->assertDatabaseHas('sliders', ['title' => 'Homecoming 2026', 'is_active' => true]);

        $slide = Slider::first();
        Storage::disk('public')->assertExists($slide->image);
    }

    public function test_alumni_administrator_cannot_manage_sliders(): void
    {
        $role = Role::firstOrCreate(['slug' => Role::ALUMNI_ADMIN], ['name' => 'Alumni Administrator']);
        $alumniAdmin = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($alumniAdmin)->get(route('admin.sliders.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_away_from_slider_management(): void
    {
        $response = $this->get(route('admin.sliders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_can_update_a_slide(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $slide = Slider::factory()->create(['title' => 'Original Title']);

        $response = $this->actingAs($admin)->put(route('admin.sliders.update', $slide), [
            'title' => 'Updated Title',
            'position' => 5,
        ]);

        $response->assertRedirect(route('admin.sliders.index'));
        $this->assertSame('Updated Title', $slide->fresh()->title);
        $this->assertSame(5, $slide->fresh()->position);
    }

    public function test_super_admin_can_delete_a_slide(): void
    {
        $admin = User::factory()->admin()->create();
        $slide = Slider::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.sliders.destroy', $slide));

        $response->assertRedirect();
        $this->assertSoftDeleted($slide);
    }

    public function test_homepage_only_shows_active_slides_in_order(): void
    {
        Slider::factory()->create(['title' => 'Second', 'position' => 2, 'is_active' => true]);
        Slider::factory()->create(['title' => 'Hidden', 'position' => 0, 'is_active' => false]);
        Slider::factory()->create(['title' => 'First', 'position' => 1, 'is_active' => true]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder(['First', 'Second']);
        $response->assertDontSee('Hidden');
    }
}
