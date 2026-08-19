<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\ParentModel;
use App\Models\Package;
use App\Models\Personnel;
use App\Models\VitrineNewsletterSubscriber;
use App\Models\VitrineBlogPost;
use App\Models\VitrineBlogPostComment;
use App\Models\VitrineBlogPostReaction;
use App\Models\VitrineFaq;
use App\Models\VitrinePage;
use App\Models\VitrineSchedule;
use App\Models\VitrineService;
use App\Models\VitrineSetting;
use App\Models\VitrineSocialPost;
use App\Models\VitrineTestimonial;
use App\Models\VitrineVisitRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class VitrineController extends Controller
{
    public function home(): View
    {
        $homePage = $this->pageBySlug('home');
        $pageMeta = is_array($homePage?->meta ?? null) ? $homePage->meta : [];

        $servicesCount = max(1, (int) ($pageMeta['home_services_count'] ?? 4));
        $activitiesCount = max(1, (int) ($pageMeta['home_activities_count'] ?? 4));
        $blogCount = max(1, (int) ($pageMeta['home_blog_count'] ?? 3));
        $testimonialsCount = max(1, (int) ($pageMeta['home_testimonials_count'] ?? 10));

        $testimonials = collect();
        $blogPosts = collect();
        $faqs = collect();
        if (Schema::hasTable('vitrine_testimonials')) {
            try {
                $testimonials = VitrineTestimonial::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->latest()
                    ->take($testimonialsCount)
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine testimonials', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if (Schema::hasTable('vitrine_blog_posts')) {
            try {
                $blogPosts = VitrineBlogPost::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderByDesc('published_at')
                    ->take($blogCount)
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine blog posts', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if (Schema::hasTable('vitrine_faqs')) {
            try {
                $faqs = VitrineFaq::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->take(8)
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine FAQs', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $socialPosts = collect();
        if (Schema::hasTable('vitrine_social_posts')) {
            try {
                $socialPosts = VitrineSocialPost::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->latest()
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine social posts', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $services = collect();
        if (Schema::hasTable('vitrine_services')) {
            try {
                $services = VitrineService::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine services', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $schedules = collect();
        if (Schema::hasTable('vitrine_schedules')) {
            try {
                $schedules = VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine schedules', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $professionals = collect();
        if (Schema::hasTable('personnels')) {
            try {
                $professionals = Personnel::query()->latest('id')->take(4)->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load professionals for vitrine', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $servicesAuto = $services->count();
        $parentsAuto = Schema::hasTable('parents') ? ParentModel::query()->count() : 0;
        $staffAuto = Schema::hasTable('personnels') ? Personnel::query()->count() : 0;
        $activitiesAuto = Schema::hasTable('activites')
            ? Activite::query()->count()
            : $socialPosts->count();

        return view('public.vitrine.home', $this->sharedData([
            'currentSlug' => 'home',
            'page' => $homePage,
            'aboutPage' => $this->pageBySlug('about'),
            'services' => $services,
            'servicesFeatured' => $services->take($servicesCount),
            'schedules' => $schedules,
            'socialPosts' => $socialPosts->take(6),
            'activitiesFeatured' => $socialPosts->take($activitiesCount),
            'blogPosts' => $blogPosts,
            'professionals' => $professionals,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'statsAuto' => [
                'services' => $servicesAuto,
                'parents' => $parentsAuto,
                'staff' => $staffAuto,
                'activities' => $activitiesAuto,
            ],
        ]));
    }

    public function blog(): View
    {
        $posts = collect();
        if (Schema::hasTable('vitrine_blog_posts')) {
            try {
                $posts = VitrineBlogPost::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->orderByDesc('published_at')
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine blog listing', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return view('public.vitrine.blog', $this->sharedData([
            'currentSlug' => 'blog',
            'posts' => $posts,
        ]));
    }

    public function blogShow(string $slug): View
    {
        abort_unless(Schema::hasTable('vitrine_blog_posts'), 404);

        $post = VitrineBlogPost::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $comments = collect();
        $reactionCounts = [];
        $myReaction = null;

        if (Schema::hasTable('vitrine_blog_post_comments')) {
            $comments = VitrineBlogPostComment::query()
                ->with('user:id,name')
                ->where('vitrine_blog_post_id', $post->id)
                ->where('is_visible', true)
                ->latest()
                ->get();
        }

        if (Schema::hasTable('vitrine_blog_post_reactions')) {
            $reactionCounts = VitrineBlogPostReaction::query()
                ->selectRaw('reaction, COUNT(*) as total')
                ->where('vitrine_blog_post_id', $post->id)
                ->groupBy('reaction')
                ->pluck('total', 'reaction')
                ->toArray();

            if (Auth::check()) {
                $myReaction = VitrineBlogPostReaction::query()
                    ->where('vitrine_blog_post_id', $post->id)
                    ->where('user_id', Auth::id())
                    ->value('reaction');
            }
        }

        return view('public.vitrine.blog-show', $this->sharedData([
            'currentSlug' => 'blog',
            'post' => $post,
            'comments' => $comments,
            'reactionCounts' => $reactionCounts,
            'myReaction' => $myReaction,
        ]));
    }

    public function storeBlogComment(Request $request, int $blogPost): RedirectResponse
    {
        abort_unless(Schema::hasTable('vitrine_blog_post_comments') && Schema::hasTable('vitrine_blog_posts'), 404);

        $post = VitrineBlogPost::query()
            ->where('id', $blogPost)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validateWithBag('blogComment', [
            'content' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        VitrineBlogPostComment::query()->create([
            'vitrine_blog_post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_visible' => true,
        ]);

        return redirect()->route('vitrine.blog.show', $post->slug)->with('blog_success', 'Commentaire ajoute avec succes.');
    }

    public function storeBlogReaction(Request $request, int $blogPost): RedirectResponse
    {
        abort_unless(Schema::hasTable('vitrine_blog_post_reactions') && Schema::hasTable('vitrine_blog_posts'), 404);

        $post = VitrineBlogPost::query()
            ->where('id', $blogPost)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validateWithBag('blogReaction', [
            'reaction' => ['required', 'in:like,love,clap'],
        ]);

        VitrineBlogPostReaction::query()->updateOrCreate(
            [
                'vitrine_blog_post_id' => $post->id,
                'user_id' => Auth::id(),
            ],
            [
                'reaction' => $validated['reaction'],
            ]
        );

        return redirect()->route('vitrine.blog.show', $post->slug)->with('blog_success', 'Reaction enregistree.');
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
        $packages = collect();
        if (Schema::hasTable('packages')) {
            try {
                $packages = Package::query()
                    ->where('is_active', true)
                    ->orderBy('nom')
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load packages for vitrine services page', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $services = collect();
        if (Schema::hasTable('vitrine_services')) {
            try {
                $services = VitrineService::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine services page services', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $schedules = collect();
        if (Schema::hasTable('vitrine_schedules')) {
            try {
                $schedules = VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine services page schedules', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return view('public.vitrine.services', $this->sharedData([
            'currentSlug' => 'services',
            'page' => $this->pageBySlug('services'),
            'services' => $services,
            'schedules' => $schedules,
            'packages' => $packages,
        ]));
    }

    public function activities(): View
    {
        $socialPosts = collect();
        if (Schema::hasTable('vitrine_social_posts')) {
            try {
                $socialPosts = VitrineSocialPost::query()->where('is_active', true)->orderBy('sort_order')->latest()->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load activities social posts', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return view('public.vitrine.activities', $this->sharedData([
            'currentSlug' => 'activities',
            'page' => $this->pageBySlug('activities'),
            'socialPosts' => $socialPosts,
        ]));
    }

    public function contact(): View
    {
        $schedules = collect();
        if (Schema::hasTable('vitrine_schedules')) {
            try {
                $schedules = VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load contact schedules', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return view('public.vitrine.contact', $this->sharedData([
            'currentSlug' => 'contact',
            'page' => $this->pageBySlug('contact'),
            'schedules' => $schedules,
        ]));
    }

    public function countdown(): View
    {
        return view('public.vitrine.countdown', $this->sharedData([
            'currentSlug' => 'countdown',
        ]));
    }

    public function privacy(): View
    {
        return view('public.vitrine.legal', $this->sharedData([
            'currentSlug' => 'privacy',
            'title' => 'Privacy Policy Terms',
            'content' => "Cette page presente les regles de confidentialite et de protection des donnees appliquees par Ancre Des Elites.",
        ]));
    }

    public function conditions(): View
    {
        return view('public.vitrine.legal', $this->sharedData([
            'currentSlug' => 'conditions',
            'title' => 'Conditions',
            'content' => "Cette page presente les conditions generales d'utilisation et les engagements de service d'Ancre Des Elites.",
        ]));
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $settings = Schema::hasTable('vitrine_settings') ? VitrineSetting::query()->first() : null;
        $recipient = $settings?->email ?: config('mail.from.address');

        $mailBody = "Nouveau message via formulaire vitrine\n\n"
            ."Nom: {$validated['full_name']}\n"
            ."Telephone: ".($validated['phone'] ?: 'Non renseigne')."\n"
            ."Email: {$validated['email']}\n"
            ."Sujet: {$validated['subject']}\n\n"
            ."Message:\n{$validated['message']}\n";

        try {
            if (!empty($recipient)) {
                Mail::raw($mailBody, function ($message) use ($recipient, $validated) {
                    $message->to($recipient)
                        ->replyTo($validated['email'], $validated['full_name'])
                        ->subject('[Vitrine] '.$validated['subject']);
                });
            } else {
                Log::info('Vitrine contact form submission', $validated);
            }
        } catch (\Throwable $exception) {
            Log::warning('Unable to send vitrine contact email', [
                'error' => $exception->getMessage(),
                'payload' => $validated,
            ]);
        }

        return back()->with('contact_success', 'Merci, votre message a bien ete envoye. Nous vous repondrons rapidement.');
    }

    public function subscribeNewsletter(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('vitrine_newsletter_subscribers')) {
            return back()->with('newsletter_success', 'Merci, votre demande a bien ete recue.');
        }

        $validated = $request->validateWithBag('newsletter', [
            'newsletter_email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = VitrineNewsletterSubscriber::query()->firstOrNew([
            'email' => $validated['newsletter_email'],
        ]);

        $subscriber->source_page = 'home';
        $subscriber->is_active = true;
        $subscriber->save();

        return back()->with('newsletter_success', 'Merci, vous etes bien inscrit(e) a notre newsletter.');
    }

    public function submitVisitRequest(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('vitrine_visit_requests')) {
            return back()->with('visit_success', 'Votre demande a bien ete envoyee.');
        }

        $validated = $request->validateWithBag('visitRequest', [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'child_age_group' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1200'],
        ]);

        VitrineVisitRequest::query()->create([
            ...$validated,
            'status' => 'pending',
        ]);

        return back()->with('visit_success', 'Votre demande de visite a bien ete envoyee. Nous vous recontacterons sous 24h.');
    }

    private function sharedData(array $data = []): array
    {
        $settings = Schema::hasTable('vitrine_settings') ? VitrineSetting::query()->first() : null;
        $pagesMenu = collect();
        $websiteCountdown = [
            'enabled' => false,
            'target_iso' => null,
            'timezone' => 'Africa/Tunis',
            'title' => 'Ouverture des inscriptions',
            'subtitle' => 'Le nouveau portail est bientot disponible.',
            'expired_label' => 'Le lancement est en ligne.',
        ];

        if ($settings?->countdown_enabled && !empty($settings->countdown_target_at)) {
            try {
                $timezone = !empty($settings->countdown_timezone) ? $settings->countdown_timezone : 'Africa/Tunis';
                $target = $settings->countdown_target_at instanceof Carbon
                    ? $settings->countdown_target_at->copy()
                    : Carbon::parse((string) $settings->countdown_target_at);

                $target = $target->setTimezone($timezone);

                $websiteCountdown = [
                    'enabled' => true,
                    'target_iso' => $target->toIso8601String(),
                    'timezone' => $timezone,
                    'title' => !empty($settings->countdown_title) ? $settings->countdown_title : 'Ouverture des inscriptions',
                    'subtitle' => !empty($settings->countdown_subtitle) ? $settings->countdown_subtitle : 'Le nouveau portail est bientot disponible.',
                    'expired_label' => !empty($settings->countdown_expired_label) ? $settings->countdown_expired_label : 'Le lancement est en ligne.',
                ];
            } catch (\Throwable $exception) {
                Log::warning('Unable to build website countdown payload', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if (Schema::hasTable('vitrine_pages')) {
            try {
                $pagesMenu = VitrinePage::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->get(['slug', 'title']);
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine pages menu', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return array_merge([
            'settings' => $settings,
            'pagesMenu' => $pagesMenu,
            'websiteCountdown' => $websiteCountdown,
        ], $data);
    }

    private function pageBySlug(string $slug): ?VitrinePage
    {
        if (! Schema::hasTable('vitrine_pages')) {
            return null;
        }

        return VitrinePage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }
}
