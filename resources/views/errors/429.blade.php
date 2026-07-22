@extends('errors.minimal')

@section('title', '429 - Trop de requetes')
@section('code', '429')
@section('message', 'Trop de requetes')
@section('description', 'Vous avez effectue trop d\'actions en peu de temps. Patientez quelques instants avant de reessayer.')
@section('icon', 'fa-solid fa-gauge-high')
@section('hint', 'Le systeme limite temporairement certaines actions pour proteger la plateforme.')