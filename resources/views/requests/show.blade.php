@extends('adminlte::page')

@section('title', 'Traitement Demande')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Traitement demande/reclamation</h1>
    <a href="{{ route('demandes.index') }}" class="btn btn-secondary">Retour</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <span class="badge bg-{{ $parentRequest->workflowBadgeClass() }}">{{ \App\Models\ParentRequest::STATUS_OPTIONS[$parentRequest->workflow_status] ?? $parentRequest->workflow_status }}</span>
            <span class="badge bg-info">{{ \App\Models\ParentRequest::ACTION_OPTIONS[$parentRequest->action_type] ?? $parentRequest->action_type }}</span>
            @if($parentRequest->handledBy)
                <span class="badge bg-secondary">Pris en charge par {{ $parentRequest->handledBy->name }}</span>
            @endif
        </div>
        <p class="mb-1"><strong>Parent:</strong> {{ $parentRequest->parent?->prenom }} {{ $parentRequest->parent?->nom }}</p>
        <p class="mb-1"><strong>Enfant:</strong> {{ $parentRequest->enfant?->prenom }} {{ $parentRequest->enfant?->nom }}</p>
        <p class="mb-1"><strong>Sujet:</strong> {{ $parentRequest->subjectLabel() }}</p>
        <p class="mb-1"><strong>Description:</strong></p>
        <div class="border rounded p-3 bg-light">{!! nl2br(e($parentRequest->description)) !!}</div>

        @if(!empty($parentRequest->attachments))
            <div class="mt-3">
                <strong>Pieces jointes:</strong>
                <div class="modern-attachment-list mt-2">
                    @foreach($parentRequest->attachments as $attachment)
                        <a href="{{ Storage::disk('public')->url($attachment['path']) }}" target="_blank" rel="noopener" class="modern-attachment-chip">
                            <i class="fa-solid {{ str_starts_with($attachment['mime'] ?? '', 'image/') ? 'fa-image' : 'fa-file-lines' }}"></i>
                            <span>{{ $attachment['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Workflow</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('demandes.workflow.update', $parentRequest) }}" class="row g-2 align-items-end">
            @csrf
            @method('PATCH')
            <div class="col-md-4">
                <label>Statut</label>
                <select name="workflow_status" class="form-control @error('workflow_status') is-invalid @enderror" required>
                    @foreach(\App\Models\ParentRequest::STATUS_OPTIONS as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(old('workflow_status', $parentRequest->workflow_status) === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                @error('workflow_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label>Note de resolution / refus</label>
                <input type="text" name="resolution_note" class="form-control @error('resolution_note') is-invalid @enderror" value="{{ old('resolution_note', $parentRequest->resolution_note) }}" placeholder="Obligatoire pour Traite/Refuse">
                @error('resolution_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Mettre a jour</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h3 class="card-title mb-0">Conversation</h3></div>
    <div class="card-body">
        @forelse($parentRequest->messages as $message)
            @php
                $isMine = (int) $message->sender_user_id === (int) auth()->id();
            @endphp
            <div class="mb-3 p-3 rounded {{ $isMine ? 'bg-primary text-white' : 'bg-light' }}">
                <div class="small mb-1 {{ $isMine ? 'text-white-50' : 'text-muted' }}">
                    {{ $message->sender?->name ?: 'Utilisateur' }} - {{ optional($message->created_at)->format('d/m/Y H:i') }}
                </div>
                <div>{!! nl2br(e($message->message)) !!}</div>
                @if(!empty($message->attachments))
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach($message->attachments as $attachment)
                            <a href="{{ Storage::disk('public')->url($attachment['path']) }}" target="_blank" rel="noopener" class="badge bg-{{ $isMine ? 'light text-dark' : 'secondary' }}">{{ $attachment['name'] }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted">Aucun message dans la conversation.</p>
        @endforelse

        <hr>

        <form method="POST" action="{{ route('demandes.messages.store', $parentRequest) }}" enctype="multipart/form-data" class="row g-2">
            @csrf
            <div class="col-12">
                <label>Reponse a envoyer au parent</label>
                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="3" required>{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label>Pieces jointes</label>
                <input type="file" name="attachments[]" multiple class="form-control @error('attachments.*') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                @error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Envoyer message</button>
            </div>
        </form>
    </div>
</div>
@stop