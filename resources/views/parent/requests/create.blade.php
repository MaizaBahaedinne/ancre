@extends('adminlte::page')

@section('title', 'Nouvelle Demande')

@section('content_header')
<h1 class="m-0">Nouvelle demande/reclamation</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('parent.demandes.store') }}" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label>Type d'action</label>
                <select name="action_type" class="form-control @error('action_type') is-invalid @enderror" required data-action-type>
                    <option value="">Choisir...</option>
                    @foreach(\App\Models\ParentRequest::ACTION_OPTIONS as $actionValue => $actionLabel)
                        <option value="{{ $actionValue }}" @selected(old('action_type') === $actionValue)>{{ $actionLabel }}</option>
                    @endforeach
                </select>
                @error('action_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label>Enfant concerne</label>
                <select name="enfant_id" class="form-control @error('enfant_id') is-invalid @enderror" required>
                    <option value="">Choisir...</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" @selected((string) old('enfant_id') === (string) $child->id)>{{ $child->prenom }} {{ $child->nom }}</option>
                    @endforeach
                </select>
                @error('enfant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label>Sujet</label>
                <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" data-subject-select data-native-select="true">
                    <option value="">Autre sujet...</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" data-action-type="{{ $subject->action_type }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->label }}</option>
                    @endforeach
                </select>
                @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12" data-subject-other-wrap>
                <label>Sujet libre (si non liste)</label>
                <input type="text" name="subject_other" class="form-control @error('subject_other') is-invalid @enderror" value="{{ old('subject_other') }}" placeholder="Ex: Demande de report de paiement">
                @error('subject_other') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label>Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6" required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label>Pieces jointes</label>
                <input type="file" name="attachments[]" multiple class="form-control @error('attachments.*') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                @error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Envoyer</button>
                <a href="{{ route('parent.demandes.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
(() => {
    const actionSelect = document.querySelector('[data-action-type]');
    const subjectSelect = document.querySelector('[data-subject-select]');
    const subjectOtherWrap = document.querySelector('[data-subject-other-wrap]');

    if (!actionSelect || !subjectSelect) {
        return;
    }

    const filterSubjects = () => {
        const actionType = actionSelect.value;

        Array.from(subjectSelect.options).forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                return;
            }

            option.hidden = actionType && option.dataset.actionType !== actionType;

            if (option.hidden && option.selected) {
                subjectSelect.value = '';
            }
        });

        if (subjectOtherWrap) {
            subjectOtherWrap.style.display = subjectSelect.value ? 'none' : '';
        }
    };

    actionSelect.addEventListener('change', filterSubjects);
    subjectSelect.addEventListener('change', filterSubjects);
    filterSubjects();
})();
</script>
@stop