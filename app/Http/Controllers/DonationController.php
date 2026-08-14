<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Notifications\DonationConfirmation;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function index(): View
    {
        $campaigns = DonationCampaign::active()->latest()->paginate(9);

        return view('public.donations.index', compact('campaigns'));
    }

    public function show(DonationCampaign $campaign): View
    {
        return view('public.donations.show', compact('campaign'));
    }

    public function checkout(?DonationCampaign $campaign = null): View
    {
        return view('public.donations.checkout', compact('campaign'));
    }

    public function store(StoreDonationRequest $request, PaymentGatewayInterface $gateway, ?DonationCampaign $campaign = null): RedirectResponse
    {
        $data = $request->validated();

        $result = $gateway->charge((float) $data['amount'], 'USD', ['email' => $data['donor_email']]);

        if (! $result->successful) {
            return back()->withErrors(['amount' => $result->errorMessage ?? 'Payment could not be processed.']);
        }

        DB::transaction(function () use ($data, $result, $campaign, $request) {
            $donation = Donation::create([
                'donor_id' => $request->user()?->id,
                'donation_campaign_id' => $campaign?->id,
                'donor_name' => $data['donor_name'] ?? null,
                'donor_email' => $data['donor_email'],
                'amount' => $data['amount'],
                'currency' => 'USD',
                'category' => $data['category'],
                'is_anonymous' => $request->boolean('is_anonymous'),
                'payment_method' => $data['payment_method'],
                'payment_status' => Donation::PAYMENT_COMPLETED,
                'transaction_reference' => $result->reference,
            ]);

            if ($campaign) {
                $campaign->increment('raised_amount', $data['amount']);
            }

            $request->user()?->notify(new DonationConfirmation($donation));
        });

        return redirect()->route('donations.index')->with('status', 'Thank you for your generous donation!');
    }
}
