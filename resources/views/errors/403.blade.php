@extends('errors.minimal')

@section('title', '403 - Acces refuse')
@section('code', '403')
@section('message', 'Acces refuse')
@section('description', 'Votre compte n\'a pas les droits necessaires pour afficher cette page ou executer cette action.')
@section('icon', 'fa-solid fa-shield-halved')
@section('hint', 'Si vous pensez que cet acces devrait etre autorise, verifiez les roles et permissions du compte concerne.')