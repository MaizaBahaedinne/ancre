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

        $validated['is_enabled'] = $request->boolean('is_enabled');

        $notificationWorkflow->update($validated);

        return redirect()->route('admin.notifications.workflows.show', $notificationWorkflow)
            ->with('success', 'Workflow mis à jour avec succès');
    }

    public function addReceiver(Request $request, NotificationWorkflow $notificationWorkflow)
    {
        $validated = $request->validate([
            'receiver_type' => 'required|in:role,user,default',
            'receiver_value' => 'nullable|string|required_if:receiver_type,role,user',
            'notification_medium' => 'required|in:system,email,sms,all',
        ]);

        $notificationWorkflow->receivers()->create($validated + ['is_enabled' => true]);

        return redirect()->route('admin.notifications.workflows.show', $notificationWorkflow)
            ->with('success', 'Receiver ajouté avec succès');
    }

    public function removeReceiver(NotificationReceiver $notificationReceiver)
    {
        $workflowId = $notificationReceiver->workflow_id;
        $notificationReceiver->delete();

        return redirect()->route('admin.notifications.workflows.show', $workflowId)
            ->with('success', 'Receiver supprimé avec succès');
    }

    public function toggleReceiver(NotificationReceiver $notificationReceiver)
    {
        $notificationReceiver->update(['is_enabled' => !$notificationReceiver->is_enabled]);

        return redirect()->route('admin.notifications.workflows.show', $notificationReceiver->workflow_id)
            ->with('success', 'Receiver mis à jour');
    }
}
