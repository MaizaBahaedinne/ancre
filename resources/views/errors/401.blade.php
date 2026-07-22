@extends('errors.minimal')

@section('title', '401 - Authentification requise')
@section('code', '401')
@section('message', 'Authentification requise')
@section('description', 'Vous devez vous connecter pour acceder a cette ressource.')
@section('icon', 'fa-solid fa-user-lock')
@section('hint', 'Reconnectez-vous puis relancez l\'action demandee.')