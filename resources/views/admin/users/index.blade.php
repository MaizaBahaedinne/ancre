@extends('adminlte::page')

@section('title', 'Utilisateurs')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Gestion des utilisateurs</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Nouvel utilisateur</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Date creation</th>
                        <th class="no-sort" width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleNames()->join(', ') ?: '-' }}</td>
                            <td>{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="modern-action-group">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="modern-action-btn is-edit" title="Modifier"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="modern-action-btn is-delete" type="submit" title="Supprimer"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop