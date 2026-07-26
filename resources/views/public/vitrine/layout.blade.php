<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Garderie')</title>
    <meta name="description" content="@yield('meta_description', 'Site vitrine de la garderie Ancre Des Elites')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        :root {
            --ink-900: #162a3a;
            --ink-700: #2d475c;
            --ink-500: #607487;
            --surface: #ffffff;
            --surface-soft: #f5f8fb;
            --surface-tint: #eef5f7;
            --line: #d9e4ec;
            --brand: #2d6f85;
            --brand-dark: #1e5062;
            --accent: #d89f54;
            --ok: #2f8d62;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 14px;
            --container: min(1240px, calc(100% - 2rem));
            --shadow-soft: 0 16px 36px rgba(22, 42, 58, 0.09);
        }

        * { box-sizing: border-box; }

        html, body { margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--ink-900);
            background:
                radial-gradient(1100px 440px at 10% -8%, #f8efe0 0%, rgba(248, 239, 224, 0) 55%),
                radial-gradient(980px 420px at 92% 0%, #e9f3f8 0%, rgba(233, 243, 248, 0) 50%),
                #f7fafc;
            line-height: 1.65;
        }

        img { max-width: 100%; display: block; }

        .wrap { width: var(--container); margin-inline: auto; }

        .ribbon {
            background: linear-gradient(90deg, #17384b, #2c6074);
            color: #deebf4;
            font-size: 0.86rem;
        }

        .ribbon-inner {
            width: var(--container);
            margin-inline: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.9rem;
            padding: 0.43rem 0;
            flex-wrap: wrap;
        }

        .ribbon-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.22rem 0.62rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(22, 42, 58, 0.08);
        }

        .header-inner {
            width: var(--container);
            margin-inline: auto;
            padding: 0.74rem 0;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.74rem;
            text-decoration: none;
            color: inherit;
            min-width: 0;
            flex: 1 1 auto;
        }

        .brand-logo {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #fff;
            padding: 0.36rem;
            box-shadow: 0 8px 18px rgba(22, 42, 58, 0.1);
        }

        .brand-title {
            font-family: 'Fraunces', serif;
            font-size: 1.1rem;
            line-height: 1.1;
            color: var(--ink-900);
            margin: 0;
        }

        .brand-sub {
            margin: 0.18rem 0 0;
            font-size: 0.84rem;
            color: var(--ink-500);
        }

        .menu-toggle {
            display: none;
            width: 2.6rem;
            height: 2.6rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            color: var(--ink-900);
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .nav-shell {
            display: flex;
            align-items: center;
            gap: 0.62rem;
        }

        .main-nav {
            display: flex;
            gap: 0.35rem;
            align-items: center;
        }

        .main-nav a {
            text-decoration: none;
            font-weight: 600;
            color: var(--ink-700);
            font-size: 0.94rem;
            padding: 0.56rem 0.88rem;
            border-radius: 999px;
            transition: all 0.25s ease;
        }

        .main-nav a:hover,
        .main-nav a.active {
            color: var(--ink-900);
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(22, 42, 58, 0.12);
        }

        .btn-parent {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.67rem 1rem;
            box-shadow: 0 12px 22px rgba(30, 80, 98, 0.25);
            white-space: nowrap;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .btn-parent:hover { transform: translateY(-1px); box-shadow: 0 16px 24px rgba(30, 80, 98, 0.3); }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 0 0 36px 36px;
            min-height: clamp(430px, 74vh, 760px);
            display: grid;
            align-items: end;
            background: linear-gradient(120deg, rgba(22, 42, 58, 0.84), rgba(45, 111, 133, 0.56));
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: var(--hero-image);
            background-size: cover;
            background-position: center;
            z-index: -2;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: auto 0 0;
            height: 140px;
            background: linear-gradient(180deg, rgba(247, 250, 252, 0), rgba(247, 250, 252, 0.96));
            pointer-events: none;
        }

        .hero-content {
            width: var(--container);
            margin-inline: auto;
            padding: clamp(3.1rem, 8vw, 6rem) 0;
            position: relative;
            z-index: 1;
            animation: rise 0.75s ease both;
        }

        .hero-badge {
            display: inline-flex;
            gap: 0.45rem;
            align-items: center;
            border-radius: 999px;
            background: rgba(216, 159, 84, 0.2);
            border: 1px solid rgba(216, 159, 84, 0.35);
            color: #fff7e6;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.34rem 0.72rem;
        }

        h1, h2, h3 { font-family: 'Fraunces', serif; color: var(--ink-900); }

        h1 {
            color: #fff;
            margin: 0.9rem 0 0.7rem;
            font-size: clamp(2.1rem, 5.5vw, 4rem);
            line-height: 1.08;
            max-width: 16ch;
        }

        .hero-lead {
            color: rgba(235, 245, 252, 0.97);
            max-width: 58ch;
            margin: 0;
            font-size: clamp(1rem, 1.8vw, 1.14rem);
        }

        .hero-actions {
            margin-top: 1.3rem;
            display: flex;
            gap: 0.62rem;
            flex-wrap: wrap;
        }

        .btn-hero, .btn-hero-alt {
            text-decoration: none;
            padding: 0.72rem 1.12rem;
            border-radius: 999px;
            font-size: 0.92rem;
            font-weight: 700;
            display: inline-flex;
            gap: 0.4rem;
            align-items: center;
        }

        .btn-hero { color: var(--ink-900); background: #fff; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18); }
        .btn-hero-alt { color: #fff; border: 1px solid rgba(255, 255, 255, 0.3); background: rgba(255, 255, 255, 0.12); }

        .section { padding: clamp(2.2rem, 5vw, 4rem) 0; }
        .section-soft { background: linear-gradient(180deg, #f3f8fb, #f9fcfe); }
        .section-warm { background: linear-gradient(140deg, #fff8ef, #fffdf8); }

        .section-title {
            margin: 0 0 0.45rem;
            font-size: clamp(1.7rem, 4vw, 2.75rem);
        }

        .section-subtitle { margin: 0; color: var(--ink-500); max-width: 72ch; }

        .grid-2 { margin-top: 1.2rem; display: grid; grid-template-columns: 1.1fr 1fr; gap: 1rem; }
        .grid-3 { margin-top: 1.2rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .grid-4 { margin-top: 1.2rem; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            padding: 1.08rem;
            box-shadow: var(--shadow-soft);
        }

        .panel h3 { margin: 0.2rem 0 0.45rem; font-size: 1.26rem; }

        .icon-chip {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #edf4f8;
            color: var(--brand-dark);
            margin-bottom: 0.64rem;
        }

        .muted { color: var(--ink-500); }

        .media-frame {
            min-height: 320px;
            border-radius: var(--radius-xl);
            border: 1px solid var(--line);
            overflow: hidden;
            background: #eaf2f7;
            box-shadow: var(--shadow-soft);
        }

        .media-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.55s ease;
        }

        .media-frame:hover img { transform: scale(1.03); }

        .stats-grid .panel { text-align: center; }

        .stat-value {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.65rem, 3.4vw, 2.4rem);
            line-height: 1.1;
            color: var(--ink-900);
        }

        .schedule { width: 100%; border-collapse: collapse; }
        .schedule td { padding: 0.64rem 0; border-bottom: 1px dashed var(--line); }
        .schedule td:first-child { color: var(--ink-700); font-weight: 600; }

        .social-grid { margin-top: 1.2rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }

        .social-card {
            text-decoration: none;
            color: inherit;
            display: block;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #fff;
            box-shadow: var(--shadow-soft);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .social-card:hover { transform: translateY(-2px); box-shadow: 0 18px 30px rgba(22, 42, 58, 0.14); }

        .social-thumb { aspect-ratio: 4 / 3; background: #eaf2f7; }
        .social-thumb img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .social-meta { padding: 0.72rem; font-size: 0.92rem; color: var(--ink-500); }

        .form-grid { display: grid; gap: 0.72rem; }
        .form-row { display: grid; gap: 0.7rem; grid-template-columns: 1fr 1fr; }

        label { display: block; margin-bottom: 0.22rem; font-size: 0.86rem; font-weight: 600; color: var(--ink-700); }

        input, textarea {
            width: 100%;
            border: 1px solid #cddae5;
            border-radius: 12px;
            padding: 0.72rem 0.82rem;
            font: inherit;
            color: var(--ink-900);
            background: #fff;
        }

        textarea { resize: vertical; }

        .btn-submit {
            border: 0;
            border-radius: 999px;
            padding: 0.8rem 1rem;
            color: #fff;
            font: inherit;
            font-weight: 700;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            cursor: pointer;
        }

        .alert {
            border-radius: 14px;
            padding: 0.72rem 0.85rem;
            font-size: 0.92rem;
            border: 1px solid;
        }

        .alert-ok { background: #f2fff7; border-color: #b8e5ca; color: #1f6f4e; }
        .alert-err { background: #fff5f5; border-color: #efc8c8; color: #8e3030; }

        .footer {
            margin-top: 1.4rem;
            border-radius: 34px 34px 0 0;
            background: linear-gradient(145deg, #18384d, #295f74);
            color: #d9e7f2;
            padding: 2rem 0 2.4rem;
        }

        .footer-inner {
            width: var(--container);
            margin-inline: auto;
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr;
            gap: 1rem 1.4rem;
        }

        .footer h3, .footer h4 { margin: 0 0 0.45rem; color: #f2f8ff; font-family: 'Fraunces', serif; }
        .footer p { margin: 0; color: #d2e3f0; }

        .footer-nav { display: flex; flex-direction: column; gap: 0.36rem; }
        .footer-nav a { color: #d2e3f0; text-decoration: none; }

        .social-links { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .social-links a {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.26);
            color: #fff;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.1);
            transition: transform 0.25s ease;
        }

        .social-links a:hover { transform: translateY(-2px); }

        .footer-bottom {
            width: var(--container);
            margin: 1.2rem auto 0;
            padding-top: 0.95rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: #bad0e2;
            font-size: 0.88rem;
        }

        .reveal > * { opacity: 0; animation: rise 0.75s ease forwards; }
        .reveal > *:nth-child(2) { animation-delay: 0.09s; }
        .reveal > *:nth-child(3) { animation-delay: 0.18s; }
        .reveal > *:nth-child(4) { animation-delay: 0.27s; }
        .reveal > *:nth-child(5) { animation-delay: 0.36s; }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1080px) {
            .grid-2, .contact-grid { grid-template-columns: 1fr; }
            .grid-3, .social-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 780px) {
            .ribbon-inner { justify-content: flex-start; }
            .menu-toggle { display: inline-flex; }

            .nav-shell {
                position: fixed;
                inset: 75px 1rem auto;
                z-index: 55;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.98);
                box-shadow: var(--shadow-soft);
                padding: 0.55rem;
                display: none;
                flex-direction: column;
                align-items: stretch;
            }

            .nav-shell.open { display: flex; }

            .main-nav {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }

            .main-nav a {
                border-radius: 10px;
                padding: 0.6rem 0.72rem;
            }

            .btn-parent {
                width: 100%;
                text-align: center;
            }

            .grid-3, .social-grid, .grid-4, .form-row, .footer-inner { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="ribbon">
        <div class="ribbon-inner">
            <span class="ribbon-pill"><i class="fa-solid fa-phone"></i> {{ $settings?->phone ?: '+216 XX XXX XXX' }}</span>
            <span class="ribbon-pill"><i class="fa-solid fa-envelope"></i> {{ $settings?->email ?: 'contact@ancredeselites.tn' }}</span>
        </div>
    </div>

    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('vitrine.home') }}" class="brand">
                <img class="brand-logo" src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo {{ $settings?->site_name ?: 'Ancre Des Elites' }}">
                <span>
                    <p class="brand-title">{{ $settings?->site_name ?: 'Ancre Des Elites' }}</p>
                    <p class="brand-sub">{{ $settings?->tagline ?: 'Garderie et eveil' }}</p>
                </span>
            </a>

            <button class="menu-toggle" id="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-nav">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="nav-shell" id="mobile-nav">
                <nav class="main-nav">
                    <a href="{{ route('vitrine.home') }}" class="{{ ($currentSlug ?? '') === 'home' ? 'active' : '' }}">Accueil</a>
                    <a href="{{ route('vitrine.about') }}" class="{{ ($currentSlug ?? '') === 'about' ? 'active' : '' }}">A propos</a>
                    <a href="{{ route('vitrine.services') }}" class="{{ ($currentSlug ?? '') === 'services' ? 'active' : '' }}">Services</a>
                    <a href="{{ route('vitrine.activities') }}" class="{{ ($currentSlug ?? '') === 'activities' ? 'active' : '' }}">Activites</a>
                    <a href="{{ route('vitrine.contact') }}" class="{{ ($currentSlug ?? '') === 'contact' ? 'active' : '' }}">Contact</a>
                </nav>
                <a href="{{ $settings?->parent_space_url ?: route('login') }}" class="btn-parent">Espace parent</a>
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <h3>{{ $settings?->site_name ?: 'Ancre Des Elites' }}</h3>
                <p>{{ $settings?->tagline ?: 'Garderie et eveil' }}</p>
                <p style="margin-top:0.45rem;"><i class="fa-solid fa-location-dot"></i> {{ $settings?->address ?: 'Adresse a configurer depuis la plateforme' }}</p>
            </div>
            <div>
                <h4>Navigation</h4>
                <div class="footer-nav">
                    <a href="{{ route('vitrine.home') }}">Accueil</a>
                    <a href="{{ route('vitrine.about') }}">A propos</a>
                    <a href="{{ route('vitrine.services') }}">Services</a>
                    <a href="{{ route('vitrine.activities') }}">Activites</a>
                    <a href="{{ route('vitrine.contact') }}">Contact</a>
                </div>
            </div>
            <div>
                <h4>Reseaux sociaux</h4>
                <div class="social-links">
                    @if(!empty($settings?->facebook_url))<a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>@endif
                    @if(!empty($settings?->instagram_url))<a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
                    @if(!empty($settings?->tiktok_url))<a href="{{ $settings->tiktok_url }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>@endif
                    @if(!empty($settings?->youtube_url))<a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
                </div>
            </div>
        </div>
        <div class="footer-bottom">© {{ date('Y') }} {{ $settings?->site_name ?: 'Ancre Des Elites' }}. Tous droits reserves.</div>
    </footer>

    <script>
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-nav');

        if (toggle && menu) {
            toggle.addEventListener('click', function () {
                menu.classList.toggle('open');
                toggle.setAttribute('aria-expanded', menu.classList.contains('open') ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                const isInside = menu.contains(event.target) || toggle.contains(event.target);
                if (!isInside) {
                    menu.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    </script>
</body>
</html>
