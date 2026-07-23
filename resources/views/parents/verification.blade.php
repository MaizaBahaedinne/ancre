<x-guest-layout>
    <div class="d-flex flex-column gap-4">
        <div>
            <h1 class="h3 fw-bold mb-2 text-dark">Verification du compte parent</h1>
            <p class="text-secondary mb-0">Cette page est accessible par QR code et ne demande aucun login pour finaliser la creation du compte parent.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-0">{{ session('success') }}</div>
        @endif

        @if (session('temporary_password'))
            <div class="alert alert-info mb-0">Compte parent cree avec succes. Mot de passe temporaire: <strong>{{ session('temporary_password') }}</strong></div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center">
                        <div id="verification-qr-code" class="d-inline-block p-2 bg-white border rounded mb-3"></div>
                        <div class="small text-muted">Scannez ce QR code pour ouvrir cette page sur mobile.</div>
                    </div>

                    <div class="col-md-7">
                        <div class="mb-3">
                            <div><strong>Nom:</strong> {{ $parent->nom }} {{ $parent->prenom }}</div>
                            <div><strong>CIN:</strong> {{ $parent->numero_cin ?: '-' }}</div>
                            <div><strong>Email:</strong> {{ $parent->email ?: '-' }}</div>
                            <div><strong>Statut:</strong> {{ ucfirst($parent->verification_status ?? 'pending') }}</div>
                        </div>

                        @if(($parent->verification_status ?? 'pending') !== 'verified')
                            <form method="POST" action="{{ $verificationUrl }}" enctype="multipart/form-data" class="d-grid gap-3">
                                @csrf

                                <div class="alert alert-info mb-0">
                                    Chargez le recto et le verso de la piece d'identite, ajoutez la signature du parent et acceptez les reglements avant de valider.
                                </div>

                                <div>
                                    <label class="form-label">Email du parent</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $parent->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label class="form-label">Recto de la piece</label>
                                    <input type="file" name="identity_documents[]" class="form-control @error('identity_documents') is-invalid @enderror" accept="application/pdf,image/*" required>
                                    @error('identity_documents')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label class="form-label">Verso de la piece</label>
                                    <input type="file" name="identity_documents[]" class="form-control" accept="application/pdf,image/*" required>
                                </div>

                                <div>
                                    <label class="form-label">Signature du parent</label>
                                    <input type="text" name="verification_signature" class="form-control @error('verification_signature') is-invalid @enderror" value="{{ old('verification_signature') }}" placeholder="Nom complet et signature" required>
                                    @error('verification_signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" value="1" required>
                                    <label class="form-check-label" for="terms_accepted">J'accepte les reglements et les conditions de la societe</label>
                                    @error('terms_accepted')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary">Soumettre la verification</button>
                                    <a href="{{ route('parents.show', $parent) }}" class="btn btn-outline-secondary">Retour admin</a>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-success mb-0">La verification est deja completee. Le compte parent a ete cree.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (() => {
            const qrContainer = document.getElementById('verification-qr-code');

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: @json($verificationUrl),
                    width: 180,
                    height: 180,
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }
        })();
    </script>
</x-guest-layout>