@extends('adminlte::page')

@section('title', 'Nouvelle matiere')

@section('content_header')
    <h1 class="m-0">Matieres par niveau</h1>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabList = document.getElementById('subject-level-tabs');
    const tabContent = document.getElementById('subject-level-tab-content');

    if (!tabList || !tabContent) {
        return;
    }

    const links = Array.from(tabList.querySelectorAll('.nav-link'));
    const panes = Array.from(tabContent.querySelectorAll('.tab-pane'));

    const activateTab = function (link) {
        const targetSelector = link.getAttribute('href');

        if (!targetSelector || !targetSelector.startsWith('#')) {
            return;
        }

        const targetPane = tabContent.querySelector(targetSelector);

        if (!targetPane) {
            return;
        }

        links.forEach(function (item) {
            item.classList.remove('active');
            item.setAttribute('aria-selected', 'false');
        });

        panes.forEach(function (pane) {
            pane.classList.remove('show', 'active');
        });

        link.classList.add('active');
        link.setAttribute('aria-selected', 'true');
        targetPane.classList.add('show', 'active');
    };

    links.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            activateTab(link);
        });
    });
});
</script>
@stop

@section('content')
    @php
        $activeLevel = old('level') ?: session('selected_level') ?: ($levelOptions[0] ?? null);
    @endphp

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="subject-level-tabs" role="tablist">
                @foreach($levelOptions as $level)
                    @php
                        $tabId = \Illuminate\Support\Str::slug($level);
                        $isActive = $activeLevel === $level;
                    @endphp
                    <li class="nav-item" role="presentation">
                        <a
                            class="nav-link {{ $isActive ? 'active' : '' }}"
                            id="tab-{{ $tabId }}"
                            data-toggle="pill"
                            href="#pane-{{ $tabId }}"
                            role="tab"
                            aria-controls="pane-{{ $tabId }}"
                            aria-selected="{{ $isActive ? 'true' : 'false' }}"
                        >
                            {{ $level }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="subject-level-tab-content">
                @foreach($levelOptions as $level)
                    @php
                        $tabId = \Illuminate\Support\Str::slug($level);
                        $isActive = $activeLevel === $level;
                        $levelSubjects = $subjectsByLevel->get($level, collect());
                    @endphp

                    <div
                        class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                        id="pane-{{ $tabId }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $tabId }}"
                    >
                        <div class="row g-3">
                            <div class="col-lg-7">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="mb-3">Matieres rattachees: {{ $level }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>Matiere</th>
                                                <th>Coefficient</th>
                                                <th>Statut</th>
                                                <th width="150">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($levelSubjects as $subject)
                                                <tr>
                                                    <td>{{ $subject->name }}</td>
                                                    <td>{{ number_format((float) $subject->default_coefficient, 2, ',', ' ') }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $subject->is_active ? 'success' : 'secondary' }}">
                                                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @can('subjects.update')
                                                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-warning">Modifier</a>
                                                        @endcan
                                                        @can('subjects.delete')
                                                            <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="d-inline" onsubmit="return confirm('Supprimer cette matiere ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Aucune matiere rattachee a ce niveau.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="border rounded p-3">
                                    <h5 class="mb-3">Ajouter une matiere</h5>
                                    <form method="POST" action="{{ route('subjects.store') }}">
                                        @csrf
                                        <input type="hidden" name="level" value="{{ $level }}">

                                        <div class="form-group">
                                            <label>Niveau</label>
                                            <input type="text" class="form-control" value="{{ $level }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Matiere</label>
                                            <input type="text" name="name" class="form-control @if($activeLevel === $level) @error('name') is-invalid @enderror @endif" value="{{ $activeLevel === $level ? old('name') : '' }}" required>
                                            @if($activeLevel === $level)
                                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>Coefficient par defaut</label>
                                            <input type="number" step="0.25" min="0.25" max="10" name="default_coefficient" class="form-control @if($activeLevel === $level) @error('default_coefficient') is-invalid @enderror @endif" value="{{ $activeLevel === $level ? old('default_coefficient', 1) : 1 }}" required>
                                            @if($activeLevel === $level)
                                                @error('default_coefficient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" class="form-check-input" id="is_active_{{ $tabId }}" name="is_active" value="1" @checked($activeLevel === $level ? old('is_active', '1') === '1' : true)>
                                            <label for="is_active_{{ $tabId }}" class="form-check-label">Matiere active</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Ajouter</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Retour a la liste</a>
            </div>
        </div>
    </div>
@stop
