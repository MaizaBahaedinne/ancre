@extends('adminlte::page')

@section('title', 'Roles et permissions')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Roles et permissions</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Nouveau role</a>
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
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Utilisateurs</th>
                        <th class="no-sort" width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                <div class="modern-action-group">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="modern-action-btn is-edit" title="Modifier"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer ce role ?')">
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