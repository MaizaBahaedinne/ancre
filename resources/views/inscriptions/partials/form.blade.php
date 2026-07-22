<div class="row">
    <div class="col-md-6 form-group">
        <label>Enfant</label>
        <select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required>
            <option value="">Choisir...</option>
            @foreach($enfants as $e)
                <option value="{{ $e->id }}" @selected(old('enfant_id', optional($inscription)->enfant_id) == $e->id)>{{ $e->nom }} {{ $e->prenom }}</option>
            @endforeach
        </select>
        @error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Package</label>
        <select name="package_id" class="form-control @error('package_id') is-invalid @enderror" required data-package-select>
            <option value="">Choisir...</option>
            @foreach($packages as $packageOption)
                <option
                    value="{{ $packageOption->id }}"
                    data-total="{{ $packageOption->total_mensuel }}"
                    data-scolarite="{{ $packageOption->frais_scolarite }}"
                    data-dejeuner="{{ $packageOption->frais_dejeuner }}"
                    data-activite="{{ $packageOption->frais_activite }}"
                    @selected((string) old('package_id', optional($inscription)->package_id) === (string) $packageOption->id)
                >
                    {{ $packageOption->nom }}{{ $packageOption->is_active ? '' : ' (Inactif)' }}
                </option>
            @endforeach
        </select>
        @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if($packages->isEmpty())
            <small class="text-danger d-block mt-2">Aucun package actif disponible. Creez d'abord un package.</small>
        @endif
    </div>
    <div class="col-md-6 form-group">
        <label>Annee scolaire en cours</label>
        <input type="hidden" name="annee_scolaire" value="{{ old('annee_scolaire', optional($inscription)->annee_scolaire ?? $activeAcademicYear?->label) }}">
        <input type="text" class="form-control @error('annee_scolaire') is-invalid @enderror" value="{{ old('annee_scolaire', optional($inscription)->annee_scolaire ?? $activeAcademicYear?->label) }}" readonly>
        @error('annee_scolaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="form-text text-muted">
            {{ $activeAcademicYear ? 'L\'annee active est appliquee automatiquement.' : 'Aucune annee scolaire active definie.' }}
        </small>
    </div>
</div>

@php
    $selectedPackageId = (string) old('package_id', optional($inscription)->package_id);
    $selectedPackage = $packages->firstWhere('id', (int) $selectedPackageId);
    $packageTotal = $selectedPackage ? (float) $selectedPackage->total_mensuel : (float) (optional($inscription)->resolved_package_monthly_total ?? 0);
    $currentAnnualRegistrationFee = (float) old('annual_registration_fee', $annualRegistrationFee ?? optional($inscription)->resolved_annual_registration_fee ?? 0);
@endphp

<div class="alert alert-light border" data-package-summary>
    <div><strong>Total mensuel du package:</strong> <span data-package-total>{{ $selectedPackage ? number_format((float) $selectedPackage->total_mensuel, 2, ',', ' ') . ' TND' : '0,00 TND' }}</span></div>
    <div><strong>Frais d'inscription annuelle:</strong> <span data-annual-fee>{{ number_format($currentAnnualRegistrationFee, 2, ',', ' ') }} TND</span></div>
    <div><strong>Total premiere inscription:</strong> <span data-inscription-total>{{ number_format($packageTotal + $currentAnnualRegistrationFee, 2, ',', ' ') }} TND</span></div>
    <small class="text-muted" data-package-breakdown>
        Scolarite: {{ $selectedPackage ? number_format((float) $selectedPackage->frais_scolarite, 2, ',', ' ') : '0,00' }} TND |
        Dejeuner: {{ $selectedPackage ? number_format((float) $selectedPackage->frais_dejeuner, 2, ',', ' ') : '0,00' }} TND |
        Activite: {{ $selectedPackage ? number_format((float) $selectedPackage->frais_activite, 2, ',', ' ') : '0,00' }} TND
    </small>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Date inscription</label>
        <input type="date" name="date_inscription" class="form-control @error('date_inscription') is-invalid @enderror" value="{{ old('date_inscription', optional(optional($inscription)->date_inscription)->format('Y-m-d')) }}" required>
        @error('date_inscription') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Type garde</label>
        <select name="type_garde" class="form-control @error('type_garde') is-invalid @enderror" required>
            @foreach(['Matin', 'Apres-midi', 'Journee complete'] as $type)
                <option value="{{ $type }}" @selected(old('type_garde', optional($inscription)->type_garde) === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type_garde') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <label>Statut</label>
        <select name="statut" class="form-control @error('statut') is-invalid @enderror" required>
            @foreach(['Active', 'Renouvelee', 'Suspendue', 'Annulee'] as $status)
                <option value="{{ $status }}" @selected(old('statut', optional($inscription)->statut ?? 'Active') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('[data-package-select]');
    const totalTarget = document.querySelector('[data-package-total]');
    const annualFeeTarget = document.querySelector('[data-annual-fee]');
    const inscriptionTotalTarget = document.querySelector('[data-inscription-total]');
    const breakdownTarget = document.querySelector('[data-package-breakdown]');
    const annualFee = Number(@json((float) ($annualRegistrationFee ?? optional($inscription)->resolved_annual_registration_fee ?? 0)));

    if (!select || !totalTarget || !annualFeeTarget || !inscriptionTotalTarget || !breakdownTarget) {
        return;
    }

    const formatAmount = (value) => new Intl.NumberFormat('fr-TN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value || 0));

    const refreshSummary = () => {
        const option = select.options[select.selectedIndex];
        const total = option?.dataset.total ?? 0;
        const scolarite = option?.dataset.scolarite ?? 0;
        const dejeuner = option?.dataset.dejeuner ?? 0;
        const activite = option?.dataset.activite ?? 0;

        totalTarget.textContent = `${formatAmount(total)} TND`;
        annualFeeTarget.textContent = `${formatAmount(annualFee)} TND`;
        inscriptionTotalTarget.textContent = `${formatAmount(Number(total) + annualFee)} TND`;
        breakdownTarget.textContent = `Scolarite: ${formatAmount(scolarite)} TND | Dejeuner: ${formatAmount(dejeuner)} TND | Activite: ${formatAmount(activite)} TND`;
    };

    select.addEventListener('change', refreshSummary);
    refreshSummary();
});
</script>
@endpush
