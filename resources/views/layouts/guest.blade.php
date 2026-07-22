<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <style>
            body.auth-shell {
                margin: 0;
                min-height: 100vh;
                font-family: 'Sora', 'Figtree', sans-serif;
                display: grid;
                place-items: center;
                padding: 1rem;
                background: radial-gradient(circle at 85% 12%, rgba(14, 165, 233, 0.2), transparent 25%), radial-gradient(circle at 8% 85%, rgba(245, 158, 11, 0.15), transparent 30%), linear-gradient(160deg, #ecf4fb 0%, #f6f8fb 48%, #eef2f9 100%);
            }

            .auth-layout {
                width: min(1120px, 100%);
                display: grid;
                grid-template-columns: minmax(0, 1.3fr) minmax(360px, 450px);
                gap: 1rem;
                align-items: stretch;
            }

            .auth-brand-wrap {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 1.5rem;
                padding: 2rem;
                border-radius: 28px;
                background: linear-gradient(145deg, rgba(13, 34, 66, 0.95), rgba(13, 56, 108, 0.9));
                color: #eff6ff;
                box-shadow: 0 24px 60px rgba(8, 27, 53, 0.27);
            }

            .auth-brand-link {
                display: flex;
                align-items: center;
                gap: 1rem;
                color: #eff6ff;
                text-decoration: none;
            }

            .auth-brand-link:hover {
                color: #eff6ff;
            }

            .auth-brand-mark {
                width: 4.2rem;
                height: 4.2rem;
                border-radius: 22px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.06));
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .auth-brand-logo {
                width: 2.45rem;
                height: 2.45rem;
                object-fit: contain;
            }

            .auth-brand-link strong,
            .auth-brand-link small {
                display: block;
            }

            .auth-brand-link small {
                opacity: 0.9;
            }

            .auth-showcase-title {
                margin: 0.4rem 0;
                font-size: clamp(1.45rem, 2vw, 2rem);
                line-height: 1.22;
            }

            .auth-showcase-text {
                margin: 0;
                opacity: 0.92;
            }

            .auth-showcase-points {
                margin: 0;
                padding: 1rem;
                list-style: none;
                display: grid;
                gap: 0.65rem;
                border-radius: 16px;
                background: rgba(8, 27, 53, 0.25);
                border: 1px solid rgba(125, 211, 252, 0.28);
            }

            .auth-showcase-points li {
                display: flex;
                gap: 0.65rem;
                align-items: flex-start;
            }

            .auth-card-shell {
                padding: 2rem;
                border-radius: 28px;
                background: rgba(255, 255, 255, 0.97);
                box-shadow: 0 22px 60px rgba(15, 23, 42, 0.13);
                border: 1px solid rgba(148, 163, 184, 0.24);
            }

            .auth-title {
                margin: 0;
                color: #0b1b37;
            }

            .auth-subtitle {
                color: #5f6f89;
            }

            .auth-form-grid {
                display: grid;
                gap: 1rem;
            }

            .auth-input-wrap {
                display: grid;
                grid-template-columns: 2.65rem minmax(0, 1fr);
                align-items: center;
                border: 1px solid rgba(148, 163, 184, 0.35);
                border-radius: 14px;
                background: #fff;
            }

            .auth-input-wrap:focus-within {
                border-color: rgba(14, 165, 233, 0.66);
                box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
            }

            .auth-input-wrap i {
                text-align: center;
                color: #64748b;
            }

            .auth-input {
                min-height: 2.9rem;
                border: 0;
                box-shadow: none;
            }

            .auth-input:focus {
                box-shadow: none;
            }

            .auth-form-row,
            .auth-assist-row {
                display: flex;
                justify-content: space-between;
                gap: 0.75rem;
                align-items: center;
            }

            .auth-assist-row {
                font-size: 0.8rem;
                font-weight: 700;
                padding: 0.6rem 0.75rem;
                border-radius: 12px;
                background: linear-gradient(135deg, rgba(14, 165, 233, 0.09), rgba(59, 130, 246, 0.07));
                border: 1px solid rgba(148, 163, 184, 0.2);
            }

            .auth-submit-btn {
                min-height: 2.95rem;
                border-radius: 14px;
                border: 0;
                font-weight: 800;
                background: linear-gradient(135deg, #0b2448, #0c7abf);
                box-shadow: 0 14px 28px rgba(12, 122, 191, 0.3);
            }

            @media (max-width: 991.98px) {
                .auth-layout {
                    grid-template-columns: 1fr;
                    max-width: 560px;
                }

                .auth-brand-wrap {
                    padding: 1.4rem;
                }
            }

            @media (max-width: 767.98px) {
                .auth-brand-wrap {
                    display: none;
                }

                .auth-card-shell {
                    padding: 1.2rem;
                    border-radius: 20px;
                }

                .auth-form-row,
                .auth-assist-row {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @yield('css')
    </head>
    <body class="modern-admin-shell auth-shell">
        <div class="modern-admin-bg"></div>

        <main class="auth-layout">
            <div class="auth-orb auth-orb-one"></div>
            <div class="auth-orb auth-orb-two"></div>

            <section class="auth-brand-wrap">
                <a href="{{ route('home') }}" class="auth-brand-link">
                    <span class="auth-brand-mark">
                        <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo Ancre Des Elites" class="auth-brand-logo">
                    </span>
                    <span>
                        <strong>Ancre Des Elites</strong>
                        <small>Espace authentification</small>
                    </span>
                </a>

                <div class="auth-showcase-copy">
                    <p class="auth-showcase-kicker">Administration unifiee</p>
                    <h2 class="auth-showcase-title">Une plateforme claire pour diriger l'ecole en temps reel.</h2>
                    <p class="auth-showcase-text">Suivez les inscriptions, paiements, presences et demandes parentales avec des interfaces adaptees a chaque role.</p>
                </div>

                <ul class="auth-showcase-points">
                    <li><i class="fa-solid fa-circle-check"></i><span>Suivi quotidien et indicateurs en temps reel</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Gestion centralisee des inscriptions et paiements</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Experience adaptee selon le role utilisateur</span></li>
                </ul>
            </section>

            <div class="auth-card-shell">
                {{ $slot }}
            </div>
        </main>

        @yield('js')
    </body>
</html>
