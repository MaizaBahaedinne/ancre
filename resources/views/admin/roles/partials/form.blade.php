<div class="form-group">
    <label>Nom du role</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', optional($role)->name) }}" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Permissions</label>
    <div class="row">
        @foreach($permissions as $group => $items)
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <strong>{{ ucfirst($group) }}</strong>
                    </div>
                    <div class="card-body">
                        @foreach($items as $permission)
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    id="permission_{{ str_replace('.', '_', $permission->name) }}"
                                    @checked(collect(old('permissions', $role?->permissions?->pluck('name')->all() ?? []))->contains($permission->name))
                                >
                                <label class="form-check-label" for="permission_{{ str_replace('.', '_', $permission->name) }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @error('permissions') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    @error('permissions.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>