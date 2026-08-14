@php
    $copy = match ($code) {
        403 => ['title' => 'Access Denied', 'message' => "You don't have permission to view this page."],
        404 => ['title' => 'Page Not Found', 'message' => "The page you're looking for doesn't exist or has been moved."],
        419 => ['title' => 'Session Expired', 'message' => 'Your session has expired. Please refresh and try again.'],
        429 => ['title' => 'Too Many Requests', 'message' => "You've made too many requests. Please wait a moment and try again."],
        503 => ['title' => 'Down for Maintenance', 'message' => "We're performing scheduled maintenance. Please check back shortly."],
        default => ['title' => 'Something Went Wrong', 'message' => 'An unexpected error occurred. Our team has been notified.'],
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $copy['title'] }} — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f8fafc; color: #1e293b;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .card { max-width: 28rem; text-align: center; }
        .badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 3.5rem; height: 3.5rem; border-radius: 9999px;
            background: #101b35; color: #edc55a; font-weight: 700; font-size: 1.25rem;
            margin: 0 auto 1.5rem;
        }
        .code { font-size: 0.875rem; font-weight: 600; color: #64748b; letter-spacing: 0.05em; text-transform: uppercase; }
        h1 { margin-top: 0.5rem; font-size: 1.5rem; font-weight: 700; color: #101b35; }
        p { margin-top: 0.75rem; color: #64748b; line-height: 1.6; }
        .actions { margin-top: 2rem; display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-block; padding: 0.625rem 1.25rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 600; text-decoration: none;
        }
        .btn-primary { background: #1c2f56; color: #fff; }
        .btn-secondary { background: #fff; color: #1c2f56; border: 1px solid #cbd5e1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">{{ $code }}</div>
        <p class="code">Error {{ $code }}</p>
        <h1>{{ $copy['title'] }}</h1>
        <p>{{ $copy['message'] }}</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        </div>
    </div>
</body>
</html>
