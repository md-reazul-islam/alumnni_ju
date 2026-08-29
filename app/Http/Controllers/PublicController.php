<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\SettingsController;
use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Book;
use App\Models\CarpoolSchedule;
use App\Models\CateringFoodItem;
use App\Models\CateringHomemadeCategory;
use App\Models\CateringHomemadeListing;
use App\Models\CateringProgramCategory;
use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\JobPosting;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\MatrimonyProfile;
use App\Models\MediaAdvocacyCategory;
use App\Models\News;
use App\Models\PublishedMedia;
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
                    'career_opportunities' => JobPosting::approved()->count(),
                    'carpooling_services' => CarpoolSchedule::approved()->upcoming()->count(),
                    'active_events' => Event::published()->upcoming()->count(),
                    'brides_grooms' => MatrimonyProfile::searchable()->count(),
                    'available_books' => Book::available()->count(),
                ],
                'featuredAlumni' => AlumniProfile::with(['user', 'department', 'degree'])
                    ->whereNotNull('verified_at')
                    ->publiclyVisible()
                    ->latest('verified_at')
                    ->limit(4)
                    ->get(),
                'upcomingEvents' => Event::published()->upcoming()->orderBy('event_date')->limit(6)->get(),
                'jobs' => JobPosting::approved()->with('company')->latest('approved_at')->limit(6)->get(),
                'marketplaceCategories' => MarketplaceCategory::active()->orderBy('name')->get(),
                'marketplaceListings' => MarketplaceListing::approved()->with(['category', 'images'])->inRandomOrder()->limit(12)->get(),
                'carpoolSchedules' => CarpoolSchedule::approved()
                    ->whereBetween('departure_date', [today(), today()->addDays(60)])
                    ->with(['car', 'driverProfile.user'])
                    ->orderBy('departure_date')
                    ->orderBy('departure_time')
                    ->limit(300)
                    ->get(),
                'matrimonyProfiles' => MatrimonyProfile::searchable()->with('photos')->inRandomOrder()->limit(8)->get(),
                'cateringCategories' => CateringProgramCategory::active()->withCount('foodItems')->orderBy('sort_order')->orderBy('name')->limit(12)->get(),
                'cateringFoodItems' => CateringFoodItem::active()->with('categories')->inRandomOrder()->limit(12)->get(),
                'cateringHomemadeCategories' => CateringHomemadeCategory::active()->orderBy('name')->get(),
                'cateringHomemadeListings' => CateringHomemadeListing::approved()->with(['category', 'images'])->inRandomOrder()->limit(9)->get(),
                'mediaAdvocacyCategories' => MediaAdvocacyCategory::active()->orderBy('sort_order')->orderBy('name')->limit(6)->get(),
                'publishedMedia' => PublishedMedia::active()->orderBy('sort_order')->latest()->limit(9)->get(),
                'stories' => AlumniStory::published()->with('alumniProfile.user')->latest('published_at')->limit(6)->get(),
                'news' => News::published()->latest('published_at')->limit(6)->get(),
                'gallery' => GalleryPhoto::approved()->with('user')->latest('approved_at')->limit(8)->get(),
                'library' => Book::available()->with('donor')->latest('approved_at')->limit(8)->get(),
            ];
        });

        $data['sectionOrder'] = SettingsController::resolveSectionOrder();

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
