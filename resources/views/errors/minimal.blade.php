<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="modern-admin-shell">
    <div class="modern-admin-bg"></div>

    <main class="container py-5 position-relative" style="z-index:1; min-height:100vh; display:flex; align-items:center;">
        <div class="row justify-content-center w-100">
            <div class="col-xl-8 col-lg-9">
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 32px; background: rgba(255,255,255,0.92); backdrop-filter: blur(18px);">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="modern-brand-mark modern-brand-mark-image">
                                <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo Ancre Des Elites" class="modern-brand-logo">
                            </div>
                            <div>
                                <div class="text-uppercase small fw-bold text-secondary">Ancre Des Elites</div>
                                <div class="fw-semibold text-dark">Interface securisee</div>
                            </div>
                        </div>

                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <div class="display-2 fw-bold text-dark mb-2">@yield('code')</div>
                                <h1 class="h2 fw-bold mb-3">@yield('message')</h1>
                                <p class="text-muted mb-4">@yield('description')</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Retour</a>
                                    <a href="{{ route('home') }}" class="btn btn-primary">Accueil</a>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="p-4 rounded-4 h-100" style="background: linear-gradient(135deg, rgba(14,165,233,0.12), rgba(16,185,129,0.12)); border:1px solid rgba(148,163,184,0.18);">
                                    <div class="mb-3">
                                        <span class="badge bg-dark-subtle text-dark px-3 py-2">@yield('code')</span>
                                    </div>
                                    <div class="fs-1 text-primary mb-3"><i class="@yield('icon', 'fa-solid fa-circle-exclamation')"></i></div>
                                    <p class="mb-0 text-secondary">@yield('hint', 'Si le probleme persiste, contactez l\'administration de la plateforme avec le contexte de la page concernee.') </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>