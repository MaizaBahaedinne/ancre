@php
    $typeOptions = $periodTypeOptions ?? [];
    $periodRows = collect(old('periods', optional($academicYear)->periods?->map(fn ($period) => [
        'title' => $period->title,
        'type' => $period->type,
        'start_date' => optional($period->start_date)->format('Y-m-d'),
        'end_date' => optional($period->end_date)->format('Y-m-d'),
        'notes' => $period->notes,
    ])->values()->all() ?? []));

    $periodRowsByType = [];
    foreach (array_keys($typeOptions) as $typeValue) {
        $rowsForType = $periodRows->filter(fn ($periodRow) => ($periodRow['type'] ?? null) === $typeValue)->values()->all();
        $periodRowsByType[$typeValue] = ! empty($rowsForType) ? $rowsForType : [[]];
    }
@endphp
<div class="row">
<div class="col-md-4 form-group"><label>Libelle</label><input type="text" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', optional($academicYear)->label) }}" required>@error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-3 form-group"><label>Date debut</label><input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', optional(optional($academicYear)->start_date)->format('Y-m-d')) }}" required>@error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-3 form-group"><label>Date fin</label><input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', optional(optional($academicYear)->end_date)->format('Y-m-d')) }}" required>@error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
<div class="col-md-2 form-group d-flex align-items-end"><div class="form-check mb-3"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', optional($academicYear)->is_active))><label class="form-check-label" for="is_active">Active</label></div></div>
</div>

<div class="row">
<div class="col-md-4 form-group"><label>Frais d'inscription annuelle</label><input type="number" step="0.01" min="0" name="registration_fee" class="form-control @error('registration_fee') is-invalid @enderror" value="{{ old('registration_fee', optional($academicYear)->registration_fee ?? 0) }}" required>@error('registration_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
</div>

<div class="card card-outline card-info">
<div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h3 class="card-title mb-0">Periodes du calendrier scolaire</h3>
    <small class="text-muted">Chaque type est regroupé dans un onglet pour alléger la lecture.</small>
</div>
<div class="card-body">
    <ul class="nav nav-tabs mb-3" role="tablist" data-period-tabs>
        @foreach($typeOptions as $typeValue => $typeLabel)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($loop->first) active @endif" id="period-tab-{{ $typeValue }}" data-tab-target="period-pane-{{ $typeValue }}" type="button" role="tab" aria-controls="period-pane-{{ $typeValue }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $typeLabel }}
                    <span class="badge text-bg-light ms-1">{{ count($periodRowsByType[$typeValue] ?? []) }}</span>
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($typeOptions as $typeValue => $typeLabel)
            @php
                $rowsForType = $periodRowsByType[$typeValue] ?? [[]];
            @endphp
            <div class="tab-pane @if($loop->first) show active @else d-none @endif" id="period-pane-{{ $typeValue }}" role="tabpanel" aria-labelledby="period-tab-{{ $typeValue }}">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <strong>{{ $typeLabel }}</strong>
                        <div class="text-muted small">Renseignez une ou plusieurs périodes de ce type.</div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-add-period-row="{{ $typeValue }}">
                        Ajouter une periode
                    </button>
                </div>

                <div class="period-rows" data-period-rows="{{ $typeValue }}" data-next-index="{{ count($rowsForType) }}">
                    @foreach($rowsForType as $index => $periodRow)
                        <div class="row g-3 mb-3 border rounded-3 p-3 bg-light" data-period-row>
                            <input type="hidden" name="periods[{{ $typeValue }}][{{ $index }}][type]" value="{{ $typeValue }}">
                            <div class="col-md-4">
                                <label>Intitule</label>
                                <input type="text" name="periods[{{ $typeValue }}][{{ $index }}][title]" class="form-control" value="{{ $periodRow['title'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label>Debut</label>
                                <input type="date" name="periods[{{ $typeValue }}][{{ $index }}][start_date]" class="form-control" value="{{ $periodRow['start_date'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label>Fin</label>
                                <input type="date" name="periods[{{ $typeValue }}][{{ $index }}][end_date]" class="form-control" value="{{ $periodRow['end_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>Notes</label>
                                <input type="text" name="periods[{{ $typeValue }}][{{ $index }}][notes]" class="form-control" value="{{ $periodRow['notes'] ?? '' }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger w-100" data-remove-period-row>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>

<template id="period-row-template">
    <div class="row g-3 mb-3 border rounded-3 p-3 bg-light" data-period-row>
        <input type="hidden" data-field="type">
        <div class="col-md-4">
            <label>Intitule</label>
            <input type="text" class="form-control" data-field="title">
        </div>
        <div class="col-md-2">
            <label>Debut</label>
            <input type="date" class="form-control" data-field="start_date">
        </div>
        <div class="col-md-2">
            <label>Fin</label>
            <input type="date" class="form-control" data-field="end_date">
        </div>
        <div class="col-md-3">
            <label>Notes</label>
            <input type="text" class="form-control" data-field="notes">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger w-100" data-remove-period-row>
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    (() => {
        const tabContainer = document.querySelector('[data-period-tabs]');

        if (tabContainer) {
            const tabButtons = Array.from(tabContainer.querySelectorAll('[data-tab-target]'));
            const tabPanes = tabButtons
                .map((button) => document.getElementById(button.dataset.tabTarget))
                .filter(Boolean);

            const activateTab = (targetId) => {
                tabButtons.forEach((button) => {
                    const isActive = button.dataset.tabTarget === targetId;
                    button.classList.toggle('active', isActive);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                tabPanes.forEach((pane) => {
                    const isActive = pane.id === targetId;
                    pane.classList.toggle('active', isActive);
                    pane.classList.toggle('show', isActive);
                    pane.classList.toggle('d-none', !isActive);
                });
            };

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
            });

            const initialTabButton = tabButtons.find((button) => button.classList.contains('active')) || tabButtons[0];
            if (initialTabButton) {
                activateTab(initialTabButton.dataset.tabTarget);
            }
        }

        const template = document.getElementById('period-row-template');

        if (!template) {
            return;
        }

        const buildName = (type, index, field) => `periods[${type}][${index}][${field}]`;

        document.querySelectorAll('[data-period-rows]').forEach((container) => {
            const type = container.dataset.periodRows;
            const addButton = document.querySelector(`[data-add-period-row="${type}"]`);

            if (!addButton) {
                return;
            }

            const addRow = () => {
                const index = parseInt(container.dataset.nextIndex || '0', 10);
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('[data-period-row]');

                row.querySelectorAll('[data-field]').forEach((field) => {
                    const fieldName = field.dataset.field;
                    field.name = buildName(type, index, fieldName);

                    if (fieldName === 'type') {
                        field.value = type;
                    }
                });

                container.appendChild(fragment);
                container.dataset.nextIndex = String(index + 1);
            };

            const syncRemoveButtons = () => {
                container.querySelectorAll('[data-remove-period-row]').forEach((button) => {
                    button.onclick = () => {
                        const row = button.closest('[data-period-row]');
                        if (row) {
                            row.remove();
                        }
                    };
                });
            };

            addButton.addEventListener('click', () => {
                addRow();
                syncRemoveButtons();
            });

            syncRemoveButtons();
        });
    })();
</script>