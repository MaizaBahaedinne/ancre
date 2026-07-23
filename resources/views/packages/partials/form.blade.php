<div class="form-group">
    <label>Nom du package</label>
    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', optional($package)->nom) }}" required>
    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

@php
    $defaultIncludeScolarite = optional($package)->exists ? ((float) optional($package)->frais_scolarite > 0) : true;
    $defaultIncludeDejeuner = optional($package)->exists ? ((float) optional($package)->frais_dejeuner > 0) : false;
    $defaultIncludeActivite = optional($package)->exists ? ((float) optional($package)->frais_activite > 0) : false;

    $includeScolarite = (string) old('include_scolarite', $defaultIncludeScolarite ? '1' : '0') === '1';
    $includeDejeuner = (string) old('include_dejeuner', $defaultIncludeDejeuner ? '1' : '0') === '1';
    $includeActivite = (string) old('include_activite', $defaultIncludeActivite ? '1' : '0') === '1';

    $fraisScolarite = (float) old('frais_scolarite', optional($package)->frais_scolarite ?? 0);
    $fraisDejeuner = (float) old('frais_dejeuner', optional($package)->frais_dejeuner ?? 0);
    $fraisActivite = (float) old('frais_activite', optional($package)->frais_activite ?? 0);

    $totalMensuel =
        ($includeScolarite ? $fraisScolarite : 0) +
        ($includeDejeuner ? $fraisDejeuner : 0) +
        ($includeActivite ? $fraisActivite : 0);
@endphp

<div class="row">
    <div class="col-md-4 form-group">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="mb-0">Scolarite incluse</label>
            <div class="custom-control custom-switch">
                <input type="hidden" name="include_scolarite" value="0">
                <input
                    type="checkbox"
                    id="include_scolarite"
                    name="include_scolarite"
                    value="1"
                    class="custom-control-input js-service-toggle"
                    data-target="frais_scolarite"
                    @checked($includeScolarite)
                >
                <label class="custom-control-label" for="include_scolarite">Activer</label>
            </div>
        </div>
        <label for="frais_scolarite">Frais mensuels scolarite</label>
        <input
            type="number"
            id="frais_scolarite"
            step="0.01"
            min="0"
            name="frais_scolarite"
            class="form-control js-service-fee @error('frais_scolarite') is-invalid @enderror"
            value="{{ old('frais_scolarite', optional($package)->frais_scolarite ?? 0) }}"
            @disabled(! $includeScolarite)
        >
        @error('frais_scolarite') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="mb-0">Dejeuner inclus</label>
            <div class="custom-control custom-switch">
                <input type="hidden" name="include_dejeuner" value="0">
                <input
                    type="checkbox"
                    id="include_dejeuner"
                    name="include_dejeuner"
                    value="1"
                    class="custom-control-input js-service-toggle"
                    data-target="frais_dejeuner"
                    @checked($includeDejeuner)
                >
                <label class="custom-control-label" for="include_dejeuner">Activer</label>
            </div>
        </div>
        <label for="frais_dejeuner">Frais mensuels dejeuner</label>
        <input
            type="number"
            id="frais_dejeuner"
            step="0.01"
            min="0"
            name="frais_dejeuner"
            class="form-control js-service-fee @error('frais_dejeuner') is-invalid @enderror"
            value="{{ old('frais_dejeuner', optional($package)->frais_dejeuner ?? 0) }}"
            @disabled(! $includeDejeuner)
        >
        @error('frais_dejeuner') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 form-group">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="mb-0">Activite incluse</label>
            <div class="custom-control custom-switch">
                <input type="hidden" name="include_activite" value="0">
                <input
                    type="checkbox"
                    id="include_activite"
                    name="include_activite"
                    value="1"
                    class="custom-control-input js-service-toggle"
                    data-target="frais_activite"
                    @checked($includeActivite)
                >
                <label class="custom-control-label" for="include_activite">Activer</label>
            </div>
        </div>
        <label for="frais_activite">Frais mensuels activite</label>
        <input
            type="number"
            id="frais_activite"
            step="0.01"
            min="0"
            name="frais_activite"
            class="form-control js-service-fee @error('frais_activite') is-invalid @enderror"
            value="{{ old('frais_activite', optional($package)->frais_activite ?? 0) }}"
            @disabled(! $includeActivite)
        >
        @error('frais_activite') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>Statut</label>
    <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" @selected((string) old('is_active', optional($package)->is_active ?? 1) === '1')>Actif</option>
        <option value="0" @selected((string) old('is_active', optional($package)->is_active) === '0')>Inactif</option>
    </select>
    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="alert alert-light border mb-0">
    <strong>Total mensuel:</strong>
    <span id="package-total-mensuel">{{ number_format($totalMensuel, 2, ',', ' ') }} TND</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggles = document.querySelectorAll('.js-service-toggle');
        const feeInputs = document.querySelectorAll('.js-service-fee');
        const totalTarget = document.getElementById('package-total-mensuel');

        function formatTnd(amount) {
            return Number(amount).toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }) + ' TND';
        }

        function calculateTotal() {
            let total = 0;

            toggles.forEach(function (toggle) {
                const targetId = toggle.dataset.target;
                const input = document.getElementById(targetId);

                if (!input || !toggle.checked) {
                    return;
                }

                const value = parseFloat(input.value);

                if (!Number.isNaN(value)) {
                    total += value;
                }
            });

            if (totalTarget) {
                totalTarget.textContent = formatTnd(total);
            }
        }

        function syncInputState(toggle) {
            const targetId = toggle.dataset.target;
            const input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            input.disabled = !toggle.checked;

            if (!toggle.checked) {
                input.value = '0';
            }
        }

        toggles.forEach(function (toggle) {
            syncInputState(toggle);

            toggle.addEventListener('change', function () {
                syncInputState(toggle);
                calculateTotal();
            });
        });

        feeInputs.forEach(function (input) {
            input.addEventListener('input', calculateTotal);
        });

        calculateTotal();
    });
</script>