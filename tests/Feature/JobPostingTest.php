<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_alumnus_can_submit_a_job_for_review(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('jobs.store'), [
            'title' => 'Backend Engineer',
            'company_name' => 'Acme Corp',
            'employment_type' => 'full_time',
            'description' => 'Build and maintain backend services.',
        ]);

        $response->assertRedirect(route('jobs.mine'));
        $this->assertDatabaseHas('job_postings', [
            'title' => 'Backend Engineer',
            'posted_by' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_job_posting_can_include_optional_tags(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('jobs.store'), [
            'title' => 'DevOps Engineer',
            'company_name' => 'Acme Corp',
            'employment_type' => 'full_time',
            'description' => 'Manage cloud infrastructure.',
            'tags' => 'aws, kubernetes, remote',
        ]);

        $response->assertRedirect(route('jobs.mine'));
        $this->assertDatabaseHas('job_postings', [
            'title' => 'DevOps Engineer',
            'tags' => 'aws, kubernetes, remote',
        ]);
    }

    public function test_pending_jobs_are_not_publicly_visible(): void
    {
        $job = JobPosting::factory()->create();

        $response = $this->get(route('jobs.show', $job));

        $response->assertNotFound();
    }

    public function test_admin_can_approve_a_pending_job(): void
    {
        $admin = User::factory()->admin()->create();
        $job = JobPosting::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.jobs.approve', $job));

        $response->assertRedirect();
        $this->assertSame('approved', $job->fresh()->status);
    }

    public function test_approved_jobs_appear_in_public_search(): void
    {
        $job = JobPosting::factory()->approved()->create(['title' => 'Data Analyst']);

        $response = $this->get(route('jobs.index', ['search' => 'Data Analyst']));

        $response->assertOk();
        $response->assertSee('Data Analyst');
    }

    public function test_non_admin_cannot_approve_jobs(): void
    {
        $user = User::factory()->create();
        $job = JobPosting::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.jobs.approve', $job));

        $response->assertForbidden();
    }
}
