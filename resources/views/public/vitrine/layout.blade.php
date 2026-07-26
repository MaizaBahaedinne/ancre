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
            --brand-navy: #0f2942;
            --brand-teal: #239da0;
            --brand-gold: #efb13e;
            --ink: #11283f;
            --muted: #5a738d;
            --paper: #fffdfa;
            --line: #d8e2ea;
            --section-soft: #f4f9fc;
            --radius-lg: 24px;
            --radius-md: 16px;
            --container: min(1280px, calc(100% - 2rem));
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #ffffff 0%, #f6fbfe 48%, #fffaf0 100%);
        }

        img { max-width: 100%; display: block; }

        .wrap {
            width: var(--container);
            margin-inline: auto;
        }

        .top-ribbon {
            width: 100%;
            background: linear-gradient(90deg, var(--brand-navy), #19466b);
            color: #e8f1f9;
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
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.93);
            border-bottom: 1px solid rgba(15, 41, 66, 0.08);
        }

        .header-inner {
            width: var(--container);
            margin-inline: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.72rem 0;
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
            color: var(--brand-navy);
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
            gap: 0.36rem;
            flex-wrap: wrap;
        }

        .main-nav a {
            text-decoration: none;
            color: var(--ink);
            font-weight: 700;
            font-size: 0.92rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .main-nav a:hover,
        .main-nav a.active {
            background: #fff;
            box-shadow: 0 8px 18px rgba(15, 41, 66, 0.12);
            color: var(--brand-navy);
        }

        .btn-parent {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, var(--brand-navy), #1a4a72);
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 0.68rem 1rem;
            box-shadow: 0 10px 20px rgba(15, 41, 66, 0.22);
            white-space: nowrap;
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
            color: var(--brand-navy);
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
            padding: 1rem;
            box-shadow: 0 16px 26px rgba(15, 41, 66, 0.08);
        }

        .image-card {
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: 0 16px 30px rgba(15, 41, 66, 0.12);
            min-height: 320px;
            background: #e8f3fb;
        }

        .image-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .social-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(15, 41, 66, 0.13);
        }

        .social-thumb {
            border-radius: 12px;
            overflow: hidden;
            min-height: 150px;
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
            background: linear-gradient(135deg, #0f2942, #1b4d74);
            color: #d4e4f2;
            padding: 1.5rem 0 2rem;
            margin-top: 1.2rem;
        }

        .footer-inner {
            width: var(--container);
            margin-inline: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
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
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 1080px) {
            .grid-2,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .social-list,
            .grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
            }

            .main-nav {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.2rem;
            }

            .social-list,
            .grid-3 {
                grid-template-columns: 1fr;
            }

            .btn-parent {
                margin-left: auto;
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

            <nav class="main-nav">
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
                <strong>{{ $settings?->site_name ?: 'Ancre Des Elites' }}</strong>
                <div>{{ $settings?->address ?: 'Adresse a configurer depuis la plateforme' }}</div>
            </div>
            <div class="social-links">
                @if(!empty($settings?->facebook_url))<a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>@endif
                @if(!empty($settings?->instagram_url))<a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
                @if(!empty($settings?->tiktok_url))<a href="{{ $settings->tiktok_url }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>@endif
                @if(!empty($settings?->youtube_url))<a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
            </div>
        </div>
    </footer>
</body>
</html>
