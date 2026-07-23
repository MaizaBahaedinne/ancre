@extends('adminlte::page')

@section('title', 'Detail Parent')

@section('content_header')
    <h1 class="m-0">Detail parent</h1>
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

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nom complet</dt>
                <dd class="col-sm-9">{{ $parent->nom }} {{ $parent->prenom }}</dd>

                <dt class="col-sm-3">N CIN</dt>
                <dd class="col-sm-9">{{ $parent->numero_cin ?: '-' }}</dd>

                <dt class="col-sm-3">Date de delivrance</dt>
                <dd class="col-sm-9">{{ optional($parent->date_delivrance_cin)->format('d/m/Y') ?: '-' }}</dd>

                <dt class="col-sm-3">Date de naissance</dt>
                <dd class="col-sm-9">{{ optional($parent->date_naissance)->format('d/m/Y') ?: '-' }}</dd>

                <dt class="col-sm-3">Sexe</dt>
                <dd class="col-sm-9">{{ $parent->sexe ?: '-' }}</dd>

                <dt class="col-sm-3">Telephone</dt>
                <dd class="col-sm-9">{{ $parent->telephone }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $parent->email ?: '-' }}</dd>

                <dt class="col-sm-3">Adresse</dt>
                <dd class="col-sm-9">{{ $parent->adresse ?: '-' }}</dd>

                <dt class="col-sm-3">Profession</dt>
                <dd class="col-sm-9">{{ $parent->profession ?: '-' }}</dd>

                <dt class="col-sm-3">Contact urgence</dt>
                <dd class="col-sm-9">{{ $parent->contact_urgence ?: '-' }}</dd>

                <dt class="col-sm-3">Nombre d'enfants</dt>
                <dd class="col-sm-9">{{ $parent->enfants_count }}</dd>

                <dt class="col-sm-3">Documents CIN</dt>
                <dd class="col-sm-9">
                    @if($parent->cin_recto)
                        <a href="{{ asset('storage/' . $parent->cin_recto) }}" target="_blank" rel="noopener">Recto</a>
                    @else
                        <span class="text-danger">Recto manquant</span>
                    @endif
                    |
                    @if($parent->cin_verso)
                        <a href="{{ asset('storage/' . $parent->cin_verso) }}" target="_blank" rel="noopener">Verso</a>
                    @else
                        <span class="text-danger">Verso manquant</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Statut profil</dt>
                <dd class="col-sm-9">
                    @if($verificationStatus === 'verified')
                        <span class="badge badge-success">Verifie</span>
                    @elseif($verificationStatus === 'submitted')
                        <span class="badge badge-warning">En attente de validation</span>
                    @else
                        <span class="badge badge-secondary">A verifier</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Compte utilisateur parent</dt>
                <dd class="col-sm-9">
                    @if($parent->user)
                        <div>{{ $parent->user->email }}</div>
                        <small class="text-muted">Roles: {{ $parent->user->getRoleNames()->join(', ') ?: '-' }}</small>
                    @else
                        <span class="text-warning">Aucun compte utilisateur associe</span>

                        @can('users.manage')
                            <div class="mt-2">
                                @if($parent->email)
                                    <form method="POST" action="{{ route('parents.create-user', $parent) }}" class="d-inline" onsubmit="return confirm('Creer et associer un compte utilisateur pour ce parent ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Creer un user parent</button>
                                    </form>
                                @else
                                    <small class="text-danger d-block">Ajoutez un email au parent pour pouvoir generer son compte utilisateur.</small>
                                @endif
                            </div>
                        @endcan
                    @endif
                </dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ $verificationUrl }}" class="btn btn-info">Verifier le compte</a>
            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-warning">Modifier</a>
            <a href="{{ route('parents.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Enfants rattaches</h3>
        </div>
        <div class="card-body p-0">
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
