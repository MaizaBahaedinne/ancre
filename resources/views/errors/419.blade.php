@extends('errors.minimal')

@section('title', '419 - Session expiree')
@section('code', '419')
@section('message', 'Session expiree')
@section('description', 'Votre session a expire. Rechargez la page puis recommencez votre action.')
@section('icon', 'fa-solid fa-hourglass-end')
@section('hint', 'Cette erreur apparait souvent apres un long delai avant validation d\'un formulaire.')