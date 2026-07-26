<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VitrinePage;
use App\Models\VitrineBlogPost;
use App\Models\VitrineFaq;
use App\Models\VitrineNewsletterSubscriber;
use App\Models\VitrineSchedule;
use App\Models\VitrineService;
use App\Models\VitrineSetting;
use App\Models\VitrineSocialPost;
use App\Models\VitrineTestimonial;
use App\Models\VitrineVisitRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class VitrineAdminController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.vitrine.settings');
    }

    public function settingsPage(): View
    {
        $settings = VitrineSetting::query()->firstOrCreate([
            'id' => 1,
        ], [
            'site_name' => 'Ancre Des Elites',
            'tagline' => 'Garderie et eveil',
            'parent_space_url' => '/login',
        ]);

        return view('admin.vitrine.settings', [
            'settings' => $settings,
        ]);
    }

    public function pagesPage(): View
    {
        return view('admin.vitrine.pages', [
            'pages' => VitrinePage::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function servicesPage(): View
    {
        return view('admin.vitrine.services', [
            'services' => VitrineService::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function schedulesPage(): View
    {
        return view('admin.vitrine.schedules', [
            'schedules' => VitrineSchedule::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function socialPostsPage(): View
    {
        return view('admin.vitrine.social-posts', [
            'socialPosts' => VitrineSocialPost::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function blogPostsPage(): View
    {
        return view('admin.vitrine.blog-posts', [
            'blogPosts' => VitrineBlogPost::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function testimonialsPage(): View
    {
        return view('admin.vitrine.testimonials', [
            'testimonials' => VitrineTestimonial::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function faqsPage(): View
    {
        return view('admin.vitrine.faqs', [
            'faqs' => VitrineFaq::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function leadsPage(): View
    {
        return view('admin.vitrine.leads', [
            'visitRequests' => VitrineVisitRequest::query()->latest()->get(),
        ]);
    }

    public function newslettersPage(): View
    {
        return view('admin.vitrine.newsletters', [
            'newsletterSubscribers' => VitrineNewsletterSubscriber::query()->latest()->get(),
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
        $hasHeroImageColumn = Schema::hasColumn('vitrine_pages', 'hero_image');
        $hasMissionColumn = Schema::hasColumn('vitrine_pages', 'mission');
        $hasVisionColumn = Schema::hasColumn('vitrine_pages', 'vision');
        $hasValeursColumn = Schema::hasColumn('vitrine_pages', 'valeurs');
        $hasMetaColumn = Schema::hasColumn('vitrine_pages', 'meta');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ];

        if ($page->slug === 'home') {
            $rules['home_hero_badge_text'] = ['nullable', 'string', 'max:255'];
            $rules['home_about_image_url'] = ['nullable', 'string', 'max:2048'];
            $rules['home_services_count'] = ['nullable', 'integer', 'min:1', 'max:12'];
            $rules['home_activities_count'] = ['nullable', 'integer', 'min:1', 'max:12'];
            $rules['home_blog_count'] = ['nullable', 'integer', 'min:1', 'max:12'];
            $rules['home_testimonials_count'] = ['nullable', 'integer', 'min:1', 'max:20'];
            $rules['home_animation_duration_seconds'] = ['nullable', 'integer', 'min:2', 'max:12'];
            $rules['home_stats_mode'] = ['nullable', 'in:auto,manual'];
            $rules['home_manual_services_count'] = ['nullable', 'integer', 'min:0', 'max:999999'];
            $rules['home_manual_parents_count'] = ['nullable', 'integer', 'min:0', 'max:999999'];
            $rules['home_manual_staff_count'] = ['nullable', 'integer', 'min:0', 'max:999999'];
            $rules['home_manual_activities_count'] = ['nullable', 'integer', 'min:0', 'max:999999'];
            $rules['home_show_blog_section'] = ['nullable', 'boolean'];
            $rules['home_show_testimonials_section'] = ['nullable', 'boolean'];
            for ($i = 1; $i <= 6; $i++) {
                $rules['home_hero_image_'.$i] = ['nullable', 'string', 'max:2048'];
            }
            for ($i = 1; $i <= 4; $i++) {
                $rules['home_about_highlight_'.$i] = ['nullable', 'string', 'max:255'];
            }
        }

        if ($hasHeroImageColumn) {
            $rules['hero_image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
            $rules['remove_hero_image'] = ['nullable', 'boolean'];
        }
        if ($hasMissionColumn) {
            $rules['mission'] = ['nullable', 'string'];
        }
        if ($hasVisionColumn) {
            $rules['vision'] = ['nullable', 'string'];
        }
        if ($hasValeursColumn) {
            $rules['valeurs'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $meta = $page->meta ?? [];
        if ($hasMetaColumn && $page->slug === 'home') {
            $homeHeroImages = [];
            for ($i = 1; $i <= 6; $i++) {
                $value = trim((string) ($validated['home_hero_image_'.$i] ?? ''));
                if ($value !== '') {
                    $homeHeroImages[] = $value;
                }
            }

            $homeHighlights = [];
            for ($i = 1; $i <= 4; $i++) {
                $value = trim((string) ($validated['home_about_highlight_'.$i] ?? ''));
                if ($value !== '') {
                    $homeHighlights[] = $value;
                }
            }

            $meta = [
                'hero_badge_text' => trim((string) ($validated['home_hero_badge_text'] ?? '')),
                'about_image_url' => trim((string) ($validated['home_about_image_url'] ?? '')),
                'hero_images' => $homeHeroImages,
                'about_highlights' => $homeHighlights,
                'home_services_count' => (int) ($validated['home_services_count'] ?? 4),
                'home_activities_count' => (int) ($validated['home_activities_count'] ?? 4),
                'home_blog_count' => (int) ($validated['home_blog_count'] ?? 3),
                'home_testimonials_count' => (int) ($validated['home_testimonials_count'] ?? 10),
                'home_animation_duration_seconds' => (int) ($validated['home_animation_duration_seconds'] ?? 4),
                'home_stats_mode' => $validated['home_stats_mode'] ?? 'auto',
                'home_manual_services_count' => $validated['home_manual_services_count'] ?? null,
                'home_manual_parents_count' => $validated['home_manual_parents_count'] ?? null,
                'home_manual_staff_count' => $validated['home_manual_staff_count'] ?? null,
                'home_manual_activities_count' => $validated['home_manual_activities_count'] ?? null,
                'home_show_blog_section' => $request->boolean('home_show_blog_section'),
                'home_show_testimonials_section' => $request->boolean('home_show_testimonials_section'),
            ];

            if ($meta['hero_badge_text'] === '') {
                unset($meta['hero_badge_text']);
            }
            if ($meta['about_image_url'] === '') {
                unset($meta['about_image_url']);
            }
        }

        try {
            $heroImagePath = $hasHeroImageColumn ? $page->hero_image : null;
            if ($hasHeroImageColumn && $request->boolean('remove_hero_image')) {
                if (! empty($heroImagePath) && Storage::disk('public')->exists($heroImagePath)) {
                    Storage::disk('public')->delete($heroImagePath);
                }
                $heroImagePath = null;
            }

            if ($hasHeroImageColumn && $request->hasFile('hero_image')) {
                if (! empty($heroImagePath) && Storage::disk('public')->exists($heroImagePath)) {
                    Storage::disk('public')->delete($heroImagePath);
                }
                $heroImagePath = $request->file('hero_image')->store('vitrine/pages', 'public');
            }

            $updates = [
                'title' => $validated['title'],
                'hero_title' => $validated['hero_title'] ?? null,
                'hero_subtitle' => $validated['hero_subtitle'] ?? null,
                'content' => $validated['content'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_published' => $request->boolean('is_published'),
            ];

            if ($hasHeroImageColumn) {
                $updates['hero_image'] = $heroImagePath;
            }
            if ($hasMissionColumn) {
                $updates['mission'] = $validated['mission'] ?? null;
            }
            if ($hasVisionColumn) {
                $updates['vision'] = $validated['vision'] ?? null;
            }
            if ($hasValeursColumn) {
                $updates['valeurs'] = $validated['valeurs'] ?? null;
            }
            if ($hasMetaColumn) {
                $updates['meta'] = $meta;
            }

            $page->update($updates);
        } catch (\Throwable $exception) {
            Log::error('Vitrine page update failed', [
                'page_id' => $page->id,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'La mise a jour de la page a echoue. Verifiez les migrations puis reessayez.');
        }

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

    public function storeBlogPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'cover_url' => ['nullable', 'string', 'max:2048'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'] ?: str($validated['title'])->slug();

        VitrineBlogPost::query()->create([
            ...$validated,
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return back()->with('success', 'Article ajoute.');
    }

    public function updateBlogPost(Request $request, VitrineBlogPost $blogPost): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'cover_url' => ['nullable', 'string', 'max:2048'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $slug = $validated['slug'] ?: str($validated['title'])->slug();

        $blogPost->update([
            ...$validated,
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $validated['published_at'] ?? $blogPost->published_at,
        ]);

        return back()->with('success', 'Article mis a jour.');
    }

    public function destroyBlogPost(VitrineBlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return back()->with('success', 'Article supprime.');
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_name' => ['required', 'string', 'max:255'],
            'child_name' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        VitrineTestimonial::query()->create([
            ...$validated,
            'rating' => $validated['rating'] ?? 5,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Temoignage ajoute.');
    }

    public function updateTestimonial(Request $request, VitrineTestimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'parent_name' => ['required', 'string', 'max:255'],
            'child_name' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $testimonial->update([
            ...$validated,
            'rating' => $validated['rating'] ?? 5,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Temoignage mis a jour.');
    }

    public function destroyTestimonial(VitrineTestimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('success', 'Temoignage supprime.');
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        VitrineFaq::query()->create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'FAQ ajoutee.');
    }

    public function updateFaq(Request $request, VitrineFaq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq->update([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'FAQ mise a jour.');
    }

    public function destroyFaq(VitrineFaq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ supprimee.');
    }

    public function exportNewsletterCsv()
    {
        $fileName = 'newsletter-subscribers-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'Actif', 'Source', 'Date inscription']);

            VitrineNewsletterSubscriber::query()
                ->orderByDesc('id')
                ->chunk(200, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->id,
                            $row->email,
                            $row->is_active ? 'Oui' : 'Non',
                            $row->source_page,
                            optional($row->created_at)->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
