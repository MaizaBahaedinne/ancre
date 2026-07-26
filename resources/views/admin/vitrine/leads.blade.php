@extends('adminlte::page')

@section('title', 'Vitrine - Leads')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card mb-4">
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

    <div class="card">
        <div class="card-header"><strong>Demandes de visite</strong></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped js-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parent</th>
                            <th>Telephone</th>
                            <th>Email</th>
                            <th>Age enfant</th>
                            <th>Message</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitRequests as $visit)
                            <tr>
                                <td>{{ $visit->id }}</td>
                                <td>{{ $visit->full_name }}</td>
                                <td>{{ $visit->phone }}</td>
                                <td>{{ $visit->email ?: '-' }}</td>
                                <td>{{ $visit->child_age_group ?: '-' }}</td>
                                <td>{{ $visit->message ?: '-' }}</td>
                                <td>{{ $visit->status }}</td>
                                <td>{{ optional($visit->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
