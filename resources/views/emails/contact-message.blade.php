<x-mail::message>
# New Contact Form Submission

**From:** {{ $senderName }} ({{ $senderEmail }})

**Subject:** {{ $subjectLine }}

{{ $body }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
