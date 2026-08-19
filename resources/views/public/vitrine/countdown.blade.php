@php
    $countdown = is_array($websiteCountdown ?? null) ? $websiteCountdown : [];
    $countdownEnabled = (bool) ($countdown['enabled'] ?? false);
    $siteName = $settings?->site_name ?: 'Ancre Des Elites';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName }} | Countdown</title>
    <meta name="description" content="Compte a rebours officiel {{ $siteName }}.">
    <link rel="icon" type="image/png" href="{{ asset('images/fav_ico.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --ink: #062648;
            --ink-soft: #365877;
            --accent: #c98e35;
            --paper: #f8fbff;
            --line: #d7e3ef;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1rem;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 9% 14%, rgba(201, 142, 53, 0.25), transparent 36%),
                radial-gradient(circle at 86% 12%, rgba(12, 122, 191, 0.2), transparent 30%),
                linear-gradient(165deg, #edf5fb 0%, #f5f9fd 45%, #fffdfa 100%);
        }

        .countdown-shell {
            width: min(920px, 100%);
            background: #fff;
            border-radius: 28px;
            border: 1px solid var(--line);
            box-shadow: 0 28px 60px rgba(6, 38, 72, 0.15);
            padding: clamp(1.1rem, 2.6vw, 2rem);
        }

        .head {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            text-align: center;
        }

        .head img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .head h1 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: clamp(1.35rem, 2.6vw, 2rem);
            line-height: 1.15;
        }

        .kicker {
            margin: 0.85rem auto 0;
            text-align: center;
            color: var(--ink-soft);
            max-width: 66ch;
        }

        .timer-box {
            margin-top: 1.1rem;
            background: linear-gradient(135deg, #052a5e, #0d4f72);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
            padding: 1rem;
        }

        .timer-head {
            display: flex;
            justify-content: space-between;
            gap: 0.6rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .timer-badge {
            display: inline-flex;
            gap: 0.45rem;
            align-items: center;
            border-radius: 999px;
            padding: 0.28rem 0.72rem;
            background: rgba(201, 142, 53, 0.2);
            border: 1px solid rgba(201, 142, 53, 0.5);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
        }

        .timer-title {
            margin: 0.65rem 0 0;
            color: #fff;
            font-size: clamp(1.05rem, 1.8vw, 1.4rem);
            line-height: 1.25;
        }

        .timer-subtitle {
            margin: 0.35rem 0 0;
            color: rgba(255, 255, 255, 0.92);
        }

        .timer-grid {
            margin-top: 0.85rem;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .timer-cell {
            text-align: center;
            border-radius: 14px;
            padding: 0.72rem 0.4rem;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .timer-cell strong {
            display: block;
            font-size: clamp(1.45rem, 2.8vw, 2rem);
            line-height: 1;
            font-family: 'Fraunces', serif;
        }

        .timer-cell span {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .timer-note {
            margin: 0.7rem 0 0;
            font-size: 0.9rem;
        }

        .newsletter {
            margin-top: 1.05rem;
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0.95rem;
        }

        .newsletter h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .newsletter p {
            margin: 0.3rem 0 0.75rem;
            color: var(--ink-soft);
        }

        .newsletter form {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .newsletter input {
            flex: 1 1 260px;
            min-height: 2.9rem;
            border-radius: 999px;
            border: 1px solid #cbd8e7;
            padding: 0 0.95rem;
            font-size: 0.96rem;
        }

        .newsletter button {
            min-height: 2.9rem;
            border: 0;
            border-radius: 999px;
            padding: 0 1.15rem;
            font-weight: 700;
            background: linear-gradient(135deg, #052a5e, #0c7abf);
            color: #fff;
            cursor: pointer;
        }

        .alert {
            border-radius: 11px;
            padding: 0.6rem 0.72rem;
            margin: 0 0 0.75rem;
            font-size: 0.92rem;
        }

        .alert-ok {
            background: #e9f7f0;
            border: 1px solid #95d5b2;
            color: #186d43;
        }

        .alert-err {
            background: #fff0f2;
            border: 1px solid #f5b6c4;
            color: #8b233f;
        }

        .alert-err ul {
            margin: 0;
            padding-left: 1.1rem;
        }

        .is-off {
            text-align: center;
            padding: 1.2rem 0.5rem;
        }

        @media (max-width: 760px) {
            .timer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <main class="countdown-shell">
        <header class="head">
            <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo {{ $siteName }}">
            <h1>{{ $siteName }}</h1>
        </header>

        @if($countdownEnabled)
            <p class="kicker">Le site sera disponible apres la fin du compte a rebours. Merci de votre patience.</p>
            <section class="timer-box" data-site-countdown data-target="{{ $countdown['target_iso'] }}" data-expired-label="{{ e($countdown['expired_label']) }}">
                <div class="timer-head">
                    <span class="timer-badge"><i class="fa-regular fa-clock"></i> Countdown</span>
                    <span>{{ $countdown['timezone'] }}</span>
                </div>
                <h2 class="timer-title">{{ $countdown['title'] }}</h2>
                <p class="timer-subtitle">{{ $countdown['subtitle'] }}</p>

                <div class="timer-grid" role="timer" aria-live="polite">
                    <div class="timer-cell"><strong data-unit="days">00</strong><span>Jours</span></div>
                    <div class="timer-cell"><strong data-unit="hours">00</strong><span>Heures</span></div>
                    <div class="timer-cell"><strong data-unit="minutes">00</strong><span>Minutes</span></div>
                    <div class="timer-cell"><strong data-unit="seconds">00</strong><span>Secondes</span></div>
                </div>
                <p class="timer-note" data-note>Le compte a rebours est en cours.</p>
            </section>

            <section class="newsletter">
                <h2>Newsletter</h2>
                <p>Recevez une alerte quand le site sera de nouveau accessible.</p>

                @if(session('newsletter_success'))
                    <div class="alert alert-ok">{{ session('newsletter_success') }}</div>
                @endif

                @if($errors->newsletter->any())
                    <div class="alert alert-err">
                        <ul>
                            @foreach($errors->newsletter->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('vitrine.newsletter.subscribe') }}">
                    @csrf
                    <input type="hidden" name="source_page" value="countdown">
                    <input type="email" name="newsletter_email" placeholder="Votre email" required>
                    <button type="submit">S'abonner</button>
                </form>
            </section>
        @else
            <section class="is-off">
                <h2 style="margin:0;">Countdown inactif</h2>
                <p style="color:var(--ink-soft);">Le countdown est desactive actuellement.</p>
                <a href="{{ route('vitrine.home') }}" style="color:#0c7abf;text-decoration:none;font-weight:700;">Aller vers le site</a>
            </section>
        @endif
    </main>

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
</body>
</html>
