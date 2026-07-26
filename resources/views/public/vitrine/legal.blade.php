@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | '.($title ?? 'Informations'))
@section('meta_description', ($title ?? 'Informations').' - Ancre des Elites')

@section('content')
    <main>
        <section class="section section-soft">
            <div class="wrap">
                <article class="panel" style="max-width:900px;margin:0 auto;">
                    <h1 class="section-title title-center" style="margin-bottom:0.8rem;">{{ $title ?? 'Informations' }}</h1>
                    <p class="muted" style="font-size:1.05rem;line-height:1.8;">{{ $content ?? '' }}</p>
                </article>
            </div>
        </section>
    </main>
@endsection
