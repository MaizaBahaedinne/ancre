@php
    $countdown = is_array($websiteCountdown ?? null) ? $websiteCountdown : [];
    $countdownEnabled = (bool) ($countdown['enabled'] ?? false);
    $siteName = $settings?->site_name ?: 'Ancre Des Elites';
    $countdownBackground = asset('images/vitrine/vitrine-05.jpg');
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
            padding: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background: #071a33;
        }

        .countdown-hero {
            min-height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(3, 17, 35, 0.94) 0%, rgba(3, 17, 35, 0.82) 38%, rgba(3, 17, 35, 0.52) 100%),
                url('{{ $countdownBackground }}') center/cover no-repeat;
        }

        .countdown-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 24%, rgba(201, 142, 53, 0.24), transparent 22%),
                radial-gradient(circle at 82% 16%, rgba(12, 122, 191, 0.16), transparent 24%);
            pointer-events: none;
        }

        .countdown-grid {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            align-items: center;
            gap: 0;
            width: min(1260px, calc(100% - 2rem));
            margin-inline: auto;
            padding: clamp(1.2rem, 3vw, 3rem) 0;
        }

        .countdown-copy {
            color: #fff;
            max-width: 72rem;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1rem;
        }

        .brand-row img {
            width: 62px;
            height: 62px;
            object-fit: contain;
            flex: 0 0 auto;
            filter: drop-shadow(0 12px 26px rgba(0, 0, 0, 0.35));
        }

        .brand-row h1 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: clamp(1.5rem, 3vw, 2.4rem);
            line-height: 1.1;
            color: #fff;
        }

        .brand-row p {
            margin: 0.25rem 0 0;
            color: rgba(255, 255, 255, 0.82);
        }

        .hero-title {
            margin: 0.2rem 0 0;
            font-family: 'Fraunces', serif;
            color: #fff;
            font-size: clamp(2rem, 4vw, 4.25rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
            max-width: 18ch;
        }

        .hero-text {
            margin: 1rem 0 0;
            max-width: 72ch;
            color: rgba(255, 255, 255, 0.88);
            font-size: 1rem;
        }

        .timer-box {
            margin-top: 1.35rem;
            background: rgba(8, 28, 52, 0.62);
            border-radius: 22px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.28);
            color: #fff;
            padding: 1.15rem;
            backdrop-filter: blur(10px);
            max-width: 760px;
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
            font-size: clamp(1.15rem, 2vw, 1.65rem);
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
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.93);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 22px;
            padding: 1rem;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(8px);
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
            .countdown-grid {
                width: min(100% - 1rem, 100%);
                gap: 1rem;
            }

            .timer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hero-title {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <main class="countdown-hero">
        <div class="countdown-grid">
            <section class="countdown-copy">
                <div class="brand-row">
                    <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo {{ $siteName }}">
                    <div>
                        <h1>{{ $siteName }}</h1>
                        <p>Page d'attente officielle</p>
                    </div>
                </div>

                @if($countdownEnabled)
                    <h2 class="hero-title">Une nouvelle aventure educative commence bientot.</h2>
                    <p class="hero-text">L'espace vitrine est temporairement ferme pendant la preparation du lancement. Le site principal redeviendra accessible automatiquement a la fin du compte a rebours.</p>

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
                        <h2>Recevoir l'ouverture</h2>
                        <p>Inscrivez-vous pour etre averti quand le site revient en ligne.</p>

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
                    <section class="newsletter" style="max-width:42rem;">
                        <h2>Countdown inactif</h2>
                        <p>Le compte a rebours est desactive actuellement.</p>
                        <a href="{{ route('vitrine.home') }}" style="display:inline-block;margin-top:0.25rem;color:#0c7abf;text-decoration:none;font-weight:700;">Aller vers le site</a>
                    </section>
                @endif
            </section>
        </div>
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
