@extends('adminlte::page')

@section('title', 'Vitrine - Blog & actualites')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card mb-4">
        <div class="card-header"><strong>Ajouter un article</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.blog-posts.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">Titre</label><input type="text" name="title" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Slug (optionnel)</label><input type="text" name="slug" class="form-control" placeholder="auto-genere si vide"></div>
                <div class="col-md-6"><label class="form-label">Image couverture (URL)</label><input type="text" name="cover_url" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Date publication</label><input type="datetime-local" name="published_at" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" min="0" max="999" value="0"></div>
                <div class="col-12"><label class="form-label">Extrait</label><textarea name="excerpt" rows="2" class="form-control"></textarea></div>
                <div class="col-12"><label class="form-label">Contenu</label><textarea name="content" rows="4" class="form-control"></textarea></div>
                <div class="col-md-3 d-flex align-items-end"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_published" value="1" checked> Publie</label></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Articles existants</strong></div>
        <div class="card-body">
            @foreach($blogPosts as $post)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.blog-posts.update', $post) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6"><input type="text" name="title" class="form-control form-control-sm" value="{{ $post->title }}" required></div>
                        <div class="col-md-6"><input type="text" name="slug" class="form-control form-control-sm" value="{{ $post->slug }}"></div>
                        <div class="col-md-6"><input type="text" name="cover_url" class="form-control form-control-sm" value="{{ $post->cover_url }}" placeholder="Image URL"></div>
                        <div class="col-md-3"><input type="datetime-local" name="published_at" class="form-control form-control-sm" value="{{ optional($post->published_at)->format('Y-m-d\\TH:i') }}"></div>
                        <div class="col-md-3"><input type="number" name="sort_order" class="form-control form-control-sm" min="0" max="999" value="{{ $post->sort_order }}"></div>
                        <div class="col-12"><textarea name="excerpt" rows="2" class="form-control form-control-sm">{{ $post->excerpt }}</textarea></div>
                        <div class="col-12"><textarea name="content" rows="3" class="form-control form-control-sm">{{ $post->content }}</textarea></div>
                        <div class="col-md-3 d-flex align-items-center"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }}> Publie</label></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Mettre a jour</button></div>
                    </form>
                    <form method="POST" action="{{ route('admin.vitrine.blog-posts.destroy', $post) }}" class="mt-2" onsubmit="return confirm('Supprimer cet article ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@stop
