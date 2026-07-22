@php
    $subject = $subject ?? null;
@endphp

<div class="row">
    <div class="col-md-4 form-group">
        <label>Type d'action</label>
        <select name="action_type" class="form-control @error('action_type') is-invalid @enderror" required>
            @foreach(\App\Models\ParentRequest::ACTION_OPTIONS as $actionValue => $actionLabel)
                <option value="{{ $actionValue }}" @selected(old('action_type', optional($subject)->action_type) === $actionValue)>{{ $actionLabel }}</option>
            @endforeach
        </select>
        @error('action_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Sujet</label>
        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', optional($subject)->label) }}" required>
        @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-2 form-group">
        <label>Ordre</label>
        <input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', optional($subject)->sort_order ?? 0) }}">
        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>Statut</label>
    <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
        <option value="1" @selected((string) old('is_active', optional($subject)->is_active ?? 1) === '1')>Actif</option>
        <option value="0" @selected((string) old('is_active', optional($subject)->is_active) === '0')>Inactif</option>
    </select>
    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>