@extends('adminlte::page')

@section('title', 'Detail annee scolaire')
@section('content_header')<h1 class="m-0">Detail annee scolaire</h1>@stop

@section('content')
<div class="card mb-4"><div class="card-body">
<dl class="row mb-0">
<dt class="col-sm-4">Libelle</dt><dd class="col-sm-8">{{ $academicYear->label }}</dd>
<dt class="col-sm-4">Debut</dt><dd class="col-sm-8">{{ optional($academicYear->start_date)->format('d/m/Y') }}</dd>
<dt class="col-sm-4">Fin</dt><dd class="col-sm-8">{{ optional($academicYear->end_date)->format('d/m/Y') }}</dd>
<dt class="col-sm-4">Frais d'inscription annuelle</dt><dd class="col-sm-8">{{ number_format((float) $academicYear->registration_fee, 2, ',', ' ') }} TND</dd>
<dt class="col-sm-4">Statut</dt><dd class="col-sm-8">{{ $academicYear->is_active ? 'Active' : 'Archivee' }}</dd>
</dl>
</div></div>

<div class="card mb-4">
    <div class="card-header"><h3 class="card-title mb-0">Calendrier scolaire</h3></div>
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead><tr><th>Intitule</th><th>Type</th><th>Debut</th><th>Fin</th><th>Notes</th></tr></thead>
                <tbody>
                @forelse($academicYear->periods as $period)
                    <tr>
                        <td>{{ $period->title }}</td>
                        <td>{{ \App\Models\AcademicCalendarPeriod::TYPE_OPTIONS[$period->type] ?? $period->type }}</td>
                        <td>{{ optional($period->start_date)->format('d/m/Y') }}</td>
                        <td>{{ optional($period->end_date)->format('d/m/Y') }}</td>
                        <td>{{ $period->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Aucune periode definie.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card"><div class="card-header"><h3 class="card-title mb-0">Classes rattachees</h3></div><div class="card-body"><ul class="mb-0">@forelse($academicYear->schoolClasses as $schoolClass)<li>{{ $schoolClass->name }} - {{ $schoolClass->school?->name }}</li>@empty<li>Aucune classe rattachee.</li>@endforelse</ul></div></div>
@stop