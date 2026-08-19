<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PGXNT48V');</script>
        <!-- End Google Tag Manager -->

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
                background: radial-gradient(circle at 12% 14%, rgba(245, 158, 11, 0.24), transparent 38%), radial-gradient(circle at 90% 84%, rgba(14, 165, 233, 0.18), transparent 42%), linear-gradient(150deg, #041326 0%, #0b2448 55%, #0a3c63 100%);
            }

            .auth-stage {
                min-height: 100vh;
                display: flex;
                align-items: center;
                width: 100%;
            }

            .auth-login-card {
                border: 0;
                border-radius: 1.1rem;
                overflow: hidden;
                box-shadow: 0 28px 65px rgba(2, 8, 23, 0.38);
            }

            .auth-visual-side {
                position: relative;
                min-height: 620px;
                background: linear-gradient(165deg, #0b2448 0%, #0d3c66 62%, #0d5f99 100%);
            }

            .auth-visual-side::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 18% 18%, rgba(245, 158, 11, 0.34), transparent 45%);
            }

            .auth-visual-wrap {
                position: relative;
                z-index: 1;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding: 2rem 1.6rem;
                color: #eef6ff;
            }

            .auth-visual-image {
                width: min(100%, 330px);
                height: auto;
                object-fit: contain;
                filter: drop-shadow(0 18px 28px rgba(3, 13, 32, 0.45));
            }

            .auth-visual-copy {
                margin-top: 1rem;
                text-align: center;
            }

            .auth-visual-copy h2 {
                margin: 0;
                font-size: clamp(1.15rem, 1.6vw, 1.45rem);
                line-height: 1.35;
            }

            .auth-visual-copy p {
                margin: 0.6rem 0 0;
                opacity: 0.9;
                font-size: 0.95rem;
            }

            .auth-form-side {
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            }

            .auth-form-body {
                width: 100%;
                padding: clamp(1.6rem, 2.2vw, 2.5rem);
            }

            .auth-logo-row {
                display: flex;
                align-items: center;
                gap: 0.7rem;
                margin-bottom: 1.1rem;
            }

            .auth-logo-row img {
                width: 40px;
                height: 40px;
                object-fit: contain;
            }

            .auth-logo-row span {
                color: #0b2448;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .auth-title {
                margin: 0;
                color: #0b1b37;
                font-size: clamp(1.35rem, 2vw, 1.75rem);
            }

            .auth-subtitle {
                color: #5f6f89;
                margin: 0.45rem 0 1.4rem;
            }

            .auth-control {
                border-radius: 0.7rem;
                border: 1px solid rgba(100, 116, 139, 0.32);
                min-height: 3.1rem;
            }

            .auth-control:focus {
                border-color: rgba(14, 165, 233, 0.66);
                box-shadow: 0 0 0 0.18rem rgba(14, 165, 233, 0.18);
            }

            .auth-login-btn {
                min-height: 3rem;
                border-radius: 0.75rem;
                border: 0;
                font-weight: 700;
                background: linear-gradient(135deg, #0b2448, #0c7abf);
                box-shadow: 0 12px 24px rgba(12, 122, 191, 0.24);
            }

            .auth-meta,
            .auth-meta a {
                color: #5f6f89;
                font-size: 0.9rem;
            }

            .auth-link {
                color: #0c7abf;
                text-decoration: none;
            }

            .auth-link:hover {
                color: #0b2448;
            }

            @media (max-width: 991.98px) {
                .auth-visual-side {
                    min-height: 540px;
                }
            }

            @media (max-width: 767.98px) {
                .auth-stage {
                    padding: 1rem 0;
                }
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @yield('css')
    </head>
    <body class="modern-admin-shell auth-shell">
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PGXNT48V"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <div class="modern-admin-bg"></div>

        <main class="auth-stage">
            <div class="container py-4 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-xl-10">
                        <div class="card auth-login-card">
                            <div class="row g-0">
                                <div class="col-md-5 d-none d-md-block auth-visual-side">
                                    @php
                                        $kapyImagePath = 'images/auth/kapy.png';
                                        $kapyImageUrl = file_exists(public_path($kapyImagePath))
                                            ? asset($kapyImagePath)
                                            : asset('images/logo encre des elites.webp');
                                    @endphp
                                    <div class="auth-visual-wrap">
                                        <img src="{{ $kapyImageUrl }}" alt="Mascotte Kapy" class="auth-visual-image">
                                        <div class="auth-visual-copy">
                                            <h2>Kapy vous souhaite la bienvenue</h2>
                                            <p>Connexion securisee pour suivre les informations de votre enfant.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7 d-flex align-items-center auth-form-side">
                                    <div class="auth-form-body">
                                        {{ $slot }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @yield('js')
    </body>
</html>
