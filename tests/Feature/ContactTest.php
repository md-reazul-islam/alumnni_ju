<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_the_contact_form_sends_a_mail(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Jane Alumna',
            'email' => 'jane@example.com',
            'subject' => 'Question about reunion',
            'message' => 'When is the next reunion?',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        Mail::assertSent(ContactMessageMail::class);
    }

    public function test_the_contact_mail_renders_without_error(): void
    {
        // Regression check: ContactMessageMail's template uses <x-mail::message>,
        // which only resolves the "mail" view namespace via the markdown-render
        // pathway. A plain ->view() call renders fine in isolation but throws
        // "No hint path defined for [mail]" only once actually sent/rendered
        // for real — Mail::fake() alone wouldn't have caught the original bug.
        $mailable = new ContactMessageMail('Jane Alumna', 'jane@example.com', 'Subject', 'Body');

        $rendered = $mailable->render();

        $this->assertStringContainsString('New Contact Form Submission', $rendered);
    }
}
