<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactRequest $request): RedirectResponse
    {
        $recipient = Setting::get('institution', 'email', config('mail.from.address'));

        Mail::to($recipient)->send(new ContactMessageMail(
            $request->string('name'),
            $request->string('email'),
            $request->string('subject'),
            $request->string('message'),
        ));

        return back()->with('status', 'Thanks for reaching out! Our team will get back to you within 1-2 business days.');
    }
}
