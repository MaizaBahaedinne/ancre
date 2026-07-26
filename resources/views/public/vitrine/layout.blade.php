<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Garderie')</title>
    <meta name="description" content="@yield('meta_description', 'Site vitrine de la garderie Ancre Des Elites')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        :root {
            --brand-ink: #123047;
            --brand-cyan: #58a8b2;
            --brand-sand: #f1e4cc;
            --brand-accent: #dd8f3d;
            --ink: #1d3141;
            --muted: #607385;
            --paper: #ffffff;
            --line: #dce6ed;
            --section-soft: #f5f9fc;
            --radius-lg: 28px;
            --radius-md: 18px;
            --container: min(1280px, calc(100% - 2rem));
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 0%, #f8f5ef 0%, rgba(248, 245, 239, 0) 33%),
                radial-gradient(circle at 90% 10%, #edf7f8 0%, rgba(237, 247, 248, 0) 30%),
                #f8fbfd;
            line-height: 1.6;
        }

        img { max-width: 100%; display: block; }

        .wrap {
            width: var(--container);
            margin-inline: auto;
        }

        .top-ribbon {
            width: 100%;
            background: linear-gradient(90deg, #17354b, #245b70);
            color: #e6eef6;
            font-size: 0.85rem;
        }

        .top-ribbon-inner {
            width: var(--container);
            margin-inline: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.45rem 0;
        }

        .top-ribbon a {
            color: inherit;
            text-decoration: none;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(18, 48, 71, 0.09);
        }

        .header-inner {
            width: var(--container);
            margin-inline: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.72rem 0;
            position: relative;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .brand img { width: 48px; height: 48px; }

        .brand strong {
            display: block;
            font-family: 'Playfair Display', serif;
            color: var(--brand-ink);
            font-size: 1.1rem;
            line-height: 1.05;
        }

        .brand small {
            color: var(--muted);
            font-size: 0.8rem;
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 0.42rem;
            flex-wrap: wrap;
        }

        .main-nav a {
            text-decoration: none;
            color: var(--ink);
            font-weight: 700;
            font-size: 0.92rem;
            padding: 0.58rem 0.95rem;
            border-radius: 999px;
            transition: all 0.25s ease;
        }

        .main-nav a:hover,
        .main-nav a.active {
            background: #ffffff;
            box-shadow: 0 10px 22px rgba(18, 48, 71, 0.14);
            color: var(--brand-ink);
        }

        .nav-toggle {
            display: none;
            width: 2.55rem;
            height: 2.55rem;
            border: 1px solid rgba(18, 48, 71, 0.16);
            border-radius: 14px;
            background: #fff;
            color: var(--brand-ink);
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-parent {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #19405d, #2a6b7b);
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 0.68rem 1rem;
            box-shadow: 0 10px 20px rgba(25, 64, 93, 0.22);
            white-space: nowrap;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .btn-parent:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(25, 64, 93, 0.27);
        }

        .hero-shell {
            width: 100%;
            min-height: clamp(420px, 72vh, 760px);
            display: grid;
            align-items: stretch;
            background:
                linear-gradient(110deg, rgba(15, 41, 66, 0.84), rgba(15, 41, 66, 0.54)),
                url('https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 34px 34px;
        }

        .hero-shell::after {
            content: '';
            position: absolute;
            inset: auto 0 0;
            height: 110px;
            background: linear-gradient(180deg, rgba(255,255,255,0), rgba(255,255,255,0.94));
            pointer-events: none;
        }

        .hero-content {
            width: var(--container);
            margin-inline: auto;
            display: grid;
            align-content: center;
            padding: clamp(3.4rem, 8vw, 6rem) 0;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.7s ease both;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            width: fit-content;
            padding: 0.36rem 0.76rem;
            border-radius: 999px;
            background: rgba(239, 177, 62, 0.2);
            color: #fff1cf;
            border: 1px solid rgba(239, 177, 62, 0.35);
            font-size: 0.79rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            color: var(--brand-ink);
        }

        h1 {
            color: #ffffff;
            font-size: clamp(2.1rem, 5.5vw, 4.1rem);
            line-height: 1.08;
            margin: 0.9rem 0 0.75rem;
            max-width: 16ch;
        }

        .hero-lead {
            max-width: 58ch;
            color: rgba(232, 244, 255, 0.95);
            font-size: clamp(0.98rem, 1.7vw, 1.12rem);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-top: 1.25rem;
        }

        .btn-hero,
        .btn-hero-alt {
            text-decoration: none;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.92rem;
            padding: 0.7rem 1.15rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .btn-hero {
            color: #1e3650;
            background: #fff;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-alt {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .section {
            width: 100%;
            padding: clamp(2.2rem, 5vw, 4.2rem) 0;
        }

        .section-soft {
            background: linear-gradient(180deg, #f3f9fd, #fbfeff);
        }

        .section-band {
            background: linear-gradient(135deg, #fff9ee, #fffef8);
        }

        .section-title {
            margin: 0 0 0.35rem;
            font-size: clamp(1.7rem, 3.9vw, 2.75rem);
        }

        .section-subtitle {
            margin: 0;
            color: var(--muted);
            max-width: 70ch;
        }

        .grid-2 {
            margin-top: 1.3rem;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1rem;
        }

        .grid-3 {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.1rem;
            box-shadow: 0 16px 30px rgba(18, 48, 71, 0.08);
            animation: fadeInUp 0.75s ease both;
        }

        .image-card {
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: 0 20px 34px rgba(18, 48, 71, 0.12);
            min-height: 320px;
            background: #e8f3fb;
        }

        .image-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.6s ease;
        }

        .image-card:hover img {
            transform: scale(1.03);
        }

        .feature-icon {
            width: 2.65rem;
            height: 2.65rem;
            border-radius: 0.8rem;
            background: rgba(239, 177, 62, 0.2);
            color: #865a0b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.68rem;
        }

        .text-muted {
            color: var(--muted);
        }

        .contact-grid {
            margin-top: 1.15rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .schedule {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule td {
            padding: 0.68rem 0;
            border-bottom: 1px dashed var(--line);
        }

        .schedule td:first-child {
            font-weight: 700;
            color: var(--ink);
        }

        .social-list {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .social-item {
            text-decoration: none;
            color: inherit;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 0.55rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .social-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(15, 41, 66, 0.13);
        }

        .social-thumb {
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: #edf6fc;
            display: grid;
            place-items: center;
        }

        .social-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .social-meta {
            margin-top: 0.48rem;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .footer {
            background: linear-gradient(145deg, #16364d, #24586e);
            color: #d5e4f1;
            padding: 2rem 0 2.4rem;
            margin-top: 1.2rem;
            border-radius: 34px 34px 0 0;
        }

        .footer-inner {
            width: var(--container);
            margin-inline: auto;
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr;
            gap: 1rem 1.5rem;
        }

        .footer h3,
        .footer h4 {
            margin: 0 0 0.45rem;
            color: #f0f7ff;
            font-family: 'Playfair Display', serif;
        }

        .footer p {
            margin: 0;
            color: #d5e4f1;
        }

        .footer-nav {
            display: flex;
            flex-direction: column;
            gap: 0.38rem;
        }

        .footer-nav a {
            text-decoration: none;
            color: #d5e4f1;
        }

        .footer-bottom {
            width: var(--container);
            margin: 1.2rem auto 0;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.17);
            font-size: 0.88rem;
            color: #c0d4e6;
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .social-links a {
            width: 2.3rem;
            height: 2.3rem;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
            transition: transform 0.25s ease, background 0.25s ease;
        }

        .social-links a:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stagger > * {
            opacity: 0;
            animation: fadeInUp 0.7s ease forwards;
        }

        .stagger > *:nth-child(2) { animation-delay: 0.08s; }
        .stagger > *:nth-child(3) { animation-delay: 0.16s; }
        .stagger > *:nth-child(4) { animation-delay: 0.24s; }
        .stagger > *:nth-child(5) { animation-delay: 0.32s; }
        .stagger > *:nth-child(6) { animation-delay: 0.4s; }

        .stats-grid {
            margin-top: 1.3rem;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.9rem;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1rem;
            box-shadow: 0 12px 24px rgba(18, 48, 71, 0.08);
        }

        .stat-number {
            font-size: clamp(1.6rem, 3.2vw, 2.3rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--brand-ink);
            font-family: 'Playfair Display', serif;
        }

        .mv-grid {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .contact-form {
            display: grid;
            gap: 0.75rem;
        }

        .contact-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.7rem;
        }

        .contact-form label {
            font-size: 0.86rem;
            color: var(--muted);
            font-weight: 700;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border: 1px solid #cddae5;
            border-radius: 12px;
            padding: 0.72rem 0.82rem;
            font: inherit;
            background: #fff;
        }

        .contact-form button {
            border: 0;
            border-radius: 999px;
            padding: 0.8rem 1rem;
            font-weight: 800;
            font: inherit;
            color: #fff;
            background: linear-gradient(135deg, #19405d, #2a6b7b);
            cursor: pointer;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1080px) {
            .grid-2,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .mv-grid,
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .social-list,
            .grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 760px) {
            .top-ribbon-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.35rem;
            }

            .header-inner {
                flex-wrap: wrap;
                gap: 0.65rem;
            }

            .nav-toggle {
                display: inline-flex;
                margin-left: auto;
            }

            .main-nav {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                background: #fff;
                border: 1px solid rgba(18, 48, 71, 0.12);
                border-radius: 16px;
                padding: 0.5rem;
            }

            .main-nav.open {
                display: flex;
            }

            .main-nav a {
                border-radius: 12px;
            }

            .social-list,
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .stats-grid,
            .mv-grid,
            .contact-form-row,
            .footer-inner {
                grid-template-columns: 1fr;
            }

            .btn-parent {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="top-ribbon">
        <div class="top-ribbon-inner">
            <span>
                <i class="fa-solid fa-phone"></i>
                {{ $settings?->phone ?: '+216 XX XXX XXX' }}
            </span>
            <span>
                <i class="fa-solid fa-envelope"></i>
                {{ $settings?->email ?: 'contact@ancredeselites.tn' }}
            </span>
        </div>
    </div>

    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('vitrine.home') }}" class="brand">
                <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo {{ $settings?->site_name ?: 'Ancre Des Elites' }}">
                <span>
                    <strong>{{ $settings?->site_name ?: 'Ancre Des Elites' }}</strong>
                    <small>{{ $settings?->tagline ?: 'Garderie et eveil' }}</small>
                </span>
            </a>

            <button class="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="vitrine-main-nav" id="vitrine-nav-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>

            <nav class="main-nav" id="vitrine-main-nav">
                <a href="{{ route('vitrine.home') }}" class="{{ ($currentSlug ?? '') === 'home' ? 'active' : '' }}">Accueil</a>
                <a href="{{ route('vitrine.about') }}" class="{{ ($currentSlug ?? '') === 'about' ? 'active' : '' }}">A propos</a>
                <a href="{{ route('vitrine.services') }}" class="{{ ($currentSlug ?? '') === 'services' ? 'active' : '' }}">Services</a>
                <a href="{{ route('vitrine.activities') }}" class="{{ ($currentSlug ?? '') === 'activities' ? 'active' : '' }}">Activites</a>
                <a href="{{ route('vitrine.contact') }}" class="{{ ($currentSlug ?? '') === 'contact' ? 'active' : '' }}">Contact</a>
            </nav>

            <a href="{{ $settings?->parent_space_url ?: route('login') }}" class="btn-parent">Espace parent</a>
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
        <div class="footer-bottom">
            © {{ date('Y') }} {{ $settings?->site_name ?: 'Ancre Des Elites' }}. Tous droits reserves.
        </div>
    </footer>

    <script>
        const navToggle = document.getElementById('vitrine-nav-toggle');
        const navMenu = document.getElementById('vitrine-main-nav');

        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function () {
                navMenu.classList.toggle('open');
                const expanded = navMenu.classList.contains('open');
                navToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            });
        }
    </script>
</body>
</html>
