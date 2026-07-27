<div class="row">
    <div class="col-12">
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <!-- Name Field -->
            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="fa-solid fa-user me-2"></i>
                    Nom Complet
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus
                >
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Field (Read-only) -->
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fa-solid fa-envelope me-2"></i>
                    Adresse Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    class="form-control" 
                    value="{{ $user->email }}" 
                    disabled
                >
                <small class="form-text text-muted d-block mt-2">
                    <i class="fa-solid fa-lock-open me-1"></i>
                    L'adresse email ne peut pas être modifiée
                </small>
            </div>

            <!-- Email Verification Status -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning" role="alert">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    Votre adresse email n'est pas vérifiée.
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0">Cliquez ici pour renvoyer l'email de vérification</button>
                    </form>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        Un nouveau lien de vérification a été envoyé à votre adresse email.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endif

            <!-- Submit Button -->
            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i>
                    Enregistrer les modifications
                </button>

                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success mb-0" role="alert">
                        <i class="fa-solid fa-check me-2"></i>
                        Profil mis à jour avec succès
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
