@extends('adminlte::page')

@section('title', 'Vitrine - Newsletters')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong>Newsletter - Abonnes</strong>
            <a href="{{ route('admin.vitrine.newsletter.export') }}" class="btn btn-outline-primary btn-sm">Exporter CSV</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped js-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Actif</th>
                            <th>Source</th>
                            <th>Date inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newsletterSubscribers as $subscriber)
                            <tr>
                                <td>{{ $subscriber->id }}</td>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->is_active ? 'Oui' : 'Non' }}</td>
                                <td>{{ $subscriber->source_page ?: '-' }}</td>
                                <td>{{ optional($subscriber->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
