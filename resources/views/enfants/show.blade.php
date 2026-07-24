@extends('adminlte::page')

@section('title', 'Detail Enfant')

@section('content_header')
    <h1 class="m-0">Detail enfant</h1>
@stop

@section('content')
    @php
        $displayName = trim($enfant->nom.' '.$enfant->prenom);
        $age = $enfant->date_naissance ? $enfant->date_naissance->age.' ans' : null;
        $parentPrincipal = $enfant->parent;
        $selectedAllergies = $enfant->allergie_options ?? [];
        $isCurrentlyInscribed = (bool) $currentYearInscription;
        $currentYearLabel = $activeAcademicYear?->label ?: 'l\'annee en cours';
    @endphp

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->has('quick_inscription'))
        <div class="alert alert-danger">{{ $errors->first('quick_inscription') }}</div>
    @endif

    <section class="child-profile-shell">
        <div class="child-profile-hero card">
            <div class="card-body">
                <div class="child-profile-hero-grid">
                    <div class="child-profile-avatar-wrap">
                        @if($enfant->photo)
                            <img src="{{ asset('storage/' . $enfant->photo) }}" alt="photo" class="child-profile-avatar">
                        @else
                            <div class="child-profile-avatar child-profile-avatar-placeholder">
                                <span>{{ strtoupper(substr($enfant->prenom ?: $enfant->nom, 0, 1)) }}</span>
                            </div>
                        @endif

                        @can('children.update')
                            <form method="POST" action="{{ route('enfants.photo.upload', $enfant) }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <div class="mb-2">
                                    <input type="file" name="photo" class="form-control form-control-sm @error('photo') is-invalid @enderror" accept="image/*" required>
                                    @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">Upload image</button>
                            </form>
                        @endcan
                    </div>

                    <div class="child-profile-main">
                        <p class="child-profile-kicker">Profil enfant</p>
                        <h2 class="child-profile-name">{{ $displayName }}</h2>
                        <div class="child-profile-meta">
                            <span><i class="fa-solid fa-graduation-cap"></i>{{ $enfant->classe ?: 'Classe non definie' }}</span>
                            <span><i class="fa-solid fa-cake-candles"></i>{{ $age ?: 'Age non disponible' }}</span>
                            <span><i class="fa-solid fa-venus-mars"></i>{{ $enfant->sexe }}</span>
                        </div>

                        <div class="child-profile-tags">
                            <span class="child-profile-chip {{ $enfant->has_allergie ? 'is-alert' : 'is-safe' }}">
                                <i class="fa-solid {{ $enfant->has_allergie ? 'fa-triangle-exclamation' : 'fa-shield-heart' }}"></i>
                                {{ $enfant->has_allergie ? 'Alergie signalee' : 'Aucune allergie signalee' }}
                            </span>
                            <span class="child-profile-chip {{ $isCurrentlyInscribed ? 'is-safe' : 'is-alert' }}">
                                <i class="fa-solid {{ $isCurrentlyInscribed ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                {{ $isCurrentlyInscribed ? 'Inscrit en '.$currentYearLabel : 'Non inscrit en '.$currentYearLabel }}
                            </span>
                            @if($parentPrincipal)
                                <span class="child-profile-chip">
                                    <i class="fa-solid fa-user-group"></i>
                                    Parent principal: {{ $parentPrincipal->prenom }} {{ $parentPrincipal->nom }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="child-profile-actions">
                        <a href="{{ route('enfants.edit', $enfant) }}" class="btn btn-warning">Modifier</a>
                        @can('registrations.create')
                            @if(! $isCurrentlyInscribed && $activeAcademicYear)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#quickInscriptionModal">Inscrire annee en cours</button>
                            @endif
                        @endcan
                        <a href="{{ route('enfants.index') }}" class="btn btn-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </div>

        @can('registrations.create')
            @if(! $isCurrentlyInscribed)
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong>Inscription annee en cours</strong>
                            <div class="text-muted small">
                                @if($activeAcademicYear)
                                    {{ $displayName }} n'est pas encore inscrit pour {{ $activeAcademicYear->label }}.
                                @else
                                    Aucune annee scolaire active n'est definie.
                                @endif
                            </div>
                        </div>
                        @if($activeAcademicYear)
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#quickInscriptionModal">Inscrire maintenant</button>
                        @endif
                    </div>
                </div>
            @endif
        @endcan

        <div class="child-profile-stats row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="child-stat-card">
                    <div class="child-stat-icon bg-success"><i class="fas fa-user-check"></i></div>
                    <div>
                        <small>Presences</small>
                        <strong>{{ $presenceMonth }}</strong>
                        <span>Mois en cours</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="child-stat-card">
                    <div class="child-stat-icon bg-danger"><i class="fas fa-user-times"></i></div>
                    <div>
                        <small>Absences</small>
                        <strong>{{ $absenceMonth }}</strong>
                        <span>Mois en cours</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="child-stat-card">
                    <div class="child-stat-icon bg-info"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <small>Paiements</small>
                        <strong>{{ number_format($paiementTotal, 2, ',', ' ') }} TND</strong>
                        <span>{{ $paiementCount }} enregistrements</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="child-stat-card">
                    <div class="child-stat-icon bg-warning"><i class="fas fa-puzzle-piece"></i></div>
                    <div>
                        <small>Activites</small>
                        <strong>{{ $activityParticipationCount }}</strong>
                        <span>{{ $activityAbsenceCount }} absences</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-xl-4">
                <div class="card child-profile-side-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">A propos</h3>
                    </div>
                    <div class="card-body">
                        <ul class="child-profile-facts">
                            <li><i class="fa-solid fa-cake-candles"></i><span>Date de naissance</span><strong>{{ optional($enfant->date_naissance)->format('d/m/Y') ?: '-' }}</strong></li>
                            <li><i class="fa-solid fa-graduation-cap"></i><span>Classe</span><strong>{{ $enfant->classe ?: '-' }}</strong></li>
                            <li><i class="fa-solid fa-user-group"></i><span>Parent principal</span><strong>{{ $parentPrincipal ? $parentPrincipal->prenom.' '.$parentPrincipal->nom : '-' }}</strong></li>
                            <li><i class="fa-solid fa-phone"></i><span>Telephone parent</span><strong>{{ $parentPrincipal?->telephone ?: '-' }}</strong></li>
                        </ul>

                        <div class="child-profile-note">
                            <h4>Observations</h4>
                            <p>{{ $enfant->observations ?: 'Aucune observation renseignee.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Sante et suivi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="child-profile-panel">
                                    <h4>Etat allergique</h4>
                                    <p class="mb-2">{{ $enfant->has_allergie ? 'Allergies connues a surveiller.' : 'Aucune allergie declaree actuellement.' }}</p>
                                    <div class="child-profile-badges">
                                        @forelse($selectedAllergies as $allergie)
                                            <span class="child-profile-badge">{{ $allergie }}</span>
                                        @empty
                                            <span class="text-muted">Aucune allergie selectionnee</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="child-profile-panel">
                                    <h4>Precisions medicales</h4>
                                    <p>{{ $enfant->allergies ?: 'Aucune precision complementaire.' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="child-profile-panel">
                                    <h4>Paiements</h4>
                                    <ul class="child-profile-mini-list">
                                        <li><span>Nombre de paiements</span><strong>{{ $paiementCount }}</strong></li>
                                        <li><span>En retard</span><strong>{{ $paiementRetardCount }}</strong></li>
                                        <li><span>Total regle</span><strong>{{ number_format($paiementTotal, 2, ',', ' ') }} TND</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="child-profile-panel">
                                    <h4>Participation aux activites</h4>
                                    <ul class="child-profile-mini-list">
                                        <li><span>Participations</span><strong>{{ $activityParticipationCount }}</strong></li>
                                        <li><span>Absences</span><strong>{{ $activityAbsenceCount }}</strong></li>
                                        <li><span>Presences mensuelles</span><strong>{{ $presenceMonth }}</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Parents rattaches</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>Relation</th>
                                    <th>Nom</th>
                                    <th>Prenom</th>
                                    <th>Telephone</th>
                                    <th>Email</th>
                                    <th>Contact urgence</th>
                                    <th width="130">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($enfant->familyRelations as $familyRelation)
                                    <tr>
                                        <td>{{ $familyRelation->relation }}</td>
                                        <td>{{ $familyRelation->parent?->nom }}</td>
                                        <td>{{ $familyRelation->parent?->prenom }}</td>
                                        <td>{{ $familyRelation->parent?->telephone }}</td>
                                        <td>{{ $familyRelation->parent?->email ?: '-' }}</td>
                                        <td>{{ $familyRelation->parent?->contact_urgence ?: '-' }}</td>
                                        <td>
                                            @if($familyRelation->parent)
                                                <a href="{{ route('parents.show', $familyRelation->parent) }}" class="btn btn-sm btn-info">Voir</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun parent rattache a cet enfant.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Activites recentes</h3>
                    </div>
                    <div class="card-body child-profile-timeline">
                        @forelse($recentActivityParticipations as $participation)
                            <article class="child-profile-timeline-item">
                                <div class="child-profile-timeline-dot {{ $participation->statut === 'Present' ? 'is-present' : 'is-absent' }}"></div>
                                <div>
                                    <h4>{{ $participation->activite?->titre ?: '-' }}</h4>
                                    <p class="child-profile-timeline-date">{{ optional($participation->activite?->date)->format('d/m/Y') ?: '-' }}</p>
                                    <p class="mb-1">
                                        <span class="badge badge-{{ $participation->statut === 'Present' ? 'success' : 'secondary' }}">{{ $participation->statut }}</span>
                                    </p>
                                    <p class="mb-0 text-muted">{{ $participation->remarque ?: 'Aucune remarque.' }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="text-muted m-0">Aucune participation enregistree.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3 border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>Suivi scolaire et evolution</strong>
                    <div class="text-muted small">La saisie trimestrielle des notes se fait desormais dans la fiche inscription.</div>
                </div>
                @if($currentYearInscription)
                    <a href="{{ route('inscriptions.show', $currentYearInscription) }}" class="btn btn-outline-primary">Ouvrir la fiche inscription</a>
                @else
                    <span class="badge badge-secondary">Inscription en cours introuvable</span>
                @endif
            </div>
        </div>
    </section>

    @can('registrations.create')
        @if(! $isCurrentlyInscribed)
            <div class="modal fade" id="quickInscriptionModal" tabindex="-1" aria-labelledby="quickInscriptionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="quickInscriptionModalLabel">Inscrire {{ $displayName }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('enfants.inscriptions.store', $enfant) }}">
                            @csrf
                            <input type="hidden" name="quick_inscription_modal" value="1">
                            <div class="modal-body">
                                <div class="alert alert-light border">
                                    <strong>Annee scolaire active:</strong> {{ $activeAcademicYear?->label ?: '-' }}
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Package</label>
                                        <select name="package_id" class="form-control @error('package_id') is-invalid @enderror" required>
                                            <option value="">Choisir...</option>
                                            @foreach($availablePackages as $package)
                                                <option value="{{ $package->id }}" @selected((string) old('package_id') === (string) $package->id)>
                                                    {{ $package->nom }} ({{ number_format((float) $package->total_mensuel, 2, ',', ' ') }} TND/mois)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date inscription</label>
                                        <input type="date" name="date_inscription" class="form-control @error('date_inscription') is-invalid @enderror" value="{{ old('date_inscription', now()->format('Y-m-d')) }}" required>
                                        @error('date_inscription') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Type garde</label>
                                        <select name="type_garde" class="form-control @error('type_garde') is-invalid @enderror" required>
                                            @foreach(['Matin', 'Apres-midi', 'Journee complete'] as $type)
                                                <option value="{{ $type }}" @selected(old('type_garde', 'Journee complete') === $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        @error('type_garde') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Statut</label>
                                        <select name="statut" class="form-control @error('statut') is-invalid @enderror" required>
                                            @foreach(['Active', 'Renouvelee', 'Suspendue', 'Annulee'] as $status)
                                                <option value="{{ $status }}" @selected(old('statut', 'Active') === $status)>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                @if($availablePackages->isEmpty())
                                    <div class="alert alert-warning mt-3 mb-0">Aucun package actif disponible. Creez d'abord un package.</div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary" @disabled($availablePackages->isEmpty() || ! $activeAcademicYear)>Creer l'inscription</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mustOpenModal = @json((bool) old('quick_inscription_modal'));
    const modalSelector = '#quickInscriptionModal';
    const modalElement = document.getElementById('quickInscriptionModal');

    if (mustOpenModal && modalElement) {
        if (window.jQuery && typeof window.jQuery(modalSelector).modal === 'function') {
            window.jQuery(modalSelector).modal('show');
        } else if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

});
</script>
@stop
