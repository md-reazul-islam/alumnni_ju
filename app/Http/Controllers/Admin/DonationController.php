<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonationController extends Controller
{
    protected function ensurePermission(Request $request): void
    {
        abort_unless($request->user()->hasPermission('manage-donations'), 403);
    }

    public function index(Request $request): View
    {
        $this->ensurePermission($request);

        $donations = Donation::query()
            ->with(['donor', 'campaign'])
            ->completed()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Donation::completed()->sum('amount'),
            'this_month' => Donation::completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'this_year' => Donation::completed()->whereYear('created_at', now()->year)->sum('amount'),
        ];

        return view('admin.donations.index', compact('donations', 'stats'));
    }

    public function campaigns(Request $request): View
    {
        $this->ensurePermission($request);

        $campaigns = DonationCampaign::withCount('donations')->latest()->paginate(15);

        return view('admin.donations.campaigns', compact('campaigns'));
    }

    public function storeCampaign(Request $request): RedirectResponse
    {
        $this->ensurePermission($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'category' => ['required', 'string'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(4);
        $data['status'] = DonationCampaign::STATUS_ACTIVE;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        DonationCampaign::create($data);

        return back()->with('status', 'Campaign created.');
    }

    public function destroyCampaign(Request $request, DonationCampaign $campaign): RedirectResponse
    {
        $this->ensurePermission($request);

        $campaign->delete();

        return back()->with('status', 'Campaign deleted.');
    }

    public function reports(Request $request): View
    {
        $this->ensurePermission($request);

        return view('admin.donations.reports');
    }

    public function chartData(Request $request): JsonResponse
    {
        $this->ensurePermission($request);

        $monthly = Donation::completed()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $trend = collect(range(11, 0))->map(function ($i) use ($monthly) {
            $key = now()->subMonths($i)->format('Y-m');

            return [
                'month' => now()->subMonths($i)->format('M Y'),
                'total' => (float) ($monthly[$key]->total ?? 0),
            ];
        });

        $byCategory = Donation::completed()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json([
            'trend' => $trend,
            'byCategory' => $byCategory,
        ]);
    }
}
