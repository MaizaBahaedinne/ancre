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
use Illuminate\Support\Facades\Storage;

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
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $heroImagePath = $page->hero_image;
        if ($request->boolean('remove_hero_image')) {
            if ($heroImagePath && Storage::disk('public')->exists($heroImagePath)) {
                Storage::disk('public')->delete($heroImagePath);
            }
            $heroImagePath = null;
        }

        if ($request->hasFile('hero_image')) {
            if ($heroImagePath && Storage::disk('public')->exists($heroImagePath)) {
                Storage::disk('public')->delete($heroImagePath);
            }
            $heroImagePath = $request->file('hero_image')->store('vitrine/pages', 'public');
        }

        $page->update([
            'title' => $validated['title'],
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
            'hero_image' => $heroImagePath,
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
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail_image')) {
            $thumbnailPath = $request->file('thumbnail_image')->store('vitrine/social', 'public');
        }

        VitrineSocialPost::query()->create([
            ...$validated,
            'thumbnail_path' => $thumbnailPath,
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
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_thumbnail_image' => ['nullable', 'boolean'],
            'caption' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $thumbnailPath = $socialPost->thumbnail_path;
        if ($request->boolean('remove_thumbnail_image')) {
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = null;
        }

        if ($request->hasFile('thumbnail_image')) {
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail_image')->store('vitrine/social', 'public');
        }

        $socialPost->update([
            ...$validated,
            'thumbnail_path' => $thumbnailPath,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Publication sociale mise a jour.');
    }

    public function destroySocialPost(VitrineSocialPost $socialPost): RedirectResponse
    {
        if ($socialPost->thumbnail_path && Storage::disk('public')->exists($socialPost->thumbnail_path)) {
            Storage::disk('public')->delete($socialPost->thumbnail_path);
        }

        $socialPost->delete();

        return back()->with('success', 'Publication sociale supprimee.');
    }
}
