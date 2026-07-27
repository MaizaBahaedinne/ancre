<div class="row">
    <div class="col-12">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <p class="text-muted mb-4">
                <i class="fa-solid fa-shield me-2"></i>
                Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.
            </p>

            <!-- Current Password -->
            <div class="mb-3">
                <label for="current_password" class="form-label">
                    <i class="fa-solid fa-key me-2"></i>
                    Mot de passe actuel
                </label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                    required
                >
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fa-solid fa-lock me-2"></i>
                    Nouveau mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                    required
                >
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted d-block mt-2">
                    Minimum 8 caractères, mélange de majuscules et minuscules recommandé
                </small>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    <i class="fa-solid fa-check me-2"></i>
                    Confirmer le mot de passe
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                    required
                >
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-2"></i>
                    Mettre à jour
                </button>

                @if (session('status') === 'password-updated')
                    <div class="alert alert-success mb-0" role="alert">
                        <i class="fa-solid fa-check me-2"></i>
                        Mot de passe mis à jour avec succès
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
