@extends('errors.minimal')

@section('title', '500 - Erreur serveur')
@section('code', '500')
@section('message', 'Erreur interne du serveur')
@section('description', 'Une erreur inattendue est survenue pendant le traitement de votre demande.')
@section('icon', 'fa-solid fa-server')
@section('hint', 'Si le probleme persiste, transmettez l\'heure et l\'action effectuee a l\'equipe technique.')