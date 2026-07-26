<?php

namespace App\Http\Controllers;

use App\Models\VitrinePage;
use App\Models\VitrineSchedule;
use App\Models\VitrineService;
use App\Models\VitrineSetting;
use App\Models\VitrineSocialPost;
use Illuminate\Contracts\View\View;

class VitrineController extends Controller
{
    public function home(): View
    {
        return view('public.vitrine.home', $this->sharedData([
            'currentSlug' => 'home',
            'page' => $this->pageBySlug('home'),
            'services' => VitrineService::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'schedules' => VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'socialPosts' => VitrineSocialPost::query()->where('is_active', true)->orderBy('sort_order')->latest()->take(6)->get(),
        ]));
    }

    public function about(): View
    {
        return view('public.vitrine.about', $this->sharedData([
            'currentSlug' => 'about',
            'page' => $this->pageBySlug('about'),
        ]));
    }

    public function services(): View
    {
        return view('public.vitrine.services', $this->sharedData([
            'currentSlug' => 'services',
            'page' => $this->pageBySlug('services'),
            'services' => VitrineService::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'schedules' => VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]));
    }

    public function activities(): View
    {
        return view('public.vitrine.activities', $this->sharedData([
            'currentSlug' => 'activities',
            'page' => $this->pageBySlug('activities'),
            'socialPosts' => VitrineSocialPost::query()->where('is_active', true)->orderBy('sort_order')->latest()->get(),
        ]));
    }

    public function contact(): View
    {
        return view('public.vitrine.contact', $this->sharedData([
            'currentSlug' => 'contact',
            'page' => $this->pageBySlug('contact'),
            'schedules' => VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]));
    }

    private function sharedData(array $data = []): array
    {
        $settings = VitrineSetting::query()->first();

        return array_merge([
            'settings' => $settings,
            'pagesMenu' => VitrinePage::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(['slug', 'title']),
        ], $data);
    }

    private function pageBySlug(string $slug): ?VitrinePage
    {
        return VitrinePage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }
}
