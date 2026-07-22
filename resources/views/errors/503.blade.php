@extends('errors.minimal')

@section('title', '503 - Service indisponible')
@section('code', '503')
@section('message', 'Service indisponible')
@section('description', 'La plateforme est temporairement indisponible, probablement pour maintenance ou surcharge ponctuelle.')
@section('icon', 'fa-solid fa-screwdriver-wrench')
@section('hint', 'Reessayez dans quelques minutes.')