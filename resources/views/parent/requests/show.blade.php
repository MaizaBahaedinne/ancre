@extends('adminlte::page')

@section('title', 'Suivi Demande')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Suivi demande/reclamation</h1>
    <a href="{{ route('parent.demandes.index') }}" class="btn btn-secondary">Retour</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <span class="badge bg-{{ $communicationRequest->workflowBadgeClass() }}">{{ \App\Models\ParentRequest::STATUS_OPTIONS[$communicationRequest->workflow_status] ?? $communicationRequest->workflow_status }}</span>
            <span class="badge bg-info">{{ \App\Models\ParentRequest::ACTION_OPTIONS[$communicationRequest->action_type] ?? $communicationRequest->action_type }}</span>
        </div>
        <p class="mb-1"><strong>Enfant:</strong> {{ $communicationRequest->enfant?->prenom }} {{ $communicationRequest->enfant?->nom }}</p>
        <p class="mb-1"><strong>Sujet:</strong> {{ $communicationRequest->subjectLabel() }}</p>
        <p class="mb-1"><strong>Description:</strong></p>
        <div class="border rounded p-3 bg-light">{!! nl2br(e($communicationRequest->description)) !!}</div>
        @if(!empty($communicationRequest->attachments))
            <div class="mt-3">
                <strong>Pieces jointes:</strong>
                <div class="modern-attachment-list mt-2">
                    @foreach($communicationRequest->attachments as $attachment)
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
    <div class="card-header"><h3 class="card-title mb-0">Conversation avec l'administration</h3></div>
    <div class="card-body">
        @forelse($communicationRequest->messages as $message)
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
            <p class="text-muted">Aucun message pour le moment.</p>
        @endforelse

        <hr>

        <form method="POST" action="{{ route('parent.demandes.messages.store', $communicationRequest) }}" enctype="multipart/form-data" class="row g-2">
            @csrf
            <div class="col-12">
                <label>Votre message</label>
                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="3" required>{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label>Pieces jointes</label>
                <input type="file" name="attachments[]" multiple class="form-control @error('attachments.*') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx">
                @error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Envoyer</button>
            </div>
        </form>
    </div>
</div>
@stop