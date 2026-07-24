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

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title mb-0">Suivi scolaire et evolution</h3>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($trimesterStatuses as $trimesterLabel => $isFilled)
                        <span class="badge badge-{{ $isFilled ? 'success' : 'secondary' }}">
                            {{ $trimesterLabel }}: {{ $isFilled ? 'Renseigne' : 'En attente' }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="card-body">
                @if($errors->has('evaluations'))
                    <div class="alert alert-danger">{{ $errors->first('evaluations') }}</div>
                @endif

                <div class="alert alert-light border">
                    <strong>Niveau actuel:</strong> {{ $currentLevel ?: 'Non defini' }}
                    <br>
                    <strong>Annee scolaire active:</strong> {{ $activeAcademicYear?->label ?: '-' }}
                </div>

                @if($subjectCatalog->isEmpty())
                    <div class="alert alert-warning mb-0">
                        Aucune matiere active n'est configuree pour ce niveau. Configurez d'abord les matieres dans le module Matieres.
                    </div>
                @else
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                            <tr>
                                <th>Trimestre</th>
                                <th>Moyenne generale</th>
                                <th>Rang</th>
                                <th>Date bulletin</th>
                                <th>Etat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\EnfantEvaluation::TRIMESTER_OPTIONS as $trimesterLabel)
                                @php
                                    $trimesterEvaluation = $activeYearEvaluations->get($trimesterLabel);
                                @endphp
                                <tr>
                                    <td>{{ $trimesterLabel }}</td>
                                    <td>{{ $trimesterEvaluation?->general_average !== null ? number_format((float) $trimesterEvaluation->general_average, 2, ',', ' ') : '-' }}</td>
                                    <td>{{ $trimesterEvaluation?->class_rank ?: '-' }}</td>
                                    <td>{{ optional($trimesterEvaluation?->bulletin_received_at)->format('d/m/Y') ?: '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $trimesterEvaluation ? 'success' : 'secondary' }}">
                                            {{ $trimesterEvaluation ? 'Renseigne' : 'En attente' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @can('children.update')
                        @if($activeAcademicYear)
                            <div class="accordion" id="accordionSchoolTracking">
                                @foreach(\App\Models\EnfantEvaluation::TRIMESTER_OPTIONS as $trimesterLabel)
                                    @php
                                        $trimesterEvaluation = $activeYearEvaluations->get($trimesterLabel);
                                        $gradeMap = $trimesterEvaluation
                                            ? $trimesterEvaluation->grades->pluck('grade', 'academic_subject_id')
                                            : collect();
                                        $collapseId = 'trimesterForm'.\Illuminate\Support\Str::slug($trimesterLabel);
                                        $headingId = 'heading'.\Illuminate\Support\Str::slug($trimesterLabel);
                                    @endphp
                                    <div class="card">
                                        <div class="card-header" id="{{ $headingId }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                                    {{ $trimesterLabel }}
                                                    @if($trimesterEvaluation)
                                                        <span class="badge badge-success ml-2">Mise a jour</span>
                                                    @endif
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="{{ $collapseId }}" class="collapse" aria-labelledby="{{ $headingId }}" data-parent="#accordionSchoolTracking">
                                            <div class="card-body">
                                                <form method="POST" action="{{ route('enfants.evaluations.upsert', $enfant) }}">
                                                    @csrf
                                                    <input type="hidden" name="academic_year_id" value="{{ $activeAcademicYear->id }}">
                                                    <input type="hidden" name="trimester" value="{{ $trimesterLabel }}">

                                                    <div class="row g-3">
                                                        <div class="col-md-4 form-group">
                                                            <label>Moyenne generale</label>
                                                            <input type="number" step="0.01" min="0" max="20" name="general_average" class="form-control @error('general_average') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('general_average') : $trimesterEvaluation?->general_average }}" placeholder="Auto si vide">
                                                            @error('general_average') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Rang dans la classe</label>
                                                            <input type="number" min="1" max="200" name="class_rank" class="form-control @error('class_rank') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('class_rank') : $trimesterEvaluation?->class_rank }}">
                                                            @error('class_rank') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Date reception bulletin</label>
                                                            <input type="date" name="bulletin_received_at" class="form-control @error('bulletin_received_at') is-invalid @enderror" value="{{ old('trimester') === $trimesterLabel ? old('bulletin_received_at') : optional($trimesterEvaluation?->bulletin_received_at)->format('Y-m-d') }}">
                                                            @error('bulletin_received_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive mt-2">
                                                        <table class="table table-striped table-bordered table-sm mb-0">
                                                            <thead>
                                                            <tr>
                                                                <th>Matiere</th>
                                                                <th>Coefficient</th>
                                                                <th>Note /20</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($subjectCatalog as $subject)
                                                                @php
                                                                    $gradeField = 'grades.'.$subject->id;
                                                                    $gradeValue = old('trimester') === $trimesterLabel
                                                                        ? old($gradeField)
                                                                        : $gradeMap->get($subject->id);
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $subject->name }}</td>
                                                                    <td>{{ number_format((float) $subject->default_coefficient, 2, ',', ' ') }}</td>
                                                                    <td>
                                                                        <input type="number" step="0.01" min="0" max="20" name="grades[{{ $subject->id }}]" class="form-control form-control-sm @error($gradeField) is-invalid @enderror" value="{{ $gradeValue }}">
                                                                        @error($gradeField) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="mt-3 form-group">
                                                        <label>Commentaire</label>
                                                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Observations du bulletin">{{ old('trimester') === $trimesterLabel ? old('notes') : $trimesterEvaluation?->notes }}</textarea>
                                                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Enregistrer {{ $trimesterLabel }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">Aucune annee scolaire active n'est definie.</div>
                        @endif
                    @endcan
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

    if (!mustOpenModal || !modalElement) {
        return;
    }

    if (window.jQuery && typeof window.jQuery(modalSelector).modal === 'function') {
        window.jQuery(modalSelector).modal('show');
        return;
    }

    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
});
</script>
@stop
