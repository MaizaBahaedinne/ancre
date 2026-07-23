<x-guest-layout>
    <div class="d-flex flex-column gap-4">
        <div>
            <h1 class="h3 fw-bold mb-2 text-dark">Verification du compte parent</h1>
            <p class="text-secondary mb-0">Depuis ce smartphone, envoyez le recto, le verso et la signature manuscrite. L'ecran de la plateforme se mettra a jour automatiquement.</p>
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
                        <div class="d-grid gap-2 mt-3 text-start">
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Recto</span>
                                <span class="badge badge-secondary" data-mobile-status="recto">En attente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Verso</span>
                                <span class="badge badge-secondary" data-mobile-status="verso">En attente</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                                <span>Signature</span>
                                <span class="badge badge-secondary" data-mobile-status="signature">En attente</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="mb-3">
                            <div><strong>Nom:</strong> {{ $parent->nom }} {{ $parent->prenom }}</div>
                            <div><strong>CIN:</strong> {{ $parent->numero_cin ?: '-' }}</div>
                            <div><strong>Email:</strong> {{ $parent->email ?: '-' }}</div>
                            <div><strong>Statut:</strong> {{ ucfirst($parent->verification_status ?? 'pending') }}</div>
                        </div>

                        @if(($parent->verification_status ?? 'pending') !== 'verified')
                            @error('verification')<div class="alert alert-danger mb-0">{{ $message }}</div>@enderror

                            <form method="POST" action="{{ $verificationSubmitUrl }}" class="d-grid gap-3" id="parent-verification-form">
                                @csrf

                                <div class="alert alert-info mb-0">
                                    1. Envoyez le recto puis le verso. 2. Signez sur l'ecran. 3. Saisissez l'email et acceptez les conditions pour creer le compte parent.
                                </div>

                                <div>
                                    <label class="form-label">Email du parent</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $parent->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Recto de la piece</label>
                                        <input type="file" class="form-control" accept="application/pdf,image/*" data-document-side="cin_recto">
                                        <div class="small text-muted mt-1" data-upload-feedback="cin_recto">Choisissez une photo ou un scan du recto.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Verso de la piece</label>
                                        <input type="file" class="form-control" accept="application/pdf,image/*" data-document-side="cin_verso">
                                        <div class="small text-muted mt-1" data-upload-feedback="cin_verso">Choisissez une photo ou un scan du verso.</div>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span>Signature manuscrite du parent</span>
                                        <span class="small text-muted">Signez avec le doigt</span>
                                    </label>
                                    <canvas id="signature-pad" class="w-100 border rounded" width="600" height="220" style="touch-action:none; background:#fff;"></canvas>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="signature-clear-btn">Effacer</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="signature-save-btn">Enregistrer la signature</button>
                                    </div>
                                    <div class="small text-muted mt-2" id="signature-feedback">Aucune signature enregistree.</div>
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
            const statusUrl = @json($verificationStatusUrl);
            const documentUploadUrl = @json($verificationDocumentUrl);
            const signatureUploadUrl = @json($verificationSignatureUrl);
            const statusBadges = {
                recto: document.querySelector('[data-mobile-status="recto"]'),
                verso: document.querySelector('[data-mobile-status="verso"]'),
                signature: document.querySelector('[data-mobile-status="signature"]'),
            };
            const feedbackNodes = {
                cin_recto: document.querySelector('[data-upload-feedback="cin_recto"]'),
                cin_verso: document.querySelector('[data-upload-feedback="cin_verso"]'),
            };
            const signatureCanvas = document.getElementById('signature-pad');
            const signatureFeedback = document.getElementById('signature-feedback');
            const clearButton = document.getElementById('signature-clear-btn');
            const saveButton = document.getElementById('signature-save-btn');
            let drawing = false;
            let hasStroke = false;

            if (qrContainer) {
                new QRCode(qrContainer, {
                    text: @json($verificationUrl),
                    width: 180,
                    height: 180,
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }

            const setBadgeState = (element, ready, doneText = 'Recu') => {
                if (!element) {
                    return;
                }

                element.textContent = ready ? doneText : 'En attente';
                element.className = ready ? 'badge badge-success' : 'badge badge-secondary';
            };

            const loadStatus = async () => {
                try {
                    const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    setBadgeState(statusBadges.recto, Boolean(payload.recto?.ready));
                    setBadgeState(statusBadges.verso, Boolean(payload.verso?.ready));
                    setBadgeState(statusBadges.signature, Boolean(payload.signature?.ready), 'Signee');

                    if (payload.verified) {
                        window.setTimeout(() => window.location.reload(), 800);
                    }
                } catch (error) {
                    console.error(error);
                }
            };

            const uploadDocument = async (side, file) => {
                const formData = new FormData();
                formData.append('side', side);
                formData.append('cin_file', file);

                const feedback = feedbackNodes[side];
                if (feedback) {
                    feedback.textContent = 'Envoi en cours...';
                }

                const response = await fetch(documentUploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    if (feedback) {
                        feedback.textContent = 'Echec de l\'envoi. Reessayez.';
                    }

                    return;
                }

                if (feedback) {
                    feedback.textContent = 'Document recu sur la plateforme.';
                }

                await loadStatus();
            };

            document.querySelectorAll('[data-document-side]').forEach((input) => {
                input.addEventListener('change', async (event) => {
                    const file = event.target.files?.[0];
                    const side = event.target.getAttribute('data-document-side');

                    if (!file || !side) {
                        return;
                    }

                    await uploadDocument(side, file);
                });
            });

            if (signatureCanvas) {
                const context = signatureCanvas.getContext('2d');
                context.lineWidth = 2.5;
                context.lineCap = 'round';
                context.strokeStyle = '#0b2448';

                const pointFromEvent = (event) => {
                    const rect = signatureCanvas.getBoundingClientRect();
                    const touch = event.touches?.[0] || event.changedTouches?.[0];
                    const clientX = touch ? touch.clientX : event.clientX;
                    const clientY = touch ? touch.clientY : event.clientY;

                    return {
                        x: (clientX - rect.left) * (signatureCanvas.width / rect.width),
                        y: (clientY - rect.top) * (signatureCanvas.height / rect.height),
                    };
                };

                const startStroke = (event) => {
                    drawing = true;
                    hasStroke = true;
                    const point = pointFromEvent(event);
                    context.beginPath();
                    context.moveTo(point.x, point.y);
                    event.preventDefault();
                };

                const moveStroke = (event) => {
                    if (!drawing) {
                        return;
                    }

                    const point = pointFromEvent(event);
                    context.lineTo(point.x, point.y);
                    context.stroke();
                    event.preventDefault();
                };

                const endStroke = () => {
                    drawing = false;
                };

                ['mousedown', 'touchstart'].forEach((eventName) => signatureCanvas.addEventListener(eventName, startStroke, { passive: false }));
                ['mousemove', 'touchmove'].forEach((eventName) => signatureCanvas.addEventListener(eventName, moveStroke, { passive: false }));
                ['mouseup', 'mouseleave', 'touchend', 'touchcancel'].forEach((eventName) => signatureCanvas.addEventListener(eventName, endStroke));

                clearButton?.addEventListener('click', () => {
                    context.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
                    hasStroke = false;
                    if (signatureFeedback) {
                        signatureFeedback.textContent = 'Signature effacee. Signez de nouveau.';
                    }
                });

                saveButton?.addEventListener('click', async () => {
                    if (!hasStroke) {
                        if (signatureFeedback) {
                            signatureFeedback.textContent = 'Veuillez signer avant enregistrement.';
                        }

                        return;
                    }

                    if (signatureFeedback) {
                        signatureFeedback.textContent = 'Enregistrement de la signature...';
                    }

                    const response = await fetch(signatureUploadUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ signature_data: signatureCanvas.toDataURL('image/png') }),
                    });

                    if (!response.ok) {
                        if (signatureFeedback) {
                            signatureFeedback.textContent = 'Echec de l\'enregistrement de la signature.';
                        }

                        return;
                    }

                    if (signatureFeedback) {
                        signatureFeedback.textContent = 'Signature manuscrite enregistree.';
                    }

                    await loadStatus();
                });
            }

            loadStatus();
            window.setInterval(loadStatus, 3000);
        })();
    </script>
</x-guest-layout>