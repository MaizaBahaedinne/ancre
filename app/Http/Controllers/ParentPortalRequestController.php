<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunicationMessageRequest;
use App\Http\Requests\StoreCommunicationRequest;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\ParentRequest;
use App\Models\ParentRequestSubject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

class ParentPortalRequestController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);

        $requests = ParentRequest::query()
            ->with(['enfant', 'subject'])
            ->where('parent_id', $parent->id)
            ->orderByDesc('created_at')
            ->get();

        return view('parent.requests.index', compact('parent', 'requests'));
    }

    public function create(Request $request): View
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);

        $children = $this->allowedChildrenQuery($parent)
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();

        $subjects = ParentRequestSubject::query()
            ->where('is_active', true)
            ->orderBy('action_type')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('parent.requests.create', compact('parent', 'children', 'subjects'));
    }

    public function store(StoreCommunicationRequest $request): RedirectResponse
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);

        $validated = $request->validated();

        $allowedChildIds = $this->allowedChildrenQuery($parent)->pluck('id')->all();
        abort_unless(in_array((int) $validated['enfant_id'], $allowedChildIds, true), 403);

        $subject = ! empty($validated['subject_id'])
            ? ParentRequestSubject::query()->find($validated['subject_id'])
            : null;

        $communicationRequest = ParentRequest::create([
            'parent_id' => $parent->id,
            'enfant_id' => $validated['enfant_id'],
            'action_type' => $validated['action_type'],
            'subject_id' => $subject?->id,
            'subject_snapshot' => $subject?->label,
            'subject_other' => $validated['subject_other'] ?? null,
            'description' => $validated['description'],
            'attachments' => $this->storeAttachments($request->file('attachments', [])),
            'workflow_status' => ParentRequest::STATUS_CREATED,
            'opened_at' => now(),
        ]);

        return redirect()
            ->route('parent.demandes.show', $communicationRequest)
            ->with('success', 'Votre demande/reclamation a ete envoyee.');
    }

    public function show(Request $request, ParentRequest $parentRequest): View
    {
        $parent = $this->currentParent($request);
        abort_unless($parent && (int) $parentRequest->parent_id === (int) $parent->id, 403);

        $parentRequest->load([
            'enfant',
            'subject',
            'messages.sender',
            'handledBy',
        ]);

        return view('parent.requests.show', [
            'parent' => $parent,
            'communicationRequest' => $parentRequest,
        ]);
    }

    public function storeMessage(StoreCommunicationMessageRequest $request, ParentRequest $parentRequest): RedirectResponse
    {
        $parent = $this->currentParent($request);
        abort_unless($parent && (int) $parentRequest->parent_id === (int) $parent->id, 403);

        $validated = $request->validated();

        $parentRequest->messages()->create([
            'sender_user_id' => $request->user()->id,
            'message' => $validated['message'],
            'attachments' => $this->storeAttachments($request->file('attachments', [])),
        ]);

        return back()->with('success', 'Message envoye a l\'administration.');
    }

    private function currentParent(Request $request): ?ParentModel
    {
        return ParentModel::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }

    private function allowedChildrenQuery(ParentModel $parent)
    {
        return Enfant::query()
            ->where(function ($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                    ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                        $relationScope->where('parent_id', $parent->id);
                    });
            });
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
                'path' => $file->store('parent-requests', 'public'),
                'mime' => $file->getClientMimeType() ?? 'application/octet-stream',
            ];
        }

        return $attachments;
    }
}