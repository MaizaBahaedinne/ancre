<div class="row">
    <div class="col-md-6 form-group">
        <label>Nom</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', optional($user)->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', optional($user)->email) }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label>Mot de passe {{ $user ? '(laisser vide pour conserver)' : '' }}</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $user ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label>Confirmation mot de passe</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>

<div class="form-group">
    <label>Roles</label>
    <select name="roles[]" class="form-control @error('roles') is-invalid @enderror" multiple data-enhance-select="true" required>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(collect(old('roles', $user?->roles?->pluck('name')->all() ?? []))->contains($role->name))>{{ $role->name }}</option>
        @endforeach
    </select>
    @error('roles') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    @error('roles.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>