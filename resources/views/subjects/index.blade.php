@extends('adminlte::page')

@section('title', 'Matieres')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Matieres par niveau</h1>
        @can('subjects.create')
            <a href="{{ route('subjects.create') }}" class="btn btn-primary">Nouvelle matiere</a>
        @endcan
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Matiere</th>
                        <th>Coefficient</th>
                        <th>Statut</th>
                        <th width="170">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ $subject->level }}</td>
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
                            <td colspan="5" class="text-center">Aucune matiere configuree.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
