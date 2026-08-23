<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\LibraryController as AdminLibraryController;
use App\Http\Controllers\Admin\ModeratorController as AdminModeratorController;
use App\Http\Controllers\Alumni\LibraryController as AlumniLibraryController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\CarpoolDriverController as AdminCarpoolDriverController;
use App\Http\Controllers\Admin\CarpoolScheduleController as AdminCarpoolScheduleController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\CommunityController as AdminCommunityController;
use App\Http\Controllers\Admin\MarketplaceCategoryController as AdminMarketplaceCategoryController;
use App\Http\Controllers\Admin\MarketplaceListingController as AdminMarketplaceListingController;
use App\Http\Controllers\Admin\MarketplaceOrderController as AdminMarketplaceOrderController;
use App\Http\Controllers\Admin\MarketplaceReportController as AdminMarketplaceReportController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ScholarshipController as AdminScholarshipController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\AlumniDirectoryController;
use App\Http\Controllers\Alumni\DashboardController;
use App\Http\Controllers\Alumni\GalleryController as AlumniGalleryController;
use App\Http\Controllers\Alumni\ProfileController as AlumniProfileController;
use App\Http\Controllers\Alumni\ProfileItemController;
use App\Http\Controllers\Alumni\StoryController as AlumniStoryController;
use App\Http\Controllers\CarpoolBookingController;
use App\Http\Controllers\CarpoolCarController;
use App\Http\Controllers\CarpoolDriverController;
use App\Http\Controllers\CarpoolScheduleController;
use App\Http\Controllers\CarpoolSearchController;
use App\Http\Controllers\CarpoolStripeWebhookController;
use App\Http\Controllers\DriverBookingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\Admin\MentorshipController as AdminMentorshipController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\VerificationPendingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::post('/assistant/chat', [AssistantController::class, 'chat'])
    ->middleware('throttle:15,1')
    ->name('assistant.chat');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/privacy-policy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PublicController::class, 'terms'])->name('terms');

// Public content pages — guest-viewable, with richer behavior layered on for authenticated alumni in later phases.
Route::prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/', [AlumniDirectoryController::class, 'index'])->name('directory');
    Route::get('/{user}', [AlumniDirectoryController::class, 'show'])->name('profile.show');
});

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{event:slug}', [EventController::class, 'show'])->name('show');
});

Route::prefix('careers')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [JobController::class, 'create'])->name('create');
        Route::post('/create', [JobController::class, 'store'])->name('store');
        Route::get('/mine', [JobController::class, 'mine'])->name('mine');
        Route::get('/saved', [JobController::class, 'saved'])->name('saved');
        Route::post('/{job:slug}/apply', [JobController::class, 'apply'])->name('apply');
        Route::post('/{job:slug}/save', [JobController::class, 'toggleSave'])->name('save');
    });

    Route::get('/{job:slug}', [JobController::class, 'show'])->name('show');
});

Route::prefix('stories')->name('stories.')->group(function () {
    Route::get('/', [StoryController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [AlumniStoryController::class, 'create'])->name('create');
        Route::post('/create', [AlumniStoryController::class, 'store'])->name('store');
        Route::get('/mine', [AlumniStoryController::class, 'mine'])->name('mine');
    });

    Route::get('/{story:slug}', [StoryController::class, 'show'])->name('show');
});

Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [GalleryController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [AlumniGalleryController::class, 'create'])->name('create');
        Route::post('/create', [AlumniGalleryController::class, 'store'])->name('store');
        Route::get('/mine', [AlumniGalleryController::class, 'mine'])->name('mine');
        Route::get('/{galleryPhoto}/edit', [AlumniGalleryController::class, 'edit'])->name('edit');
        Route::put('/{galleryPhoto}', [AlumniGalleryController::class, 'update'])->name('update');
        Route::delete('/{galleryPhoto}', [AlumniGalleryController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('library')->name('library.')->group(function () {
    Route::get('/', [LibraryController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/donate', [AlumniLibraryController::class, 'create'])->name('create');
        Route::post('/donate', [AlumniLibraryController::class, 'store'])->name('store');
        Route::get('/donations', [AlumniLibraryController::class, 'donations'])->name('donations');
        Route::get('/mine', [AlumniLibraryController::class, 'mine'])->name('mine');
        Route::delete('/requests/{borrowRequest}', [AlumniLibraryController::class, 'cancel'])->name('requests.cancel');
        Route::get('/{book}/edit', [AlumniLibraryController::class, 'edit'])->name('edit');
        Route::put('/{book}', [AlumniLibraryController::class, 'update'])->name('update');
        Route::delete('/{book}', [AlumniLibraryController::class, 'destroy'])->name('destroy');
        Route::post('/{book}/borrow', [AlumniLibraryController::class, 'borrow'])->name('borrow');
    });

    Route::get('/{book}', [LibraryController::class, 'show'])->name('show');
});

Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [MarketplaceController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [MarketplaceController::class, 'create'])->name('create');
        Route::post('/create', [MarketplaceController::class, 'store'])->name('store');
        Route::get('/mine', [MarketplaceController::class, 'mine'])->name('mine');
        Route::get('/{listing:slug}/edit', [MarketplaceController::class, 'edit'])->name('edit');
        Route::put('/{listing:slug}', [MarketplaceController::class, 'update'])->name('update');
        Route::delete('/{listing:slug}', [MarketplaceController::class, 'destroy'])->name('destroy');
        Route::post('/{listing:slug}/inquire', [MarketplaceController::class, 'inquire'])->name('inquire');
    });

    Route::get('/{listing:slug}', [MarketplaceController::class, 'show'])->name('show');
});

Route::prefix('carpooling')->name('carpooling.')->group(function () {
    Route::get('/', [CarpoolSearchController::class, 'index'])->name('search');
});

Route::post('/stripe/carpool/webhook', [CarpoolStripeWebhookController::class, 'handle'])->name('carpooling.stripe.webhook');

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{news:slug}', [NewsController::class, 'show'])->name('show');
});

Route::prefix('scholarships')->name('scholarships.')->group(function () {
    Route::get('/', [ScholarshipController::class, 'index'])->name('index');
    Route::get('/{scholarship:slug}', [ScholarshipController::class, 'show'])->name('show');
});

Route::prefix('donate')->name('donations.')->group(function () {
    Route::get('/', [DonationController::class, 'index'])->name('index');
    Route::get('/checkout/{campaign:slug?}', [DonationController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{campaign:slug?}', [DonationController::class, 'store'])->middleware('throttle:10,1')->name('store');
    Route::get('/{campaign:slug}', [DonationController::class, 'show'])->name('show');
});

// Shown to alumni whose account is still awaiting admin verification.
Route::middleware('auth')->group(function () {
    Route::get('/verification-pending', VerificationPendingController::class)->name('verification.pending');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Alumni-area routes: requires a verified email AND an approved alumni account.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    });

    Route::prefix('my-profile')->name('alumni.profile.')->group(function () {
        Route::get('/', [AlumniProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [AlumniProfileController::class, 'update'])->name('update');
        Route::post('/items/{type}', [ProfileItemController::class, 'store'])->name('items.store');
        Route::delete('/items/{type}/{item}', [ProfileItemController::class, 'destroy'])->name('items.destroy');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/{conversation?}', [MessageController::class, 'index'])->name('index');
        Route::get('/start/{user}', [MessageController::class, 'create'])->name('create');
        Route::post('/{conversation}/send', [MessageController::class, 'store'])->name('store');
    });

    Route::prefix('carpooling')->name('carpooling.')->group(function () {
        Route::get('/become-driver', [CarpoolDriverController::class, 'become'])->name('driver.become');
        Route::post('/become-driver', [CarpoolDriverController::class, 'store'])->name('driver.store');
        Route::get('/dashboard', [CarpoolDriverController::class, 'dashboard'])->name('driver.dashboard');
        Route::post('/toggle-active', [CarpoolDriverController::class, 'toggleActive'])->name('driver.toggle-active');

        Route::prefix('cars')->name('cars.')->group(function () {
            Route::get('/', [CarpoolCarController::class, 'index'])->name('index');
            Route::get('/create', [CarpoolCarController::class, 'create'])->name('create');
            Route::post('/create', [CarpoolCarController::class, 'store'])->name('store');
            Route::get('/{car}/edit', [CarpoolCarController::class, 'edit'])->name('edit');
            Route::put('/{car}', [CarpoolCarController::class, 'update'])->name('update');
            Route::delete('/{car}', [CarpoolCarController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/create', [CarpoolScheduleController::class, 'create'])->name('create');
            Route::post('/create', [CarpoolScheduleController::class, 'store'])->name('store');
            Route::get('/{schedule}/edit', [CarpoolScheduleController::class, 'edit'])->name('edit');
            Route::put('/{schedule}', [CarpoolScheduleController::class, 'update'])->name('update');
            Route::delete('/{schedule}', [CarpoolScheduleController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [CarpoolBookingController::class, 'index'])->name('index');
            Route::post('/{schedule}', [CarpoolBookingController::class, 'store'])->name('store');
            Route::delete('/{booking}', [CarpoolBookingController::class, 'cancel'])->name('cancel');
            Route::get('/{booking}/pay', [CarpoolBookingController::class, 'pay'])->name('pay');
            Route::get('/{booking}/payment-success', [CarpoolBookingController::class, 'paymentSuccess'])->name('payment-success');
            Route::get('/{booking}/payment-cancelled', [CarpoolBookingController::class, 'paymentCancelled'])->name('payment-cancelled');
        });

        Route::prefix('driver/bookings')->name('driver.bookings.')->group(function () {
            Route::get('/', [DriverBookingController::class, 'index'])->name('index');
            Route::post('/{booking}/accept', [DriverBookingController::class, 'accept'])->name('accept');
            Route::post('/{booking}/decline', [DriverBookingController::class, 'decline'])->name('decline');
        });
    });

    Route::prefix('network')->name('connections.')->group(function () {
        Route::get('/', [ConnectionController::class, 'index'])->name('index');
        Route::post('/{user}', [ConnectionController::class, 'store'])->name('store');
        Route::post('/{connection}/accept', [ConnectionController::class, 'accept'])->name('accept');
        Route::post('/{connection}/reject', [ConnectionController::class, 'reject'])->name('reject');
        Route::delete('/{connection}/cancel', [ConnectionController::class, 'cancel'])->name('cancel');
        Route::delete('/{connection}', [ConnectionController::class, 'destroy'])->name('destroy');
    });

    Route::post('/events/{event:slug}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event:slug}/cancel', [EventController::class, 'cancelRegistration'])->name('events.cancel');

    Route::prefix('community')->name('community.')->group(function () {
        Route::get('/', [CommunityController::class, 'index'])->name('index');
        Route::post('/', [CommunityController::class, 'store'])->name('store');
        Route::get('/{post}', [CommunityController::class, 'show'])->name('show');
        Route::delete('/{post}', [CommunityController::class, 'destroy'])->name('destroy');
    });
    Route::post('/community/poll/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');
    Route::post('/comments/{type}/{id}', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/likes/{type}/{id}', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/reports/{type}/{id}', [ReportController::class, 'store'])->name('reports.store');

    Route::prefix('mentorship')->name('mentorship.')->group(function () {
        Route::get('/', [MentorshipController::class, 'index'])->name('index');
        Route::post('/become-mentor', [MentorshipController::class, 'becomeMentor'])->name('become-mentor');
        Route::post('/toggle-active', [MentorshipController::class, 'toggleActive'])->name('toggle-active');
        Route::get('/mine', [MentorshipController::class, 'myMentorships'])->name('mine');
        Route::post('/{mentorProfile}/request', [MentorshipController::class, 'requestMentorship'])->name('request');
        Route::post('/requests/{mentorshipRequest}/accept', [MentorshipController::class, 'accept'])->name('accept');
        Route::post('/requests/{mentorshipRequest}/reject', [MentorshipController::class, 'reject'])->name('reject');
    });
});

// Admin-area routes: staff roles only (super administrator, alumni administrator, moderator).
Route::middleware(['auth', 'verified', 'role:super-administrator,alumni-administrator,moderator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('alumni')->name('alumni.')->group(function () {
            Route::get('/', [AdminAlumniController::class, 'index'])->name('index');
            Route::get('/pending', [AdminAlumniController::class, 'pending'])->name('pending');
            Route::get('/verified', [AdminAlumniController::class, 'verified'])->name('verified');
            Route::get('/suspended', [AdminAlumniController::class, 'suspended'])->name('suspended');
            Route::post('/bulk-action', [AdminAlumniController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export', [AdminAlumniController::class, 'export'])->name('export');
            Route::get('/{user}', [AdminAlumniController::class, 'show'])->name('show');
            Route::post('/{user}/verify', [AdminAlumniController::class, 'verify'])->name('verify');
            Route::post('/{user}/visibility', [AdminAlumniController::class, 'updateVisibility'])->name('visibility');
            Route::post('/{user}/reject', [AdminAlumniController::class, 'reject'])->name('reject');
            Route::post('/{user}/suspend', [AdminAlumniController::class, 'suspend'])->name('suspend');
            Route::post('/{user}/restore', [AdminAlumniController::class, 'restore'])->name('restore');
            Route::delete('/{user}', [AdminAlumniController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('moderators')->name('moderators.')->group(function () {
            Route::get('/', [AdminModeratorController::class, 'index'])->name('index');
            Route::get('/create', [AdminModeratorController::class, 'create'])->name('create');
            Route::post('/', [AdminModeratorController::class, 'store'])->name('store');
            Route::get('/{moderator}/edit', [AdminModeratorController::class, 'edit'])->name('edit');
            Route::put('/{moderator}', [AdminModeratorController::class, 'update'])->name('update');
            Route::delete('/{moderator}', [AdminModeratorController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [AdminEventController::class, 'index'])->name('index');
            Route::get('/create', [AdminEventController::class, 'create'])->name('create');
            Route::post('/', [AdminEventController::class, 'store'])->name('store');
            Route::get('/registrations', [AdminEventController::class, 'registrations'])->name('registrations');
            Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit');
            Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
            Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
            Route::get('/{event}/attendees', [AdminEventController::class, 'attendees'])->name('attendees');
            Route::get('/{event}/attendees/export', [AdminEventController::class, 'exportRegistrations'])->name('attendees.export');
        });

        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [AdminJobController::class, 'index'])->name('index');
            Route::get('/create', [AdminJobController::class, 'create'])->name('create');
            Route::post('/', [AdminJobController::class, 'store'])->name('store');
            Route::get('/pending', [AdminJobController::class, 'pending'])->name('pending');
            Route::get('/{job}/edit', [AdminJobController::class, 'edit'])->name('edit');
            Route::put('/{job}', [AdminJobController::class, 'update'])->name('update');
            Route::post('/{job}/approve', [AdminJobController::class, 'approve'])->name('approve');
            Route::post('/{job}/reject', [AdminJobController::class, 'reject'])->name('reject');
            Route::delete('/{job}', [AdminJobController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [AdminCompanyController::class, 'index'])->name('index');
            Route::post('/', [AdminCompanyController::class, 'store'])->name('store');
            Route::put('/{company}', [AdminCompanyController::class, 'update'])->name('update');
            Route::delete('/{company}', [AdminCompanyController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('carpooling/drivers')->name('carpooling.drivers.')->group(function () {
            Route::get('/pending', [AdminCarpoolDriverController::class, 'pending'])->name('pending');
            Route::get('/approved', [AdminCarpoolDriverController::class, 'approvedIndex'])->name('approved');
            Route::get('/rejected', [AdminCarpoolDriverController::class, 'rejectedIndex'])->name('rejected');
            Route::get('/{carpoolDriverProfile}', [AdminCarpoolDriverController::class, 'show'])->name('show');
            Route::post('/{carpoolDriverProfile}/approve', [AdminCarpoolDriverController::class, 'approve'])->name('approve');
            Route::post('/{carpoolDriverProfile}/reject', [AdminCarpoolDriverController::class, 'reject'])->name('reject');
            Route::post('/{carpoolDriverProfile}/suspend', [AdminCarpoolDriverController::class, 'suspend'])->name('suspend');
        });

        Route::prefix('carpooling/schedules')->name('carpooling.schedules.')->group(function () {
            Route::get('/pending', [AdminCarpoolScheduleController::class, 'pending'])->name('pending');
            Route::get('/approved', [AdminCarpoolScheduleController::class, 'approvedIndex'])->name('approved');
            Route::get('/rejected', [AdminCarpoolScheduleController::class, 'rejectedIndex'])->name('rejected');
            Route::get('/{carpoolSchedule}', [AdminCarpoolScheduleController::class, 'show'])->name('show');
            Route::post('/{carpoolSchedule}/approve', [AdminCarpoolScheduleController::class, 'approve'])->name('approve');
            Route::post('/{carpoolSchedule}/reject', [AdminCarpoolScheduleController::class, 'reject'])->name('reject');
        });

        Route::prefix('marketplace/categories')->name('marketplace.categories.')->group(function () {
            Route::get('/', [AdminMarketplaceCategoryController::class, 'index'])->name('index');
            Route::post('/', [AdminMarketplaceCategoryController::class, 'store'])->name('store');
            Route::put('/{marketplaceCategory}', [AdminMarketplaceCategoryController::class, 'update'])->name('update');
            Route::delete('/{marketplaceCategory}', [AdminMarketplaceCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('marketplace/listings')->name('marketplace.listings.')->group(function () {
            Route::get('/pending', [AdminMarketplaceListingController::class, 'pending'])->name('pending');
            Route::get('/approved', [AdminMarketplaceListingController::class, 'approvedIndex'])->name('approved');
            Route::get('/rejected', [AdminMarketplaceListingController::class, 'rejectedIndex'])->name('rejected');
            Route::get('/{marketplaceListing}', [AdminMarketplaceListingController::class, 'show'])->name('show');
            Route::post('/{marketplaceListing}/approve', [AdminMarketplaceListingController::class, 'approve'])->name('approve');
            Route::post('/{marketplaceListing}/reject', [AdminMarketplaceListingController::class, 'reject'])->name('reject');
            Route::delete('/{marketplaceListing}', [AdminMarketplaceListingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('marketplace/orders')->name('marketplace.orders.')->group(function () {
            Route::get('/', [AdminMarketplaceOrderController::class, 'index'])->name('index');
            Route::get('/{marketplaceOrder}', [AdminMarketplaceOrderController::class, 'show'])->name('show');
            Route::post('/{marketplaceOrder}/status', [AdminMarketplaceOrderController::class, 'updateStatus'])->name('status');
            Route::post('/{marketplaceOrder}/converse/{role}', [AdminMarketplaceOrderController::class, 'converse'])->name('converse')->whereIn('role', ['buyer', 'seller']);
        });

        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/{conversation?}', [AdminMessageController::class, 'index'])->name('index');
            Route::post('/{conversation}/send', [AdminMessageController::class, 'store'])->name('store');
        });

        Route::prefix('marketplace/reports')->name('marketplace.reports.')->group(function () {
            Route::get('/', [AdminMarketplaceReportController::class, 'index'])->name('index');
            Route::get('/orders', [AdminMarketplaceReportController::class, 'orders'])->name('orders');
            Route::get('/sellers', [AdminMarketplaceReportController::class, 'sellers'])->name('sellers');
            Route::get('/categories', [AdminMarketplaceReportController::class, 'categories'])->name('categories');
            Route::get('/export/{type}', [AdminMarketplaceReportController::class, 'export'])->name('export');
        });

        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [AdminNewsController::class, 'index'])->name('index');
            Route::get('/create', [AdminNewsController::class, 'create'])->name('create');
            Route::post('/', [AdminNewsController::class, 'store'])->name('store');
            Route::get('/{news}/edit', [AdminNewsController::class, 'edit'])->name('edit');
            Route::put('/{news}', [AdminNewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [AdminNewsController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('stories')->name('stories.')->group(function () {
            Route::get('/', [AdminStoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminStoryController::class, 'create'])->name('create');
            Route::post('/', [AdminStoryController::class, 'store'])->name('store');
            Route::get('/{story}/edit', [AdminStoryController::class, 'edit'])->name('edit');
            Route::put('/{story}', [AdminStoryController::class, 'update'])->name('update');
            Route::post('/{story}/publish', [AdminStoryController::class, 'publish'])->name('publish');
            Route::post('/{story}/reject', [AdminStoryController::class, 'reject'])->name('reject');
            Route::delete('/{story}', [AdminStoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('gallery')->name('gallery.')->group(function () {
            Route::get('/', [AdminGalleryController::class, 'index'])->name('index');
            Route::get('/create', [AdminGalleryController::class, 'create'])->name('create');
            Route::post('/', [AdminGalleryController::class, 'store'])->name('store');
            Route::get('/{galleryPhoto}/edit', [AdminGalleryController::class, 'edit'])->name('edit');
            Route::put('/{galleryPhoto}', [AdminGalleryController::class, 'update'])->name('update');
            Route::post('/{galleryPhoto}/approve', [AdminGalleryController::class, 'approve'])->name('approve');
            Route::post('/{galleryPhoto}/reject', [AdminGalleryController::class, 'reject'])->name('reject');
            Route::delete('/{galleryPhoto}', [AdminGalleryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('library')->name('library.')->group(function () {
            Route::get('/', [AdminLibraryController::class, 'index'])->name('index');
            Route::get('/create', [AdminLibraryController::class, 'create'])->name('create');
            Route::post('/', [AdminLibraryController::class, 'store'])->name('store');
            Route::get('/available', [AdminLibraryController::class, 'availableBooks'])->name('available');
            Route::get('/requests/pending', [AdminLibraryController::class, 'pendingRequests'])->name('requests.pending');
            Route::get('/requests/rejected', [AdminLibraryController::class, 'rejectedRequests'])->name('requests.rejected');
            Route::get('/requests/accepted', [AdminLibraryController::class, 'acceptedRequests'])->name('requests.accepted');
            Route::get('/requests/borrowed', [AdminLibraryController::class, 'borrowedReport'])->name('requests.borrowed');
            Route::get('/requests/history', [AdminLibraryController::class, 'history'])->name('requests.history');
            Route::post('/requests/{borrowRequest}/approve', [AdminLibraryController::class, 'approveRequest'])->name('requests.approve');
            Route::post('/requests/{borrowRequest}/reject', [AdminLibraryController::class, 'rejectRequest'])->name('requests.reject');
            Route::post('/requests/{borrowRequest}/handover', [AdminLibraryController::class, 'handover'])->name('requests.handover');
            Route::post('/requests/{borrowRequest}/returned', [AdminLibraryController::class, 'markReturned'])->name('requests.returned');
            Route::post('/requests/{borrowRequest}/remind', [AdminLibraryController::class, 'sendReminder'])->name('requests.remind');
            Route::get('/{book}/edit', [AdminLibraryController::class, 'edit'])->name('edit');
            Route::put('/{book}', [AdminLibraryController::class, 'update'])->name('update');
            Route::post('/{book}/approve', [AdminLibraryController::class, 'approve'])->name('approve');
            Route::post('/{book}/reject', [AdminLibraryController::class, 'reject'])->name('reject');
            Route::delete('/{book}', [AdminLibraryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('community')->name('community.')->group(function () {
            Route::get('/posts', [AdminCommunityController::class, 'posts'])->name('posts');
            Route::get('/reports', [AdminCommunityController::class, 'reports'])->name('reports');
            Route::get('/moderation', [AdminCommunityController::class, 'moderation'])->name('moderation');
            Route::post('/posts/{post}/approve', [AdminCommunityController::class, 'approvePost'])->name('posts.approve');
            Route::post('/posts/{post}/remove', [AdminCommunityController::class, 'removePost'])->name('posts.remove');
            Route::post('/reports/{report}/dismiss', [AdminCommunityController::class, 'dismissReport'])->name('reports.dismiss');
        });

        Route::prefix('mentorship')->name('mentorship.')->group(function () {
            Route::get('/mentors', [AdminMentorshipController::class, 'mentors'])->name('mentors');
            Route::get('/requests', [AdminMentorshipController::class, 'requests'])->name('requests');
            Route::post('/requests/{mentorshipRequest}/approve', [AdminMentorshipController::class, 'approve'])->name('requests.approve');
            Route::post('/requests/{mentorshipRequest}/reject', [AdminMentorshipController::class, 'reject'])->name('requests.reject');
        });

        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            Route::get('/', [AdminScholarshipController::class, 'index'])->name('index');
            Route::post('/', [AdminScholarshipController::class, 'store'])->name('store');
            Route::put('/{scholarship}', [AdminScholarshipController::class, 'update'])->name('update');
            Route::delete('/{scholarship}', [AdminScholarshipController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('index');
            Route::get('/chart-data', [AdminReportController::class, 'chartData'])->name('chart-data');
            Route::get('/export/{type}', [AdminReportController::class, 'export'])->name('export');
        });

        Route::prefix('sliders')->name('sliders.')->group(function () {
            Route::get('/', [AdminSliderController::class, 'index'])->name('index');
            Route::get('/create', [AdminSliderController::class, 'create'])->name('create');
            Route::post('/', [AdminSliderController::class, 'store'])->name('store');
            Route::get('/{slider}/edit', [AdminSliderController::class, 'edit'])->name('edit');
            Route::put('/{slider}', [AdminSliderController::class, 'update'])->name('update');
            Route::delete('/{slider}', [AdminSliderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
            Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
            Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
        });

        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::put('/institution', [AdminSettingsController::class, 'updateInstitution'])->name('institution');
            Route::put('/association', [AdminSettingsController::class, 'updateAssociation'])->name('association');
            Route::put('/general', [AdminSettingsController::class, 'updateGeneral'])->name('general');
            Route::put('/about', [AdminSettingsController::class, 'updateAbout'])->name('about');
            Route::put('/login-page', [AdminSettingsController::class, 'updateLoginPage'])->name('login-page');
            Route::put('/homepage', [AdminSettingsController::class, 'updateHomepage'])->name('homepage');
        });

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('/', [AdminDonationController::class, 'index'])->name('index');
            Route::get('/campaigns', [AdminDonationController::class, 'campaigns'])->name('campaigns');
            Route::post('/campaigns', [AdminDonationController::class, 'storeCampaign'])->name('campaigns.store');
            Route::delete('/campaigns/{campaign}', [AdminDonationController::class, 'destroyCampaign'])->name('campaigns.destroy');
            Route::get('/reports', [AdminDonationController::class, 'reports'])->name('reports');
            Route::get('/reports/chart-data', [AdminDonationController::class, 'chartData'])->name('reports.chart-data');
        });
    });

require __DIR__.'/auth.php';
