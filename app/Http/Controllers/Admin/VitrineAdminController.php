<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VitrinePage;
use App\Models\VitrineSchedule;
use App\Models\VitrineService;
use App\Models\VitrineSetting;
use App\Models\VitrineSocialPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VitrineAdminController extends Controller
{
    public function index(): View
    {
        $settings = VitrineSetting::query()->firstOrCreate([
            'id' => 1,
        ], [
            'site_name' => 'Ancre Des Elites',
            'tagline' => 'Garderie et eveil',
            'parent_space_url' => '/login',
        ]);

        return view('admin.vitrine.index', [
            'settings' => $settings,
            'pages' => VitrinePage::query()->orderBy('sort_order')->get(),
            'services' => VitrineService::query()->orderBy('sort_order')->get(),
            'schedules' => VitrineSchedule::query()->orderBy('sort_order')->get(),
            'socialPosts' => VitrineSocialPost::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $settings = VitrineSetting::query()->firstOrFail();

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'parent_space_url' => ['nullable', 'string', 'max:2048'],
            'map_embed_url' => ['nullable', 'string', 'max:2048'],
            'facebook_url' => ['nullable', 'string', 'max:2048'],
            'instagram_url' => ['nullable', 'string', 'max:2048'],
            'tiktok_url' => ['nullable', 'string', 'max:2048'],
            'youtube_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $settings->update($validated);

        return back()->with('success', 'Parametres vitrine mis a jour avec succes.');
    }

    public function updatePage(Request $request, VitrinePage $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $page->update([
            'title' => $validated['title'],
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
            'content' => $validated['content'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Page vitrine mise a jour.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        VitrineService::query()->create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Service ajoute a la vitrine.');
    }

    public function updateService(Request $request, VitrineService $service): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $service->update([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Service mis a jour.');
    }

    public function destroyService(VitrineService $service): RedirectResponse
    {
        $service->delete();

        return back()->with('success', 'Service supprime.');
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'day_label' => ['required', 'string', 'max:255'],
            'open_at' => ['nullable', 'string', 'max:20'],
            'close_at' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_closed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        VitrineSchedule::query()->create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_closed' => $request->boolean('is_closed'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Horaire ajoute.');
    }

    public function updateSchedule(Request $request, VitrineSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'day_label' => ['required', 'string', 'max:255'],
            'open_at' => ['nullable', 'string', 'max:20'],
            'close_at' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_closed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $schedule->update([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_closed' => $request->boolean('is_closed'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Horaire mis a jour.');
    }

    public function destroySchedule(VitrineSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return back()->with('success', 'Horaire supprime.');
    }

    public function storeSocialPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:255'],
            'post_url' => ['required', 'string', 'max:2048'],
            'thumbnail_url' => ['nullable', 'string', 'max:2048'],
            'caption' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        VitrineSocialPost::query()->create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Publication sociale ajoutee.');
    }

    public function updateSocialPost(Request $request, VitrineSocialPost $socialPost): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:255'],
            'post_url' => ['required', 'string', 'max:2048'],
            'thumbnail_url' => ['nullable', 'string', 'max:2048'],
            'caption' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $socialPost->update([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Publication sociale mise a jour.');
    }

    public function destroySocialPost(VitrineSocialPost $socialPost): RedirectResponse
    {
        $socialPost->delete();

        return back()->with('success', 'Publication sociale supprimee.');
    }
}
