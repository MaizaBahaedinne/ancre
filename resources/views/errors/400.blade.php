@extends('errors.minimal')

@section('title', '400 - Requete invalide')
@section('code', '400')
@section('message', 'Requete invalide')
@section('description', 'La demande envoyee au serveur est incomplete ou mal formee. Verifiez les informations saisies et recommencez.')
@section('icon', 'fa-solid fa-file-circle-xmark')
@section('hint', 'Cette erreur apparait souvent lorsqu\'une action est lancee avec des donnees manquantes ou non valides.')