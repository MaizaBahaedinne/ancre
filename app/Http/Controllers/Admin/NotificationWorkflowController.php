<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationWorkflow;
use App\Models\NotificationReceiver;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class NotificationWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:notifications.manage');
    }

    public function index()
    {
        $workflows = NotificationWorkflow::with('receivers')->orderBy('created_at', 'desc')->get();
        return view('admin.notifications.workflows.index', compact('workflows'));
    }

    public function show(NotificationWorkflow $notificationWorkflow)
    {
        $notificationWorkflow->load('receivers');
        $roles = Role::all();
        $users = User::all();
        return view('admin.notifications.workflows.show', compact('notificationWorkflow', 'roles', 'users'));
    }

    public function edit(NotificationWorkflow $notificationWorkflow)
    {
        $notificationWorkflow->load('receivers');
        $roles = Role::all();
        $users = User::all();
        return view('admin.notifications.workflows.edit', compact('notificationWorkflow', 'roles', 'users'));
    }

    public function update(Request $request, NotificationWorkflow $notificationWorkflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_enabled' => 'boolean',
        ]);

        $notificationWorkflow->update($validated);

        return redirect()->route('admin.notifications.workflows.show', $notificationWorkflow)
            ->with('success', 'Workflow mis à jour avec succès');
    }

    public function addReceiver(Request $request, NotificationWorkflow $notificationWorkflow)
    {
        $validated = $request->validate([
            'receiver_type' => 'required|in:role,user,default',
            'receiver_value' => 'nullable|string',
            'notification_medium' => 'required|in:system,email,sms,all',
        ]);

        $notificationWorkflow->receivers()->create($validated + ['is_enabled' => true]);

        return redirect()->route('admin.notifications.workflows.show', $notificationWorkflow)
            ->with('success', 'Receiver ajouté avec succès');
    }

    public function removeReceiver(NotificationReceiver $receiver)
    {
        $workflowId = $receiver->workflow_id;
        $receiver->delete();

        return redirect()->route('admin.notifications.workflows.show', $workflowId)
            ->with('success', 'Receiver supprimé avec succès');
    }

    public function toggleReceiver(NotificationReceiver $receiver)
    {
        $receiver->update(['is_enabled' => !$receiver->is_enabled]);

        return redirect()->route('admin.notifications.workflows.show', $receiver->workflow_id)
            ->with('success', 'Receiver mis à jour');
    }
}
