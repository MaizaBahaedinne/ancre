@extends('adminlte::page')

@section('title', 'Nouveau Parent')

@section('content_header')
    <h1 class="m-0">Ajouter un parent</h1>
@stop

@section('content')
    @php
        $scanTokenValue = old('cin_scan_token', $scanToken ?? '');
        $scannerUrl = $scanTokenValue ? route('parents.cin-scanner', $scanTokenValue) : null;
        $scannerStatusUrl = $scanTokenValue ? route('parents.cin-scanner.status', $scanTokenValue) : null;
    @endphp

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('parents.store') }}" enctype="multipart/form-data" id="parent-create-form" data-scan-status-url="{{ $scannerStatusUrl }}">
                @csrf
                <input type="hidden" name="cin_scan_token" value="{{ $scanTokenValue }}">

                <div class="alert alert-info">
                    Vous pouvez soit charger les documents depuis cet appareil, soit scanner la CIN avec votre smartphone via le QR code ci-dessous.
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h3 class="card-title mb-0">Assistant de scan smartphone</h3>
                        @if($scannerUrl)
                            <span class="badge badge-primary">Token actif</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 text-center">
                                <div id="cin-qr-code" class="d-inline-block p-2 bg-white rounded border"></div>
                                @if($scannerUrl)
                                    <div class="mt-2">
                                        <a href="{{ $scannerUrl }}" target="_blank" rel="noopener">Ouvrir la page scanner</a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <p class="mb-2">Scannez ce QR code avec votre smartphone pour ouvrir la caméra et transférer les deux faces de la CIN.</p>
                                <div class="d-grid gap-2">
                                    <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2" data-scan-status-item="cin_recto">
                                        <span>Recto via smartphone</span>
                                        <span class="badge badge-secondary" data-scan-badge="cin_recto">En attente</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2" data-scan-status-item="cin_verso">
                                        <span>Verso via smartphone</span>
                                        <span class="badge badge-secondary" data-scan-badge="cin_verso">En attente</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">Le bouton d'enregistrement reste disponible aussi pour l'upload classique depuis PC, tablette ou mobile.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Prenom</label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>N Carte d'identite nationale</label>
                        <input type="text" name="numero_cin" class="form-control @error('numero_cin') is-invalid @enderror" value="{{ old('numero_cin') }}" required>
                        @error('numero_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date de delivrance</label>
                        <input type="date" name="date_delivrance_cin" class="form-control @error('date_delivrance_cin') is-invalid @enderror" value="{{ old('date_delivrance_cin') }}" required>
                        @error('date_delivrance_cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control @error('date_naissance') is-invalid @enderror" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sexe</label>
                        <select name="sexe" class="form-control @error('sexe') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            <option value="M" @selected(old('sexe') === 'M')>M</option>
                            <option value="F" @selected(old('sexe') === 'F')>F</option>
                        </select>
                        @error('sexe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Telephone</label>
                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}" required>
                        @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" class="form-control @error('profession') is-invalid @enderror" value="{{ old('profession') }}">
                        @error('profession') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Contact urgence</label>
                        <input type="text" name="contact_urgence" class="form-control @error('contact_urgence') is-invalid @enderror" value="{{ old('contact_urgence') }}">
                        @error('contact_urgence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror" value="{{ old('adresse') }}">
                    @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Documents d'identite (obligatoires)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>CIN Recto (image ou PDF)</label>
                                <input type="file" name="cin_recto" id="cin-recto-file" class="form-control-file @error('cin_recto') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('cin_recto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>CIN Verso (image ou PDF)</label>
                                <input type="file" name="cin_verso" id="cin-verso-file" class="form-control-file @error('cin_verso') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('cin_verso') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <small class="text-muted">Le profil parent ne peut pas etre valide sans ces documents.</small>
                    </div>
                </div>

                <button class="btn btn-primary" id="parent-submit-btn">Enregistrer</button>
                <a href="{{ route('parents.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
@stop

@section('js')
    @if($scannerUrl)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            (() => {
                const qrContainer = document.getElementById('cin-qr-code');
                const form = document.getElementById('parent-create-form');
                const statusUrl = form?.dataset.scanStatusUrl;
                const submitButton = document.getElementById('parent-submit-btn');
                const rectoInput = document.getElementById('cin-recto-file');
                const versoInput = document.getElementById('cin-verso-file');
                const badgeRecto = document.querySelector('[data-scan-badge="cin_recto"]');
                const badgeVerso = document.querySelector('[data-scan-badge="cin_verso"]');
                const tokenValue = @json($scanTokenValue);
                let scanState = { recto: false, verso: false };

                if (qrContainer) {
                    new QRCode(qrContainer, {
                        text: @json($scannerUrl),
                        width: 180,
                        height: 180,
                        correctLevel: QRCode.CorrectLevel.M,
                    });
                }

                const manualReady = (input) => Boolean(input && input.files && input.files.length > 0);

                const refreshUi = () => {
                    const rectoReady = manualReady(rectoInput) || scanState.recto;
                    const versoReady = manualReady(versoInput) || scanState.verso;

                    if (badgeRecto) {
                        badgeRecto.textContent = rectoReady ? 'Prêt' : 'En attente';
                        badgeRecto.className = rectoReady ? 'badge badge-success' : 'badge badge-secondary';
                    }

                    if (badgeVerso) {
                        badgeVerso.textContent = versoReady ? 'Prêt' : 'En attente';
                        badgeVerso.className = versoReady ? 'badge badge-success' : 'badge badge-secondary';
                    }

                    if (submitButton) {
                        submitButton.disabled = !(rectoReady && versoReady);
                    }
                };

                const loadStatus = async () => {
                    if (!statusUrl || !tokenValue) {
                        refreshUi();
                        return;
                    }

                    try {
                        const response = await fetch(statusUrl, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (!response.ok) {
                            refreshUi();
                            return;
                        }

                        const payload = await response.json();
                        scanState = {
                            recto: Boolean(payload.recto),
                            verso: Boolean(payload.verso),
                        };
                    } catch (error) {
                        // ignore polling errors and keep the form usable for manual upload
                    }

                    refreshUi();
                };

                rectoInput?.addEventListener('change', refreshUi);
                versoInput?.addEventListener('change', refreshUi);

                refreshUi();
                loadStatus();
                window.setInterval(loadStatus, 4000);
            })();
        </script>
    @endif
@stop
