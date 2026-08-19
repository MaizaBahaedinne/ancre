<x-guest-layout>
    <div class="auth-logo-row">
        <img src="{{ asset('images/logo encre des elites.webp') }}" alt="Logo Ancre Des Elites">
        <span>Ancre Des Elites</span>
    </div>

    <h1 class="auth-title">Connexion parent</h1>
    <p class="auth-subtitle">Accedez a votre espace famille pour suivre la scolarite de votre enfant.</p>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email parent</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-lg auth-control @error('email') is-invalid @enderror" placeholder="parent@domaine.tn">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control form-control-lg auth-control @error('password') is-invalid @enderror" placeholder="Votre mot de passe">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center gap-2 mb-4 flex-wrap">
            <div class="form-check m-0">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label">Se souvenir de moi</label>
            </div>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Mot de passe oublie ?</a>
            @endif
        </div>

        <div class="pt-1 mb-3">
            <button class="btn auth-login-btn btn-lg w-100" type="submit">Se connecter</button>
        </div>

        <p class="auth-meta mb-1">Connexion reservee aux parents et tuteurs autorises.</p>

        @if (Route::has('register'))
            <p class="auth-meta mb-0">Vous n'avez pas de compte ? <a href="{{ route('register') }}" class="auth-link">Creer un compte</a></p>
        @endif
    </form>
</x-guest-layout>
