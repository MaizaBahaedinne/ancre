@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-user-circle me-2"></i>
                        Informations du Profil
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-lock me-2"></i>
                        Modifier le Mot de Passe
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light border-danger">
                    <h5 class="mb-0 text-danger">
                        <i class="fa-solid fa-trash me-2"></i>
                        Supprimer le Compte
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
