<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\VitrineNewsletterSubscriber;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class VitrineController extends Controller
{
    public function home(): View
    {
        $testimonials = collect();
        if (Schema::hasTable('vitrine_testimonials')) {
            try {
                $testimonials = VitrineTestimonial::query()
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->latest()
                    ->take(6)
                    ->get();
            } catch (\Throwable $exception) {
                Log::warning('Unable to load vitrine testimonials', [
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $socialPosts = VitrineSocialPost::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('public.vitrine.home', $this->sharedData([
            'currentSlug' => 'home',
            'page' => $this->pageBySlug('home'),
            'aboutPage' => $this->pageBySlug('about'),
            'services' => VitrineService::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'servicesFeatured' => VitrineService::query()->where('is_active', true)->orderBy('sort_order')->take(4)->get(),
            'schedules' => VitrineSchedule::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'socialPosts' => $socialPosts->take(6),
            'activitiesFeatured' => $socialPosts->take(4),
            'blogPosts' => $socialPosts->take(3),
            'professionals' => Personnel::query()->latest('id')->take(4)->get(),
            'testimonials' => $testimonials,
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

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $settings = VitrineSetting::query()->first();
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
