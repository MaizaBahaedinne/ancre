<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunicationMessageRequest;
use App\Http\Requests\UpdateCommunicationWorkflowRequest;
use App\Models\ParentRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class RequestManagementController extends Controller
{
    public function index(): View
    {
        $workflowStatus = request('workflow_status');
        $actionType = request('action_type');

        $requests = ParentRequest::query()
            ->with(['parent', 'enfant', 'subject'])
            ->when($workflowStatus, fn ($query, $value) => $query->where('workflow_status', $value))
            ->when($actionType, fn ($query, $value) => $query->where('action_type', $value))
            ->orderByDesc('created_at')
            ->get();

        return view('requests.index', compact('requests', 'workflowStatus', 'actionType'));
    }

    public function show(ParentRequest $parentRequest): View
    {
        $parentRequest->load([
            'parent',
            'enfant',
            'subject',
            'handledBy',
            'messages.sender',
        ]);

        return view('requests.show', compact('parentRequest'));
    }

    public function updateWorkflow(UpdateCommunicationWorkflowRequest $request, ParentRequest $parentRequest): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'workflow_status' => $validated['workflow_status'],
            'resolution_note' => $validated['resolution_note'] ?? null,
            'handled_by_user_id' => $request->user()->id,
        ];

        if ($validated['workflow_status'] === ParentRequest::STATUS_ACKNOWLEDGED && ! $parentRequest->acknowledged_at) {
            $data['acknowledged_at'] = now();
        }

        if ($validated['workflow_status'] === ParentRequest::STATUS_IN_PROGRESS && ! $parentRequest->in_progress_at) {
            $data['in_progress_at'] = now();
            $data['acknowledged_at'] = $parentRequest->acknowledged_at ?: now();
        }

        if (in_array($validated['workflow_status'], [ParentRequest::STATUS_PROCESSED, ParentRequest::STATUS_REJECTED], true)) {
            $data['resolved_at'] = now();
            $data['acknowledged_at'] = $parentRequest->acknowledged_at ?: now();
            $data['in_progress_at'] = $parentRequest->in_progress_at ?: now();
        }

        $parentRequest->update($data);

        return back()->with('success', 'Workflow mis a jour avec succes.');
    }

    public function storeMessage(StoreCommunicationMessageRequest $request, ParentRequest $parentRequest): RedirectResponse
    {
        $validated = $request->validated();

        $parentRequest->messages()->create([
            'sender_user_id' => $request->user()->id,
            'message' => $validated['message'],
            'attachments' => $this->storeAttachments($request->file('attachments', [])),
        ]);

        if ($parentRequest->workflow_status === ParentRequest::STATUS_CREATED) {
            $parentRequest->update([
                'workflow_status' => ParentRequest::STATUS_ACKNOWLEDGED,
                'acknowledged_at' => $parentRequest->acknowledged_at ?: now(),
                'handled_by_user_id' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'Message envoye au parent.');
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
                'path' => $file->store('parent-requests/messages', 'public'),
                'mime' => $file->getClientMimeType() ?? 'application/octet-stream',
            ];
        }

        return $attachments;
    }
}