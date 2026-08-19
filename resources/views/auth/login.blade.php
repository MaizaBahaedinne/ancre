<x-guest-layout>
    <div class="auth-card-header">
        <span class="auth-pill"><i class="fa-solid fa-shield-halved"></i> Connexion parent securisee</span>
        <p class="auth-kicker">Bienvenue parent</p>
        <h1 class="auth-title">Accedez a votre espace famille</h1>
        <p class="auth-subtitle">Connectez-vous pour suivre la scolarite et les informations de votre enfant.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <label for="email" class="form-label">Email parent</label>
            <div class="auth-input-wrap">
                <i class="fa-regular fa-envelope"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control auth-input @error('email') is-invalid @enderror" placeholder="parent@domaine.tn">
            </div>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="auth-input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control auth-input @error('password') is-invalid @enderror" placeholder="Votre mot de passe">
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="auth-form-row">
            <label for="remember_me" class="form-check d-flex align-items-center gap-2 m-0">
                <input id="remember_me" type="checkbox" class="form-check-input m-0" name="remember">
                <span class="form-check-label">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Mot de passe oublie ?</a>
            @endif
        </div>

        <div class="auth-assist-row">
            <span><i class="fa-solid fa-lock"></i> Session parent protegee</span>
            <span><i class="fa-regular fa-clock"></i> Acces rapide aux infos famille</span>
        </div>

        <button type="submit" class="btn btn-primary auth-submit-btn">Se connecter</button>

        <p class="auth-footnote">En vous connectant, vous acceptez les regles de securite de l'espace parent.</p>
    </form>
</x-guest-layout>
