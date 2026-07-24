@extends('adminlte::page')

@section('title', 'Modifier matiere')

@section('content_header')
    <h1 class="m-0">Modifier matiere</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('subjects.update', $subject) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6 form-group">
                        <label>Matiere</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Niveau</label>
                        <select name="level" class="form-control @error('level') is-invalid @enderror" required>
                            @foreach($levelOptions as $level)
                                <option value="{{ $level }}" @selected(old('level', $subject->level) === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Coefficient par defaut</label>
                        <input type="number" step="0.25" min="0.25" max="10" name="default_coefficient" class="form-control @error('default_coefficient') is-invalid @enderror" value="{{ old('default_coefficient', $subject->default_coefficient) }}" required>
                        @error('default_coefficient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-end form-group">
                        <div class="form-check mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', (string) (int) $subject->is_active) === '1')>
                            <label for="is_active" class="form-check-label">Matiere active</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre a jour</button>
                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Retour</a>
                </div>
            </form>
        </div>
    </div>
@stop
