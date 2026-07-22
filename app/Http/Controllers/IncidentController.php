<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Enfant;
use App\Models\Incident;
use App\Models\ParentModel;
use App\Models\Personnel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IncidentController extends Controller
{
    private const INCIDENT_TYPES = [
        'Blessure legere',
        'Chute',
        'Conflit entre enfants',
        'Probleme de sante',
        'Allergie',
        'Accident de jeu',
        'Incident alimentaire',
        'Comportement inhabituel',
        'Autre',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $parent = $this->currentParent();
        $parentChildIds = $this->isParentUser()
            ? ($parent?->enfants()->pluck('id')->all() ?? [])
            : [];
        $enfantId = request('enfant_id');
        $scope = request('scope', 'open');

        $baseQuery = Incident::query()
            ->with(['enfant', 'responsablePersonnel'])
            ->when($this->isParentUser(), function ($query) use ($parentChildIds) {
                if (empty($parentChildIds)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('enfant_id', $parentChildIds);
            })
            ->when($enfantId, fn ($query, $value) => $query->where('enfant_id', $value));

        $statsQuery = clone $baseQuery;

        $stats = [
            'active' => (clone $statsQuery)->where('workflow_status', '!=', Incident::WORKFLOW_CLOSED)->count(),
            'in_progress' => (clone $statsQuery)->where('workflow_status', Incident::WORKFLOW_IN_PROGRESS)->count(),
            'waiting' => (clone $statsQuery)->where('workflow_status', Incident::WORKFLOW_WAITING)->count(),
            'closed' => (clone $statsQuery)->where('workflow_status', Incident::WORKFLOW_CLOSED)->count(),
        ];

        $incidents = $baseQuery
            ->when($scope === 'closed', fn ($query) => $query->where('workflow_status', Incident::WORKFLOW_CLOSED))
            ->when($scope !== 'closed', fn ($query) => $query->where('workflow_status', '!=', Incident::WORKFLOW_CLOSED))
            ->orderByDesc('date')
            ->orderByDesc('opened_at')
            ->get();

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('incidents.index', compact('incidents', 'enfants', 'enfantId', 'scope', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $personnels = Personnel::orderBy('nom')->orderBy('prenom')->get();

        return view('incidents.create', [
            'enfants' => $enfants,
            'personnels' => $personnels,
            'incidentTypes' => self::INCIDENT_TYPES,
            'workflowOptions' => Incident::WORKFLOW_OPTIONS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($data['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $data['attachments'] = $this->storeAttachments($request->file('attachments', []));
        $data = $this->applyWorkflowTimestamps($data);

        Incident::create($data);

        return redirect()->route('incidents.index')->with('success', 'Incident enregistre avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident): View
    {
        $this->ensureParentCanAccessIncident($incident);

        $incident->load(['enfant.parent', 'responsablePersonnel']);

        $personnels = Personnel::orderBy('nom')->orderBy('prenom')->get();

        return view('incidents.show', compact('incident', 'personnels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident): View
    {
        $this->ensureParentCanAccessIncident($incident);

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $personnels = Personnel::orderBy('nom')->orderBy('prenom')->get();

        return view('incidents.edit', [
            'incident' => $incident,
            'enfants' => $enfants,
            'personnels' => $personnels,
            'incidentTypes' => self::INCIDENT_TYPES,
            'workflowOptions' => Incident::WORKFLOW_OPTIONS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $this->ensureParentCanAccessIncident($incident);

        $data = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($data['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        if ($request->hasFile('attachments')) {
            $data['attachments'] = array_merge(
                $incident->attachments ?? [],
                $this->storeAttachments($request->file('attachments', [])),
            );
        }

        $data = $this->applyWorkflowTimestamps($data, $incident);

        $incident->update($data);

        return redirect()->route('incidents.index')->with('success', 'Incident mis a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident): RedirectResponse
    {
        $this->ensureParentCanAccessIncident($incident);

        foreach ($incident->attachments ?? [] as $attachment) {
            Storage::disk('public')->delete($attachment['path']);
        }

        $incident->delete();

        return redirect()->route('incidents.index')->with('success', 'Incident supprime avec succes.');
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array<int, array{name: string, path: string, mime: string}>
     */
    private function storeAttachments(array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $file->store('incidents', 'public'),
                'mime' => $file->getClientMimeType() ?? 'application/octet-stream',
            ];
        }

        return $attachments;
    }

    private function applyWorkflowTimestamps(array $data, ?Incident $incident = null): array
    {
        $now = now();
        $currentStatus = $incident?->workflow_status;
        $newStatus = $data['workflow_status'] ?? Incident::WORKFLOW_OPEN;

        $data['opened_at'] = $incident?->opened_at ?? ($data['opened_at'] ?? $now);

        if ($newStatus === Incident::WORKFLOW_TAKEN && empty($data['taken_at'])) {
            $data['taken_at'] = $incident?->taken_at ?? $now;
        }

        if ($newStatus === Incident::WORKFLOW_IN_PROGRESS && empty($data['taken_at'])) {
            $data['taken_at'] = $incident?->taken_at ?? $now;
        }

        if ($newStatus === Incident::WORKFLOW_CLOSED) {
            $data['closed_at'] = $now;
            $data['resolved_at'] = $incident?->resolved_at ?? $now;
            $data['taken_at'] = $incident?->taken_at ?? $data['taken_at'] ?? $now;
        }

        if ($newStatus === Incident::WORKFLOW_WAITING && empty($data['taken_at']) && in_array($currentStatus, [null, Incident::WORKFLOW_OPEN], true)) {
            $data['taken_at'] = $incident?->taken_at ?? $now;
        }

        return $data;
    }

    private function isParentUser(): bool
    {
        return (bool) auth()->user()?->hasRole('Parent');
    }

    private function currentParent(): ?ParentModel
    {
        $userId = auth()->id();

        if (! $userId) {
            return null;
        }

        return ParentModel::query()->where('user_id', $userId)->first();
    }

    private function allowedChildrenQuery()
    {
        $query = Enfant::query();

        if (! $this->isParentUser()) {
            return $query;
        }

        $parent = $this->currentParent();

        if (! $parent) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($scope) use ($parent) {
            $scope->where('parent_id', $parent->id)
                ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                    $relationScope->where('parent_id', $parent->id);
                });
        });
    }

    private function ensureParentCanAccessIncident(Incident $incident): void
    {
        if (! $this->isParentUser()) {
            return;
        }

        $canAccess = $this->allowedChildrenQuery()
            ->whereKey($incident->enfant_id)
            ->exists();

        abort_unless($canAccess, 403);
    }
}
