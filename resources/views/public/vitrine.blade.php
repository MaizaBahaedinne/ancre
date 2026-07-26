<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ancre Des Elites | Garderie et Eveil</title>
    <meta name="description" content="Garderie Ancre Des Elites: accueil des enfants, activites d'eveil, accompagnement scolaire, repas et suivi personnalise.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        :root {
            --ink: #0f2942;
            --ink-soft: #355472;
            --sand: #f9f5ec;
            --paper: #fffdfa;
            --sun: #f4b740;
            --reef: #2aa6a1;
            --berry: #b24a6a;
            --line: #d8e0e7;
            --shadow: 0 20px 40px rgba(15, 41, 66, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 8%, rgba(244, 183, 64, 0.25), transparent 28%),
                radial-gradient(circle at 86% 10%, rgba(42, 166, 161, 0.18), transparent 22%),
                linear-gradient(180deg, #fffefb 0%, #f7fbfd 40%, #f9f5ec 100%);
            line-height: 1.6;
        }

        .container {
            width: min(1120px, calc(100% - 2rem));
            margin-inline: auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(10px);
            background: rgba(255, 253, 250, 0.88);
            border-bottom: 1px solid rgba(15, 41, 66, 0.08);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-decoration: none;
            color: inherit;
        }

        .brand img { width: 42px; height: 42px; }

        .brand strong {
            display: block;
            font-family: 'Fraunces', serif;
            letter-spacing: 0.02em;
            font-size: 1.05rem;
        }

        .brand small { color: var(--ink-soft); font-size: 0.8rem; }

        .top-links {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            border: 0;
            border-radius: 999px;
            padding: 0.68rem 1.1rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-main { background: var(--ink); color: #fff; box-shadow: var(--shadow); }
        .btn-soft { background: #fff; color: var(--ink); border: 1px solid var(--line); }

        .hero { padding: 4rem 0 2.8rem; }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.4rem;
            align-items: stretch;
        }

        .hero-copy {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #fff;
            border-radius: 28px;
            padding: clamp(1.3rem, 3vw, 2.2rem);
            box-shadow: var(--shadow);
            animation: rise 0.8s ease both;
        }

        .eyebrow {
            display: inline-block;
            padding: 0.28rem 0.72rem;
            border-radius: 999px;
            background: rgba(42, 166, 161, 0.16);
            color: #0f5c59;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0.85rem 0 0.8rem;
            font-family: 'Fraunces', serif;
            font-weight: 800;
            line-height: 1.1;
            font-size: clamp(2rem, 5.4vw, 3.5rem);
        }

        .hero-copy p {
            margin: 0;
            color: var(--ink-soft);
            max-width: 56ch;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
            margin-top: 1.25rem;
        }

        .hero-stats {
            margin-top: 1.4rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .hero-stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 0.72rem;
        }

        .hero-stat strong {
            display: block;
            font-family: 'Fraunces', serif;
            font-size: 1.4rem;
        }

        .hero-card {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            min-height: 340px;
            background:
                linear-gradient(140deg, rgba(15, 41, 66, 0.92), rgba(22, 70, 101, 0.85)),
                url('https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=80') center/cover;
            color: #fff;
            padding: 1.5rem;
            display: flex;
            align-items: flex-end;
            box-shadow: var(--shadow);
            animation: rise 0.95s ease both;
        }

        .hero-card .note {
            backdrop-filter: blur(4px);
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 0.8rem 0.95rem;
        }

        .hero-card .note strong {
            display: block;
            font-size: 1.1rem;
        }

        .section { padding: 2.6rem 0; }

        .section h2 {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.6rem, 4vw, 2.4rem);
            margin: 0 0 0.5rem;
        }

        .muted { color: var(--ink-soft); }

        .cards {
            margin-top: 1.15rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.95rem;
        }

        .card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1rem;
            box-shadow: 0 12px 22px rgba(15, 41, 66, 0.08);
        }

        .card .icon {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(244, 183, 64, 0.22);
            color: #7a5100;
            margin-bottom: 0.7rem;
        }

        .program {
            margin-top: 1.1rem;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
        }

        .program-row {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 1rem;
            padding: 0.95rem 1rem;
            border-bottom: 1px dashed var(--line);
        }

        .program-row:last-child { border-bottom: 0; }

        .quote {
            margin-top: 1.1rem;
            border-left: 5px solid var(--berry);
            background: #fff6f8;
            border-radius: 12px;
            padding: 1rem;
        }

        .contact {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            align-items: stretch;
        }

        .contact-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1rem;
        }

        .contact-list {
            list-style: none;
            margin: 0.7rem 0 0;
            padding: 0;
            display: grid;
            gap: 0.6rem;
        }

        .contact-list li {
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }

        .contact-list i { color: var(--reef); }

        .cta {
            background: linear-gradient(130deg, #0f2942, #1b4d75);
            color: #fff;
            border-radius: 24px;
            padding: 1.1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.8rem;
        }

        footer {
            padding: 1.4rem 0 2.2rem;
            color: #58718b;
            font-size: 0.92rem;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 990px) {
            .topbar { display: none; }

            .hero-grid,
            .contact,
            .cards { grid-template-columns: 1fr; }

            .program-row { grid-template-columns: 120px 1fr; }
            .hero { padding-top: 2.1rem; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="{{ route('vitrine') }}">
                <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo Ancre Des Elites">
                <span>
                    <strong>Ancre Des Elites</strong>
                    <small>Garderie et eveil</small>
                </span>
            </a>
            <div class="top-links">
                <a class="btn btn-soft" href="#contact"><i class="fa-solid fa-phone"></i> Contact</a>
                <a class="btn btn-main" href="{{ route('login') }}"><i class="fa-solid fa-lock"></i> Espace parent</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-grid">
                <article class="hero-copy">
                    <span class="eyebrow">Garderie de confiance</span>
                    <h1>Un cocon d'apprentissage, de joie et de securite pour vos enfants.</h1>
                    <p>
                        A Ancre Des Elites, chaque enfant evolue a son rythme avec un accompagnement bienveillant,
                        des activites creatives et un suivi attentif avec les familles.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-main" href="#services">Decouvrir nos services</a>
                        <a class="btn btn-soft" href="#programme">Voir le programme</a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat"><strong>3-12</strong><small>ans accompagnes</small></div>
                        <div class="hero-stat"><strong>8h-18h</strong><small>accueil quotidien</small></div>
                        <div class="hero-stat"><strong>100%</strong><small>ambiance familiale</small></div>
                    </div>
                </article>

                <aside class="hero-card">
                    <div class="note">
                        <strong>Inscription ouverte 2026/2027</strong>
                        <span>Places limitees selon les groupes d'age.</span>
                    </div>
                </aside>
            </div>
        </section>

        <section id="services" class="section">
            <div class="container">
                <h2>Nos services</h2>
                <p class="muted">Une prise en charge complete pour le developpement et le bien-etre de l'enfant.</p>
                <div class="cards">
                    <article class="card">
                        <span class="icon"><i class="fa-solid fa-book-open-reader"></i></span>
                        <h3>Eveil pedagogique</h3>
                        <p class="muted">Ateliers de langage, logique, arts et decouverte scientifique adaptes par niveau.</p>
                    </article>
                    <article class="card">
                        <span class="icon"><i class="fa-solid fa-apple-whole"></i></span>
                        <h3>Repas et hygiene</h3>
                        <p class="muted">Repas equilibres, routines d'hygiene et environnement propre et securise.</p>
                    </article>
                    <article class="card">
                        <span class="icon"><i class="fa-solid fa-heart-pulse"></i></span>
                        <h3>Suivi personnalise</h3>
                        <p class="muted">Communication reguliere avec les parents et suivi de progression individualise.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="programme" class="section">
            <div class="container">
                <h2>Une journee type</h2>
                <p class="muted">Un rythme rassurant qui favorise l'autonomie et l'eveil.</p>
                <div class="program">
                    <div class="program-row"><strong>08:00 - 09:00</strong><span>Accueil des enfants et activites calmes</span></div>
                    <div class="program-row"><strong>09:00 - 11:30</strong><span>Ateliers pedagogiques et jeux d'equipe</span></div>
                    <div class="program-row"><strong>11:30 - 13:30</strong><span>Repas, hygiene et temps de repos</span></div>
                    <div class="program-row"><strong>13:30 - 16:00</strong><span>Activites artistiques, motricite et sorties encadrees</span></div>
                    <div class="program-row"><strong>16:00 - 18:00</strong><span>Gouter, revision douce et depart progressif</span></div>
                </div>
                <blockquote class="quote">
                    "Notre priorite: que chaque enfant se sente valorise, ecoute et heureux de revenir chaque matin."
                </blockquote>
            </div>
        </section>

        <section id="contact" class="section">
            <div class="container contact">
                <div class="contact-box">
                    <h2>Contactez-nous</h2>
                    <p class="muted">Nous repondons rapidement pour les inscriptions et les visites.</p>
                    <ul class="contact-list">
                        <li><i class="fa-solid fa-location-dot"></i><span>Tunis, Tunisie</span></li>
                        <li><i class="fa-solid fa-phone"></i><span>+216 00 000 000</span></li>
                        <li><i class="fa-solid fa-envelope"></i><span>contact@ancredeselites.tn</span></li>
                        <li><i class="fa-solid fa-clock"></i><span>Lun - Ven: 08:00 a 18:00</span></li>
                    </ul>
                </div>

                <div class="cta">
                    <div>
                        <h3>Visiter l'espace de gestion</h3>
                        <p>Responsable, educateurs et parents peuvent acceder a leur espace securise.</p>
                    </div>
                    <div>
                        <a class="btn btn-main" href="{{ route('login') }}">Connexion</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <span>© {{ date('Y') }} Ancre Des Elites. Tous droits reserves.</span>
        </div>
    </footer>
</body>
</html>
