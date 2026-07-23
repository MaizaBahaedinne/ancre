@extends('adminlte::page')

@section('title', 'Detail Parent')

@section('content_header')
    <h1 class="m-0">Detail parent</h1>
@stop

@section('css')
    <style>
        .parent-profile-shell {
            display: grid;
            gap: 1rem;
        }

        .parent-profile-hero {
            overflow: hidden;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #fff8ee 0%, #ffffff 52%, #eef7ff 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .parent-profile-hero-body {
            padding: 1.75rem;
        }

        .parent-profile-hero-grid {
            display: grid;
            grid-template-columns: 124px minmax(0, 1fr) auto;
            gap: 1.5rem;
            align-items: center;
        }

        .parent-profile-avatar {
            width: 124px;
            height: 124px;
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0b2448, #0c7abf);
            color: #fff;
            font-size: 2.7rem;
            font-weight: 800;
            box-shadow: 0 16px 36px rgba(12, 122, 191, 0.28);
            overflow: hidden;
        }

        .parent-profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .parent-profile-kicker {
            margin: 0 0 0.3rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 800;
        }

        .parent-profile-name {
            margin: 0;
            font-size: clamp(1.75rem, 3vw, 2.35rem);
            font-weight: 800;
            color: #0f172a;
        }

        .parent-profile-meta,
        .parent-profile-tags,
        .parent-profile-actions,
        .parent-profile-doc-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .parent-profile-meta {
            margin-top: 0.75rem;
            color: #475569;
        }

        .parent-profile-meta span,
        .parent-profile-tags span {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .parent-profile-chip {
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.12);
            color: #334155;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .parent-profile-chip.is-safe {
            background: rgba(34, 197, 94, 0.14);
            color: #166534;
        }

        .parent-profile-chip.is-warn {
            background: rgba(245, 158, 11, 0.16);
            color: #92400e;
        }

        .parent-stat-card {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.1rem 1.2rem;
            border-radius: 1.25rem;
            background: #fff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .parent-stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .parent-stat-card small,
        .parent-stat-card span {
            display: block;
        }

        .parent-stat-card small {
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .parent-stat-card strong {
            display: block;
            margin: 0.18rem 0;
            color: #0f172a;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .parent-stat-card span {
            color: #94a3b8;
            font-size: 0.82rem;
        }

        .parent-side-card .card-body,
        .parent-panel {
            padding: 1rem;
        }

        .parent-facts {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.8rem;
        }

        .parent-facts li {
            display: grid;
            grid-template-columns: 1.35rem minmax(0, 1fr);
            gap: 0.7rem;
            align-items: start;
        }

        .parent-facts i {
            color: #0c7abf;
            margin-top: 0.2rem;
        }

        .parent-facts span,
        .parent-detail-grid p small {
            display: block;
            color: #64748b;
            font-size: 0.84rem;
            margin-bottom: 0.16rem;
        }

        .parent-facts strong,
        .parent-detail-grid p strong {
            color: #0f172a;
        }

        .parent-detail-grid p {
            margin-bottom: 1rem;
        }

        .parent-linked-table .table {
            margin-bottom: 0;
        }

        @media (max-width: 991.98px) {
            .parent-profile-hero-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('temporary_password'))
        <div class="alert alert-info">Mot de passe temporaire du compte parent: <strong>{{ session('temporary_password') }}</strong></div>
    @endif

    @php
        $verificationStatus = $verificationCompleted ? 'verified' : ($parent->verification_status ?? 'pending');
        $needsVerification = ! $verificationCompleted;
    @endphp

    @if($needsVerification)
        <div class="modal fade" id="verificationPromptModal" tabindex="-1" aria-labelledby="verificationPromptModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verificationPromptModalLabel">Verification du compte parent</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Scannez le QR code avec le smartphone du parent pour envoyer le recto, le verso et la signature manuscrite. L'etat se met a jour automatiquement sur cet ecran.</p>
                        <div class="text-center mb-3">
                            <div id="parent-verification-qr" class="d-inline-block p-2 bg-white border rounded"></div>
                        </div>
                        <div class="d-grid gap-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Recto smartphone</span>
                                <span class="badge badge-secondary" data-verification-badge="recto">En attente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Verso smartphone</span>
                                <span class="badge badge-secondary" data-verification-badge="verso">En attente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Signature manuscrite</span>
                                <span class="badge badge-secondary" data-verification-badge="signature">En attente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Verification complete</span>
                                <span class="badge badge-secondary" data-verification-badge="verified">En attente</span>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2 h-100 bg-light">
                                    <div class="small fw-semibold mb-2">Apercu recto</div>
                                    <div data-verification-preview="recto" class="small text-muted">Aucun document recu</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 h-100 bg-light">
                                    <div class="small fw-semibold mb-2">Apercu verso</div>
                                    <div data-verification-preview="verso" class="small text-muted">Aucun document recu</div>
                                </div>
                            </div>
                        </div>
                        <div class="small text-muted">Statut actuel: {{ ucfirst($verificationStatus) }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Plus tard</button>
                        <a href="{{ $verificationUrl }}" class="btn btn-primary">Aller a la verification</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="parent-profile-shell">
        <section class="parent-profile-hero card border-0">
            <div class="parent-profile-hero-body">
                <div class="parent-profile-hero-grid">
                    <div class="parent-profile-avatar">
                        @if($parent->photo)
                            <img src="{{ asset('storage/'.$parent->photo) }}" alt="Photo de {{ $parent->prenom }} {{ $parent->nom }}">
                        @else
                            {{ strtoupper(substr($parent->prenom ?: $parent->nom, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <p class="parent-profile-kicker">Profil parent</p>
                        <h2 class="parent-profile-name">{{ $parent->nom }} {{ $parent->prenom }}</h2>
                        <div class="parent-profile-meta">
                            <span><i class="fa-solid fa-phone"></i>{{ $parent->telephone ?: '-' }}</span>
                            <span><i class="fa-solid fa-envelope"></i>{{ $parent->email ?: '-' }}</span>
                            <span><i class="fa-solid fa-id-card"></i>{{ $parent->numero_cin ?: '-' }}</span>
                        </div>
                        <div class="parent-profile-tags mt-3">
                            <span class="parent-profile-chip">{{ $parent->sexe === 'M' ? 'Masculin' : ($parent->sexe === 'F' ? 'Feminin' : 'Sexe non renseigne') }}</span>
                            <span class="parent-profile-chip">{{ $parent->profession ?: 'Profession non renseignee' }}</span>
                            <span class="parent-profile-chip {{ $verificationStatus === 'verified' ? 'is-safe' : 'is-warn' }}">
                                {{ $verificationStatus === 'verified' ? 'Compte verifie' : 'Verification en attente' }}
                            </span>
                            <span class="parent-profile-chip {{ $parent->user ? 'is-safe' : 'is-warn' }}">
                                {{ $parent->user ? 'Compte utilisateur cree' : 'Aucun compte utilisateur' }}
                            </span>
                        </div>
                    </div>
                    <div class="parent-profile-actions">
                        @if($needsVerification)
                            <a href="{{ $verificationUrl }}" class="btn btn-info">Verifier le compte</a>
                        @endif
                        <a href="{{ route('parents.edit', $parent) }}" class="btn btn-warning">Modifier</a>
                        <a href="{{ route('parents.index', $parent) }}" class="btn btn-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="parent-stat-card">
                    <div class="parent-stat-icon bg-primary"><i class="fa-solid fa-children"></i></div>
                    <div>
                        <small>Enfants</small>
                        <strong>{{ $parent->enfants_count }}</strong>
                        <span>rattaches au parent</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="parent-stat-card">
                    <div class="parent-stat-icon bg-success"><i class="fa-solid fa-file-shield"></i></div>
                    <div>
                        <small>Verification</small>
                        <strong>{{ $verificationStatus === 'verified' ? 'OK' : 'En attente' }}</strong>
                        <span>{{ $verificationStatus === 'verified' ? 'profil valide' : 'action requise' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="parent-stat-card">
                    <div class="parent-stat-icon bg-info"><i class="fa-solid fa-user-lock"></i></div>
                    <div>
                        <small>Compte parent</small>
                        <strong>{{ $parent->user ? 'Actif' : 'Non cree' }}</strong>
                        <span>{{ $parent->user?->email ?: 'aucune liaison utilisateur' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="parent-stat-card">
                    <div class="parent-stat-icon bg-warning"><i class="fa-solid fa-phone-volume"></i></div>
                    <div>
                        <small>Urgence</small>
                        <strong>{{ $parent->contact_urgence ?: '-' }}</strong>
                        <span>contact prioritaire</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-4">
                <div class="card parent-side-card h-100">
                    <div class="card-body">
                        <ul class="parent-facts mb-0">
                            <li><i class="fa-solid fa-envelope"></i><div><span>Email</span><strong>{{ $parent->email ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-phone"></i><div><span>Telephone</span><strong>{{ $parent->telephone ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-id-card"></i><div><span>Numero CIN</span><strong>{{ $parent->numero_cin ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-calendar-days"></i><div><span>Date de delivrance</span><strong>{{ optional($parent->date_delivrance_cin)->format('d/m/Y') ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-cake-candles"></i><div><span>Date de naissance</span><strong>{{ optional($parent->date_naissance)->format('d/m/Y') ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-road"></i><div><span>Rue</span><strong>{{ $parent->adresse_rue ?: ($parent->adresse ?: '-') }}</strong></div></li>
                            <li><i class="fa-solid fa-city"></i><div><span>Ville</span><strong>{{ $parent->adresse_ville ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-map-location-dot"></i><div><span>Gouvernorat</span><strong>{{ $parent->adresse_gouvernorat ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-briefcase"></i><div><span>Profession</span><strong>{{ $parent->profession ?: '-' }}</strong></div></li>
                            <li><i class="fa-solid fa-triangle-exclamation"></i><div><span>Contact urgence</span><strong>{{ $parent->contact_urgence ?: '-' }}</strong></div></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body parent-panel">
                        <h4 class="mb-3">Identite et verification</h4>
                        <div class="row parent-detail-grid">
                            <div class="col-md-6">
                                <p><small>Statut profil</small><strong>{{ $verificationStatus === 'verified' ? 'Verifie' : ($verificationStatus === 'submitted' ? 'En attente de validation' : 'A verifier') }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p><small>Compte utilisateur parent</small><strong>{{ $parent->user ? $parent->user->email : 'Aucun compte utilisateur associe' }}</strong></p>
                            </div>
                            <div class="col-md-12">
                                <small class="d-block mb-2 text-muted">Documents CIN</small>
                                <div class="parent-profile-doc-links">
                                    @if($parent->cin_recto)
                                        <a href="{{ asset('storage/' . $parent->cin_recto) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">Voir recto</a>
                                    @else
                                        <span class="parent-profile-chip is-warn">Recto manquant</span>
                                    @endif
                                    @if($parent->cin_verso)
                                        <a href="{{ asset('storage/' . $parent->cin_verso) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">Voir verso</a>
                                    @else
                                        <span class="parent-profile-chip is-warn">Verso manquant</span>
                                    @endif
                                    @if($parent->verification_signature)
                                        <a href="{{ asset('storage/' . $parent->verification_signature) }}" target="_blank" rel="noopener" class="btn btn-outline-success btn-sm">Voir signature</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(! $parent->user)
                    <div class="card mb-4">
                        <div class="card-body parent-panel">
                            <h4 class="mb-3">Compte utilisateur</h4>
                            @can('users.manage')
                                @if($parent->email)
                                    <p class="text-muted">Le compte parent n'est pas encore lie. Vous pouvez le creer manuellement si besoin.</p>
                                    <form method="POST" action="{{ route('parents.create-user', $parent) }}" class="d-inline" onsubmit="return confirm('Creer et associer un compte utilisateur pour ce parent ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Creer un user parent</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning mb-0">Ajoutez un email au parent pour pouvoir generer son compte utilisateur.</div>
                                @endif
                            @else
                                <div class="alert alert-light mb-0">Aucun compte utilisateur associe.</div>
                            @endcan
                        </div>
                    </div>
                @endif

                <div class="card parent-linked-table">
                    <div class="card-header border-0 pt-4 px-4 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <p class="parent-profile-kicker mb-1">Famille rattachee</p>
                            <h3 class="card-title mb-0">Enfants rattaches</h3>
                        </div>
                        <div class="parent-profile-tags">
                            <span class="parent-profile-chip"><i class="fa-solid fa-children"></i>{{ $linkedEnfants->count() }} enfant(s)</span>
                        </div>
                    </div>
                    <div class="card-body pt-3 p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                    <thead>
                    <tr>
                        <th>Lien</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Date naissance</th>
                        <th>Classe</th>
                        <th width="120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($linkedEnfants as $enfant)
                        @php
                            $relationLabels = collect();

                            if ((int) $enfant->parent_id === (int) $parent->id) {
                                $relationLabels->push('Parent principal');
                            }

                            foreach ($enfant->familyRelations as $familyRelation) {
                                if ($familyRelation->relation) {
                                    $relationLabels->push($familyRelation->relation);
                                }
                            }

                            $relationText = $relationLabels->filter()->unique()->join(', ');
                        @endphp
                        <tr>
                            <td>{{ $relationText ?: '-' }}</td>
                            <td>{{ $enfant->nom }}</td>
                            <td>{{ $enfant->prenom }}</td>
                            <td>{{ optional($enfant->date_naissance)->format('d/m/Y') }}</td>
                            <td>{{ $enfant->classe ?: '-' }}</td>
                            <td>
                                <a href="{{ route('enfants.show', $enfant) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun enfant rattache a ce parent.</td>
                        </tr>
                    @endforelse
                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (() => {
            const verificationUrl = @json($verificationUrl);
            const verificationStatusUrl = @json($verificationStatusUrl);
            const qrContainer = document.getElementById('parent-verification-qr');
            const needsVerification = @json($needsVerification);
            const badges = {
                recto: document.querySelector('[data-verification-badge="recto"]'),
                verso: document.querySelector('[data-verification-badge="verso"]'),
                signature: document.querySelector('[data-verification-badge="signature"]'),
                verified: document.querySelector('[data-verification-badge="verified"]'),
            };
            const previews = {
                recto: document.querySelector('[data-verification-preview="recto"]'),
                verso: document.querySelector('[data-verification-preview="verso"]'),
            };

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: verificationUrl,
                    width: 170,
                    height: 170,
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }

            const setBadgeState = (element, ready, doneText = 'Recu') => {
                if (!element) {
                    return;
                }

                element.textContent = ready ? doneText : 'En attente';
                element.className = ready ? 'badge badge-success' : 'badge badge-secondary';
            };

            const renderPreview = (container, payload) => {
                if (!container) {
                    return;
                }

                if (!payload?.ready || !payload?.url) {
                    container.innerHTML = '<span class="small text-muted">Aucun document recu</span>';
                    return;
                }

                if (payload.is_image) {
                    container.innerHTML = `<img src="${payload.url}" alt="Document" class="img-fluid rounded border">`;
                    return;
                }

                container.innerHTML = `<a href="${payload.url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Ouvrir le fichier ${payload.extension ? payload.extension.toUpperCase() : ''}</a>`;
            };

            const loadStatus = async () => {
                if (!verificationStatusUrl) {
                    return;
                }

                try {
                    const response = await fetch(verificationStatusUrl, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();

                    setBadgeState(badges.recto, Boolean(payload.recto?.ready));
                    setBadgeState(badges.verso, Boolean(payload.verso?.ready));
                    setBadgeState(badges.signature, Boolean(payload.signature?.ready), 'Signee');
                    setBadgeState(badges.verified, Boolean(payload.verified), 'Complete');
                    renderPreview(previews.recto, payload.recto);
                    renderPreview(previews.verso, payload.verso);

                    if (payload.verified) {
                        window.setTimeout(() => window.location.reload(), 1200);
                    }
                } catch (error) {
                    console.error(error);
                }
            };

            if (needsVerification && window.jQuery) {
                window.jQuery('#verificationPromptModal').modal('show');
            }

            if (needsVerification) {
                loadStatus();
                window.setInterval(loadStatus, 3000);
            }
        })();
    </script>
@stop
