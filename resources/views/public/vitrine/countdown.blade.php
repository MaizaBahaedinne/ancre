@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Countdown')
@section('meta_description', 'Compte a rebours officiel de la vitrine Ancre Des Elites.')

@section('content')
    @php
        $countdown = is_array($websiteCountdown ?? null) ? $websiteCountdown : [];
        $countdownEnabled = (bool) ($countdown['enabled'] ?? false);
    @endphp

    <main>
        <section class="section" style="padding-top:2rem;">
            <div class="wrap">
                @if($countdownEnabled)
                    <article class="countdown-page-card" data-site-countdown data-target="{{ $countdown['target_iso'] }}" data-expired-label="{{ e($countdown['expired_label']) }}">
                        <header class="countdown-page-head">
                            <span class="countdown-page-badge"><i class="fa-regular fa-clock"></i> Countdown</span>
                            <span>{{ $countdown['timezone'] }}</span>
                        </header>

                        <h1 class="countdown-page-title">{{ $countdown['title'] }}</h1>
                        <p class="countdown-page-subtitle">{{ $countdown['subtitle'] }}</p>

                        <div class="countdown-page-grid" role="timer" aria-live="polite">
                            <div class="countdown-page-cell"><strong data-unit="days">00</strong><span>Jours</span></div>
                            <div class="countdown-page-cell"><strong data-unit="hours">00</strong><span>Heures</span></div>
                            <div class="countdown-page-cell"><strong data-unit="minutes">00</strong><span>Minutes</span></div>
                            <div class="countdown-page-cell"><strong data-unit="seconds">00</strong><span>Secondes</span></div>
                        </div>

                        <p class="countdown-page-note" data-note>Le compte a rebours est en cours.</p>
                    </article>
                @else
                    <article class="countdown-page-empty">
                        <h1>Countdown indisponible</h1>
                        <p>Le compte a rebours est desactive pour le moment.</p>
                        <a href="{{ route('vitrine.home') }}" class="btn-parent">Retour a l'accueil</a>
                    </article>
                @endif
            </div>
        </section>
    </main>

    <style>
        .countdown-page-card {
            max-width: 860px;
            margin: 0 auto;
            background: linear-gradient(130deg, #052a5e, #0d4f72);
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 36px rgba(5, 42, 94, 0.22);
            color: #fff;
            padding: clamp(1rem, 2.2vw, 1.5rem);
        }

        .countdown-page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-bottom: 0.7rem;
        }

        .countdown-page-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.28rem 0.68rem;
            border-radius: 999px;
            background: rgba(201, 142, 53, 0.24);
            border: 1px solid rgba(201, 142, 53, 0.6);
        }

        .countdown-page-title {
            margin: 0;
            color: #fff;
            font-size: clamp(1.35rem, 2.8vw, 2rem);
            line-height: 1.2;
        }

        .countdown-page-subtitle {
            margin: 0.45rem 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .countdown-page-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .countdown-page-cell {
            text-align: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 0.65rem 0.45rem;
        }

        .countdown-page-cell strong {
            display: block;
            font-size: clamp(1.35rem, 3vw, 2.1rem);
            line-height: 1;
        }

        .countdown-page-cell span {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: rgba(255, 255, 255, 0.92);
        }

        .countdown-page-note {
            margin: 0.8rem 0 0;
            font-size: 0.9rem;
        }

        .countdown-page-empty {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d9e4ec;
            border-radius: 18px;
            padding: 1.2rem;
            text-align: center;
        }

        .countdown-page-empty h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .countdown-page-empty p {
            color: #607487;
            margin: 0.4rem 0 1rem;
        }

        @media (max-width: 760px) {
            .countdown-page-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

    @if($countdownEnabled)
        <script>
            (function () {
                var root = document.querySelector('[data-site-countdown]');
                if (!root) {
                    return;
                }

                var target = new Date(root.getAttribute('data-target')).getTime();
                if (Number.isNaN(target)) {
                    return;
                }

                var note = root.querySelector('[data-note]');
                var expiredLabel = root.getAttribute('data-expired-label') || 'Le compte a rebours est termine.';
                var units = {
                    days: root.querySelector('[data-unit="days"]'),
                    hours: root.querySelector('[data-unit="hours"]'),
                    minutes: root.querySelector('[data-unit="minutes"]'),
                    seconds: root.querySelector('[data-unit="seconds"]')
                };

                function pad(value) {
                    return String(value).padStart(2, '0');
                }

                function draw() {
                    var now = Date.now();
                    var diff = target - now;

                    if (diff <= 0) {
                        units.days.textContent = '00';
                        units.hours.textContent = '00';
                        units.minutes.textContent = '00';
                        units.seconds.textContent = '00';
                        if (note) {
                            note.textContent = expiredLabel;
                        }
                        clearInterval(timer);
                        return;
                    }

                    var days = Math.floor(diff / 86400000);
                    var hours = Math.floor((diff % 86400000) / 3600000);
                    var minutes = Math.floor((diff % 3600000) / 60000);
                    var seconds = Math.floor((diff % 60000) / 1000);

                    units.days.textContent = pad(days);
                    units.hours.textContent = pad(hours);
                    units.minutes.textContent = pad(minutes);
                    units.seconds.textContent = pad(seconds);
                }

                draw();
                var timer = setInterval(draw, 1000);
            })();
        </script>
    @endif
@endsection
