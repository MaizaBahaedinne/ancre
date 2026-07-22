<div class="row">
<div class="col-md-6 form-group">
<label>Enfant</label>
<select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required data-enhance-select="true">
<option value="">Choisir...</option>
@foreach($enfants as $e)
<option value="{{ $e->id }}" @selected(old('enfant_id', optional($incident)->enfant_id) == $e->id)>{{ $e->nom }} {{ $e->prenom }}</option>
@endforeach
</select>
@error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Date</label>
<input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional(optional($incident)->date)->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required>
@error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>
<div class="form-group">
<label>Type incident</label>
<select name="type_incident" class="form-control @error('type_incident') is-invalid @enderror" required>
<option value="">Choisir le type...</option>
@foreach($incidentTypes as $type)
<option value="{{ $type }}" @selected(old('type_incident', optional($incident)->type_incident) === $type)>{{ $type }}</option>
@endforeach
</select>
@error('type_incident') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
<div class="col-md-6 form-group">
<label>Workflow</label>
<select name="workflow_status" class="form-control @error('workflow_status') is-invalid @enderror" data-enhance-select="true" required>
@foreach($workflowOptions as $workflowValue => $workflowLabel)
<option value="{{ $workflowValue }}" @selected(old('workflow_status', optional($incident)->workflow_status ?? \App\Models\Incident::WORKFLOW_OPEN) === $workflowValue)>{{ $workflowLabel }}</option>
@endforeach
</select>
@error('workflow_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="col-md-6 form-group">
<label>Personnel responsable</label>
<select name="responsable_personnel_id" class="form-control @error('responsable_personnel_id') is-invalid @enderror" data-enhance-select="true">
<option value="">Aucun</option>
@foreach($personnels as $personnel)
<option value="{{ $personnel->id }}" @selected((string) old('responsable_personnel_id', optional($incident)->responsable_personnel_id) === (string) $personnel->id)>{{ $personnel->prenom }} {{ $personnel->nom }} - {{ $personnel->fonction }}</option>
@endforeach
</select>
@error('responsable_personnel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
</div>

<div class="form-group">
<div class="form-check">
<input type="hidden" name="notify_parent" value="0">
<input class="form-check-input" type="checkbox" id="notify_parent" name="notify_parent" value="1" @checked(old('notify_parent', optional($incident)->notify_parent))>
<label class="form-check-label" for="notify_parent">Informer le parent</label>
</div>
<small class="text-muted">Si activé, le ticket de l'incident apparaitra sur l'interface parent de l'enfant.</small>
</div>

<div class="form-group">
<label>Description</label>
<input type="hidden" id="description" name="description" value="{{ old('description', optional($incident)->description) }}">
<div class="modern-rich-editor @error('description') is-invalid @enderror" data-rich-editor data-input="description" data-placeholder="Decrivez l'incident..."></div>
@error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="form-group">
<label>Action realisee</label>
<input type="hidden" id="action_realisee" name="action_realisee" value="{{ old('action_realisee', optional($incident)->action_realisee) }}">
<div class="modern-rich-editor @error('action_realisee') is-invalid @enderror" data-rich-editor data-input="action_realisee" data-placeholder="Precisez l'action realisee..."></div>
@error('action_realisee') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="form-group">
<label>Pieces jointes</label>
<input type="file" name="attachments[]" class="form-control-file @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" multiple>
<small class="text-muted">Ajoutez des photos ou des documents pour documenter l'incident.</small>
@error('attachments') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
@error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

@if(!empty(optional($incident)->attachments))
<div class="modern-attachment-list mt-3">
	@foreach($incident->attachments as $attachment)
		<a href="{{ Storage::disk('public')->url($attachment['path']) }}" target="_blank" rel="noopener" class="modern-attachment-chip">
			<i class="fa-solid {{ str_starts_with($attachment['mime'] ?? '', 'image/') ? 'fa-image' : 'fa-file-lines' }}"></i>
			<span>{{ $attachment['name'] }}</span>
		</a>
	@endforeach
</div>
@endif
</div>
