<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Models\Slider;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Slider::class);

        $sliders = Slider::ordered()->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        $this->authorize('create', Slider::class);

        return view('admin.sliders.create');
    }

    public function store(StoreSliderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['position'] = $data['position'] ?? (Slider::max('position') + 1);
        $data['image'] = $request->file('image')->store('sliders', 'public');

        $slider = Slider::create($data);

        AuditLogger::log('created_slider', $slider, "Created homepage slide \"{$slider->title}\".");
        Cache::forget('homepage.content');

        return redirect()->route('admin.sliders.index')->with('status', 'Slide created successfully.');
    }

    public function edit(Slider $slider): View
    {
        $this->authorize('update', $slider);

        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(StoreSliderRequest $request, Slider $slider): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['position'] = $data['position'] ?? $slider->position;

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image);
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        AuditLogger::log('updated_slider', $slider, "Updated homepage slide \"{$slider->title}\".");
        Cache::forget('homepage.content');

        return redirect()->route('admin.sliders.index')->with('status', 'Slide updated successfully.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $this->authorize('delete', $slider);

        Storage::disk('public')->delete($slider->image);
        $slider->delete();

        AuditLogger::log('deleted_slider', null, "Deleted homepage slide \"{$slider->title}\".");
        Cache::forget('homepage.content');

        return back()->with('status', 'Slide deleted.');
    }
}
