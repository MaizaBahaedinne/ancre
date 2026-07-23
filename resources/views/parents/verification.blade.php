@extends('adminlte::page')

@section('title', 'Verification parent')

@section('content_header')
    <h1 class="m-0">Verification du compte parent</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <div id="verification-qr-code" class="d-inline-block p-2 bg-white border rounded mb-3"></div>
                    <div class="small text-muted">Scannez ce QR code pour ouvrir cette page sur mobile.</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Parent</h5>
                    <div><strong>Nom:</strong> {{ $parent->nom }} {{ $parent->prenom }}</div>
                    <div><strong>CIN:</strong> {{ $parent->numero_cin ?: '-' }}</div>
                    <div><strong>Email:</strong> {{ $parent->email ?: '-' }}</div>
                    <div><strong>Statut:</strong> {{ ucfirst($parent->verification_status ?? 'pending') }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('parents.verification.store', $parent) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info">
                            Chargez le recto et le verso de la piece d'identite, ajoutez la signature du parent et acceptez les reglements avant de valider.
                        </div>

                        <div class="form-group">
                            <label>Recto de la piece</label>
                            <input type="file" name="identity_documents[]" class="form-control @error('identity_documents') is-invalid @enderror" accept="image/*,application/pdf" required>
                            @error('identity_documents')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Verso de la piece</label>
                            <input type="file" name="identity_documents[]" class="form-control @error('identity_documents') is-invalid @enderror" accept="image/*,application/pdf" required>
                        </div>

                        <div class="form-group">
                            <label>Signature du parent</label>
                            <input type="text" name="verification_signature" class="form-control @error('verification_signature') is-invalid @enderror" value="{{ old('verification_signature') }}" placeholder="Nom complet et signature" required>
                            @error('verification_signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" value="1" required>
                            <label class="form-check-label" for="terms_accepted">J'accepte les reglements et les conditions de la societe</label>
                            @error('terms_accepted')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Soumettre la verification</button>
                        <a href="{{ route('parents.show', $parent) }}" class="btn btn-secondary">Retour</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (() => {
            const qrContainer = document.getElementById('verification-qr-code');

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: @json(route('parents.verification', $parent)),
                    width: 180,
                    height: 180,
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }
        })();
    </script>
@stop