<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verification parent - Ancre Des Elites</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --brand: #0c7abf;
            --brand-dark: #0b2448;
            --surface: rgba(255, 255, 255, 0.95);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 80% 12%, rgba(14, 165, 233, 0.18), transparent 28%),
                radial-gradient(circle at 10% 85%, rgba(245, 158, 11, 0.16), transparent 32%),
                linear-gradient(160deg, #edf5fb 0%, #f8fafc 50%, #eef2f9 100%);
        }

        .verification-shell {
            max-width: 860px;
            margin: 0 auto;
            padding: 1rem;
        }

        .verification-card {
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 28px;
            background: var(--surface);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .verification-hero {
            padding: 1.35rem;
            background: linear-gradient(145deg, rgba(13, 34, 66, 0.96), rgba(13, 56, 108, 0.92));
            color: #eff6ff;
        }

        .verification-hero h1 {
            margin: 0;
            font-size: clamp(1.35rem, 3vw, 2rem);
            font-weight: 800;
        }

        .verification-hero p {
            margin: 0.35rem 0 0;
            color: rgba(226, 232, 240, 0.92);
        }

        .verification-body {
            padding: 1rem;
        }

        .status-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .status-box {
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 16px;
            padding: 0.85rem;
            background: #f8fafc;
        }

        .preview-card {
            min-height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px dashed rgba(148, 163, 184, 0.5);
            border-radius: 16px;
            overflow: hidden;
        }

        .preview-card img {
            width: 100%;
            height: auto;
            display: block;
        }

        .signature-canvas {
            width: 100%;
            height: auto;
            touch-action: none;
            background: #fff;
        }

        .thanks-panel {
            text-align: center;
            padding: 2rem 1rem;
        }

        .thanks-mark {
            width: 4rem;
            height: 4rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(22, 163, 74, 0.12);
            color: #15803d;
            font-size: 1.6rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 767.98px) {
            .status-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="verification-shell">
        <section class="verification-card">
            <header class="verification-hero">
                <h1>Verification du compte parent</h1>
                <p>Une seule page mobile pour envoyer les documents, signer sur l'ecran et finaliser la creation du compte.</p>
            </header>

            <div class="verification-body d-flex flex-column gap-4">
                <div>
                    <div class="fw-semibold">Parent</div>
                    <div class="text-muted">{{ $parent->nom }} {{ $parent->prenom }} | CIN: {{ $parent->numero_cin ?: '-' }}</div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                @endif

                @if (session('temporary_password'))
                    <div class="alert alert-info mb-0">Compte parent cree avec succes. Mot de passe temporaire: <strong>{{ session('temporary_password') }}</strong></div>
                @endif

                @if(($parent->verification_status ?? 'pending') === 'verified')
                    <section class="thanks-panel">
                        <div class="thanks-mark"><i class="fa-solid fa-check"></i></div>
                        <h2 class="h3 fw-bold">Merci, compte cree</h2>
                        <p class="text-muted mb-3">Les documents et la signature ont bien ete recus. Le compte parent est maintenant cree dans la plateforme.</p>
                        <div class="status-strip mt-4">
                            <div class="status-box">
                                <div class="small text-muted">Recto</div>
                                <div class="fw-semibold text-success">Recu</div>
                            </div>
                            <div class="status-box">
                                <div class="small text-muted">Verso</div>
                                <div class="fw-semibold text-success">Recu</div>
                            </div>
                            <div class="status-box">
                                <div class="small text-muted">Signature</div>
                                <div class="fw-semibold text-success">Signee</div>
                            </div>
                        </div>
                    </section>
                @else
                    @error('verification')<div class="alert alert-danger mb-0">{{ $message }}</div>@enderror

                    <div class="status-strip">
                        <div class="status-box d-flex justify-content-between align-items-center gap-2">
                            <span>Recto</span>
                            <span class="badge badge-secondary" data-mobile-status="recto">En attente</span>
                        </div>
                        <div class="status-box d-flex justify-content-between align-items-center gap-2">
                            <span>Verso</span>
                            <span class="badge badge-secondary" data-mobile-status="verso">En attente</span>
                        </div>
                        <div class="status-box d-flex justify-content-between align-items-center gap-2">
                            <span>Signature</span>
                            <span class="badge badge-secondary" data-mobile-status="signature">En attente</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ $verificationSubmitUrl }}" class="d-grid gap-4" id="parent-verification-form">
                        @csrf

                        <div class="alert alert-info mb-0">
                            1. Envoyez le recto. 2. Envoyez le verso. 3. Signez sur l'ecran. 4. Finalisez la creation du compte.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Recto de la piece</label>
                                <input type="file" class="form-control" accept="application/pdf,image/*" data-document-side="cin_recto">
                                <div class="small text-muted mt-1" data-upload-feedback="cin_recto">Choisissez une photo ou un scan du recto.</div>
                                <div class="preview-card mt-2" data-mobile-preview="cin_recto"><span class="small text-muted">Aucun recto recu</span></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Verso de la piece</label>
                                <input type="file" class="form-control" accept="application/pdf,image/*" data-document-side="cin_verso">
                                <div class="small text-muted mt-1" data-upload-feedback="cin_verso">Choisissez une photo ou un scan du verso.</div>
                                <div class="preview-card mt-2" data-mobile-preview="cin_verso"><span class="small text-muted">Aucun verso recu</span></div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Signature manuscrite du parent</span>
                                <span class="small text-muted">Signez avec le doigt</span>
                            </label>
                            <canvas id="signature-pad" class="signature-canvas border rounded" width="600" height="220"></canvas>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="signature-clear-btn">Effacer</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="signature-save-btn">Enregistrer la signature</button>
                            </div>
                            <div class="small text-muted mt-2" id="signature-feedback">Aucune signature enregistree.</div>
                            <div class="preview-card mt-2" data-mobile-preview="signature"><span class="small text-muted">Aucune signature recu</span></div>
                        </div>

                        <div>
                            <label class="form-label">Email du parent</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $parent->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" value="1" required>
                            <label class="form-check-label" for="terms_accepted">J'accepte les reglements et les conditions de la societe</label>
                            @error('terms_accepted')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary" id="verification-submit-btn" disabled>Finaliser et creer le compte</button>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (() => {
            const statusUrl = @json($verificationStatusUrl);
            const documentUploadUrl = @json($verificationDocumentUrl);
            const signatureUploadUrl = @json($verificationSignatureUrl);
            const verificationSubmitButton = document.getElementById('verification-submit-btn');
            const statusBadges = {
                recto: document.querySelector('[data-mobile-status="recto"]'),
                verso: document.querySelector('[data-mobile-status="verso"]'),
                signature: document.querySelector('[data-mobile-status="signature"]'),
            };
            const previewNodes = {
                cin_recto: document.querySelector('[data-mobile-preview="cin_recto"]'),
                cin_verso: document.querySelector('[data-mobile-preview="cin_verso"]'),
                signature: document.querySelector('[data-mobile-preview="signature"]'),
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

            const setBadgeState = (element, ready, doneText = 'Recu') => {
                if (!element) {
                    return;
                }

                element.textContent = ready ? doneText : 'En attente';
                element.className = ready ? 'badge badge-success' : 'badge badge-secondary';
            };

            const renderPreview = (container, payload, emptyLabel) => {
                if (!container) {
                    return;
                }

                if (!payload?.ready || !payload?.url) {
                    container.innerHTML = `<span class="small text-muted">${emptyLabel}</span>`;
                    return;
                }

                if (payload.is_image) {
                    container.innerHTML = `<img src="${payload.url}" alt="Apercu">`;
                    return;
                }

                container.innerHTML = `<a href="${payload.url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Ouvrir ${payload.extension ? payload.extension.toUpperCase() : 'fichier'}</a>`;
            };

            const syncSubmitState = (payload) => {
                if (!verificationSubmitButton) {
                    return;
                }

                const ready = Boolean(payload.recto?.ready && payload.verso?.ready && payload.signature?.ready);
                verificationSubmitButton.disabled = !ready;
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
                    renderPreview(previewNodes.cin_recto, payload.recto, 'Aucun recto recu');
                    renderPreview(previewNodes.cin_verso, payload.verso, 'Aucun verso recu');
                    renderPreview(previewNodes.signature, payload.signature, 'Aucune signature recue');
                    syncSubmitState(payload);

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
                    const signaturePreview = previewNodes.signature;
                    if (signaturePreview) {
                        signaturePreview.innerHTML = '<span class="small text-muted">Aucune signature recue</span>';
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
</body>
</html>