<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner CIN - Ancre Des Elites</title>
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
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
            background:
                radial-gradient(circle at 80% 12%, rgba(14, 165, 233, 0.18), transparent 28%),
                radial-gradient(circle at 10% 85%, rgba(245, 158, 11, 0.16), transparent 32%),
                linear-gradient(160deg, #edf5fb 0%, #f8fafc 50%, #eef2f9 100%);
            color: var(--ink);
        }

        .scanner-shell {
            max-width: 980px;
            margin: 0 auto;
            padding: 1rem;
        }

        .scanner-card {
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .scanner-hero {
            padding: 1.25rem;
            background: linear-gradient(145deg, rgba(13, 34, 66, 0.96), rgba(13, 56, 108, 0.92));
            color: #eff6ff;
        }

        .scanner-hero h1 {
            margin: 0;
            font-size: clamp(1.35rem, 3vw, 2rem);
            font-weight: 800;
        }

        .scanner-hero p {
            margin: 0.35rem 0 0;
            color: rgba(226, 232, 240, 0.92);
        }

        .scanner-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, 360px);
            gap: 1rem;
            padding: 1rem;
        }

        .camera-panel,
        .status-panel {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 18px;
            background: #fff;
            padding: 1rem;
        }

        .camera-stage {
            position: relative;
            aspect-ratio: 4 / 3;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(180deg, #0f172a, #1e293b);
        }

        .camera-stage video,
        .camera-stage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .camera-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.88);
            text-align: center;
            padding: 1rem;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.1), rgba(15, 23, 42, 0.3));
        }

        .scanner-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .scanner-actions .btn {
            min-height: 3rem;
            border-radius: 14px;
            font-weight: 700;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 0.85rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 14px;
            margin-bottom: 0.75rem;
        }

        .status-title {
            font-weight: 700;
        }

        .preview-box {
            margin-top: 0.75rem;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: #f8fafc;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-box img {
            width: 100%;
            height: auto;
            display: block;
        }

        .fallback-input {
            display: none;
        }

        .helper-text {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .feedback {
            margin-top: 0.75rem;
            padding: 0.75rem 0.9rem;
            border-radius: 12px;
            background: #eff6ff;
            color: #0b3b66;
            font-size: 0.92rem;
        }

        @media (max-width: 767.98px) {
            .scanner-grid {
                grid-template-columns: 1fr;
            }

            .scanner-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="scanner-shell">
        <section class="scanner-card">
            <header class="scanner-hero">
                <h1>Scanner la piece d'identite</h1>
                <p>Utilisez la camera de votre smartphone pour envoyer le recto et le verso. Le dossier sur l'ordinateur attendra la reception des images.</p>
            </header>

            <div class="scanner-grid">
                <div class="camera-panel">
                    <div class="camera-stage mb-3">
                        <video id="cin-camera" playsinline autoplay muted></video>
                        <img id="cin-preview" alt="Apercu de la capture" class="d-none">
                        <div class="camera-overlay" id="camera-overlay">
                            <div>
                                <i class="fa-solid fa-camera fa-2x mb-2"></i>
                                <div>Activez la camera puis capturez le recto ou le verso.</div>
                            </div>
                        </div>
                    </div>

                    <div class="scanner-actions">
                        <button type="button" class="btn btn-dark" data-capture-side="cin_recto">
                            <i class="fa-solid fa-id-card me-1"></i> Capturer recto
                        </button>
                        <button type="button" class="btn btn-primary" data-capture-side="cin_verso">
                            <i class="fa-solid fa-id-card-clip me-1"></i> Capturer verso
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="start-camera-btn">
                            <i class="fa-solid fa-video me-1"></i> Activer camera
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="switch-camera-btn">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Recharger statut
                        </button>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Ou charger un fichier depuis l'appareil</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="file" id="fallback-recto" class="form-control fallback-input" accept="image/*" capture="environment">
                                <label for="fallback-recto" class="btn btn-outline-dark w-100">Choisir recto</label>
                            </div>
                            <div class="col-md-6">
                                <input type="file" id="fallback-verso" class="form-control fallback-input" accept="image/*" capture="environment">
                                <label for="fallback-verso" class="btn btn-outline-primary w-100">Choisir verso</label>
                            </div>
                        </div>
                        <p class="helper-text mt-3 mb-0">Si votre telephone ne permet pas la camera live, utilisez la capture de fichier classique.</p>
                    </div>

                    <canvas id="capture-canvas" class="d-none"></canvas>
                    <div id="feedback" class="feedback d-none"></div>
                </div>

                <aside class="status-panel">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 mb-0">Etat du dossier</h2>
                        <span class="badge bg-secondary" id="overall-status">En attente</span>
                    </div>

                    <div class="status-item">
                        <div>
                            <div class="status-title">Recto</div>
                            <small class="text-muted">Attente ou reception</small>
                        </div>
                        <span class="badge bg-secondary" id="recto-status">En attente</span>
                    </div>
                    <div class="status-item">
                        <div>
                            <div class="status-title">Verso</div>
                            <small class="text-muted">Attente ou reception</small>
                        </div>
                        <span class="badge bg-secondary" id="verso-status">En attente</span>
                    </div>

                    <div class="preview-box" id="recto-preview-box">
                        <span class="text-muted">Apercu recto</span>
                    </div>
                    <div class="preview-box mt-3" id="verso-preview-box">
                        <span class="text-muted">Apercu verso</span>
                    </div>

                    <p class="helper-text mt-3 mb-0">
                        Le formulaire desktop reste en attente jusqu'a ce que les deux images soient recues.
                    </p>
                </aside>
            </div>
        </section>
    </main>

    <script>
        (() => {
            const uploadUrl = @json($uploadUrl);
            const statusUrl = @json($statusUrl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const camera = document.getElementById('cin-camera');
            const preview = document.getElementById('cin-preview');
            const overlay = document.getElementById('camera-overlay');
            const canvas = document.getElementById('capture-canvas');
            const feedback = document.getElementById('feedback');
            const overallStatus = document.getElementById('overall-status');
            const rectoStatus = document.getElementById('recto-status');
            const versoStatus = document.getElementById('verso-status');
            const rectoPreviewBox = document.getElementById('recto-preview-box');
            const versoPreviewBox = document.getElementById('verso-preview-box');
            const startCameraBtn = document.getElementById('start-camera-btn');
            const switchCameraBtn = document.getElementById('switch-camera-btn');
            const rectoFallback = document.getElementById('fallback-recto');
            const versoFallback = document.getElementById('fallback-verso');
            const captureButtons = document.querySelectorAll('[data-capture-side]');

            let stream = null;
            let statusState = { recto: null, verso: null, completed: false };

            const showMessage = (message, isError = false) => {
                feedback.textContent = message;
                feedback.classList.remove('d-none');
                feedback.style.background = isError ? '#fef2f2' : '#eff6ff';
                feedback.style.color = isError ? '#991b1b' : '#0b3b66';
            };

            const hideMessage = () => feedback.classList.add('d-none');

            const updatePreview = (box, url) => {
                box.innerHTML = '';

                if (!url) {
                    box.innerHTML = '<span class="text-muted">Apercu indisponible</span>';
                    return;
                }

                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Apercu document';
                box.appendChild(img);
            };

            const renderStatus = () => {
                const rectoReady = Boolean(statusState.recto);
                const versoReady = Boolean(statusState.verso);
                rectoStatus.className = rectoReady ? 'badge bg-success' : 'badge bg-secondary';
                versoStatus.className = versoReady ? 'badge bg-success' : 'badge bg-secondary';
                rectoStatus.textContent = rectoReady ? 'Recu' : 'En attente';
                versoStatus.textContent = versoReady ? 'Recu' : 'En attente';
                overallStatus.className = statusState.completed ? 'badge bg-success' : 'badge bg-secondary';
                overallStatus.textContent = statusState.completed ? 'Pret' : 'En attente';
                updatePreview(rectoPreviewBox, statusState.recto?.url || null);
                updatePreview(versoPreviewBox, statusState.verso?.url || null);
                overlay.classList.toggle('d-none', Boolean(stream));
            };

            const refreshStatus = async () => {
                try {
                    const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    statusState = {
                        recto: payload.recto,
                        verso: payload.verso,
                        completed: Boolean(payload.completed),
                    };
                    renderStatus();
                } catch (error) {
                    // Keep the interface usable even if polling fails.
                }
            };

            const uploadFile = async (side, file) => {
                const formData = new FormData();
                formData.append('side', side);
                formData.append('cin_file', file);
                formData.append('_token', csrfToken);

                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                const payload = await response.json();
                showMessage(`Document ${side === 'cin_recto' ? 'recto' : 'verso'} transmis avec succes.`);
                await refreshStatus();
                return payload;
            };

            const startCamera = async () => {
                try {
                    if (stream) {
                        return;
                    }

                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false,
                    });

                    camera.srcObject = stream;
                    await camera.play();
                    renderStatus();
                    showMessage('Camera activee. Capturez maintenant le recto ou le verso.');
                } catch (error) {
                    showMessage('Impossible d activer la camera. Utilisez le chargement de fichier classique.', true);
                }
            };

            const captureFromCamera = async (side) => {
                if (!stream) {
                    await startCamera();
                }

                if (!stream || !camera.videoWidth || !camera.videoHeight) {
                    showMessage('La camera n est pas encore prete.', true);
                    return;
                }

                const context = canvas.getContext('2d');
                canvas.width = camera.videoWidth;
                canvas.height = camera.videoHeight;
                context.drawImage(camera, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92));
                if (!blob) {
                    showMessage('Capture impossible. Reessayez.', true);
                    return;
                }

                const file = new File([blob], `${side}.jpg`, { type: 'image/jpeg' });
                await uploadFile(side, file);
            };

            captureButtons.forEach((button) => {
                button.addEventListener('click', () => captureFromCamera(button.dataset.captureSide));
            });

            startCameraBtn.addEventListener('click', startCamera);
            switchCameraBtn.addEventListener('click', refreshStatus);

            rectoFallback.addEventListener('change', async () => {
                if (rectoFallback.files?.[0]) {
                    try {
                        await uploadFile('cin_recto', rectoFallback.files[0]);
                    } catch (error) {
                        showMessage('Le chargement du recto a echoue.', true);
                    }
                }
            });

            versoFallback.addEventListener('change', async () => {
                if (versoFallback.files?.[0]) {
                    try {
                        await uploadFile('cin_verso', versoFallback.files[0]);
                    } catch (error) {
                        showMessage('Le chargement du verso a echoue.', true);
                    }
                }
            });

            refreshStatus();
            window.setInterval(refreshStatus, 3500);
        })();
    </script>
</body>
</html>
