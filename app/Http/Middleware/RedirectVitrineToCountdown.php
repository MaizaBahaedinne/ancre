<?php

namespace App\Http\Middleware;

use App\Models\VitrineSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RedirectVitrineToCountdown
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request) || ! $this->isCountdownActive()) {
            return $next($request);
        }

        if ($request->routeIs('vitrine.countdown') || $request->is('countdown') || $request->is('vitrine/countdown')) {
            return $next($request);
        }

        return redirect()->route('vitrine.countdown');
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->routeIs('vitrine.countdown') || $request->routeIs('vitrine.newsletter.subscribe')) {
            return true;
        }

        return $request->is('countdown')
            || $request->is('vitrine/countdown')
            || $request->is('newsletter/subscribe')
            || $request->is('vitrine/newsletter/subscribe');
    }

    private function isCountdownActive(): bool
    {
        if (! Schema::hasTable('vitrine_settings')) {
            return false;
        }

        $requiredColumns = ['countdown_enabled', 'countdown_target_at', 'countdown_timezone'];
        foreach ($requiredColumns as $column) {
            if (! Schema::hasColumn('vitrine_settings', $column)) {
                return false;
            }
        }

        $settings = VitrineSetting::query()->first();

        if (! $settings || ! (bool) $settings->countdown_enabled) {
            return false;
        }

        if (empty($settings->countdown_target_at)) {
            return true;
        }

        try {
            $timezone = ! empty($settings->countdown_timezone) ? (string) $settings->countdown_timezone : 'Africa/Tunis';
            $target = $settings->countdown_target_at instanceof Carbon
                ? $settings->countdown_target_at->copy()->setTimezone($timezone)
                : Carbon::parse((string) $settings->countdown_target_at, $timezone);

            return now($timezone)->lt($target);
        } catch (\Throwable $e) {
            return true;
        }
    }
}
