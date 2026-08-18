<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\JobPosting;
use App\Models\News;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $data = Cache::remember('homepage.content', now()->addMinutes(15), function () {
            return [
                'sliders' => Slider::active()->ordered()->get(),
                'stats' => [
                    'total_alumni' => User::whereHas('role', fn ($q) => $q->where('slug', 'alumni'))->count(),
                    'verified_alumni' => User::verified()->whereHas('role', fn ($q) => $q->where('slug', 'alumni'))->count(),
                    'countries' => AlumniProfile::whereNotNull('country')->distinct('country')->count('country'),
                    'active_events' => Event::published()->upcoming()->count(),
                ],
                'featuredAlumni' => AlumniProfile::with(['user', 'department', 'degree'])
                    ->whereNotNull('verified_at')
                    ->publiclyVisible()
                    ->latest('verified_at')
                    ->limit(4)
                    ->get(),
                'upcomingEvents' => Event::published()->upcoming()->orderBy('event_date')->limit(3)->get(),
                'jobs' => JobPosting::approved()->with('company')->latest('approved_at')->limit(4)->get(),
                'stories' => AlumniStory::published()->with('alumniProfile.user')->latest('published_at')->limit(3)->get(),
                'news' => News::published()->latest('published_at')->limit(3)->get(),
                'gallery' => GalleryPhoto::approved()->with('user')->latest('approved_at')->limit(8)->get(),
            ];
        });

        return view('public.home', $data);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }
}
