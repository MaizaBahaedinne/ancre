@extends('adminlte::page')

@section('title', 'Vitrine - Reseaux sociaux')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card">
        <div class="card-header"><strong>Publications sociales (Facebook / Instagram / TikTok)</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.social-posts.store') }}" class="row g-2 mb-3" enctype="multipart/form-data">
                @csrf
                <div class="col-md-2"><input type="text" name="platform" class="form-control" placeholder="facebook" required></div>
                <div class="col-md-4"><input type="text" name="post_url" class="form-control" placeholder="URL publication" required></div>
                <div class="col-md-3"><input type="text" name="thumbnail_url" class="form-control" placeholder="URL miniature"></div>
                <div class="col-md-1"><input type="number" name="sort_order" class="form-control" placeholder="#" min="0" max="999"></div>
                <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Actif</label></div>
                <div class="col-12"><input type="file" name="thumbnail_image" class="form-control" accept="image/*"></div>
                <div class="col-12"><textarea name="caption" rows="2" class="form-control" placeholder="Legende"></textarea></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter publication</button></div>
            </form>

            @foreach($socialPosts as $post)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.social-posts.update', $post) }}" class="row g-2" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-md-2"><input type="text" name="platform" class="form-control form-control-sm" value="{{ $post->platform }}" required></div>
                        <div class="col-md-4"><input type="text" name="post_url" class="form-control form-control-sm" value="{{ $post->post_url }}" required></div>
                        <div class="col-md-3"><input type="text" name="thumbnail_url" class="form-control form-control-sm" value="{{ $post->thumbnail_url }}"></div>
                        <div class="col-md-1"><input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $post->sort_order }}" min="0" max="999"></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $post->is_active ? 'checked' : '' }}> Actif</label></div>
                        <div class="col-md-6"><input type="file" name="thumbnail_image" class="form-control form-control-sm" accept="image/*"></div>
                        <div class="col-md-6 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="remove_thumbnail_image" value="1"> Supprimer image locale</label></div>
                        @if($post->thumbnail_path)
                            <div class="col-12"><img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="Miniature {{ $post->platform }}" class="img-fluid rounded border" style="max-height:180px;object-fit:cover;"></div>
                        @endif
                        <div class="col-12"><textarea name="caption" rows="2" class="form-control form-control-sm">{{ $post->caption }}</textarea></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Maj</button></div>
                    </form>
                    <div class="d-flex gap-2 mt-2">
                        <form method="POST" action="{{ route('admin.vitrine.social-posts.destroy', $post) }}" onsubmit="return confirm('Supprimer cette publication ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                        </form>
                        <a href="{{ $post->post_url }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">Voir</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
