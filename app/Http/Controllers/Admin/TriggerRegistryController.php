<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTriggerOverride;
use App\Support\NotificationWorkflowSynchronizer;
use App\Support\TriggerRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TriggerRegistryController extends Controller
{
    public function index(TriggerRegistry $registry): View
    {
        $definitions = collect($registry->definitions())->sortBy('trigger')->values();
        $overrides = NotificationTriggerOverride::query()->get()->keyBy('trigger');

        return view('admin.notifications.registry.index', compact('definitions', 'overrides'));
    }

    public function edit(string $trigger, TriggerRegistry $registry): View
    {
        $definition = collect($registry->definitions())->firstWhere('trigger', $trigger);
        abort_if(!$definition, 404);

        $override = NotificationTriggerOverride::query()->where('trigger', $trigger)->first();

        return view('admin.notifications.registry.edit', compact('definition', 'override'));
    }

    public function update(Request $request, string $trigger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
            'enabled_mode' => 'required|in:inherit,enabled,disabled',
            'receivers_json' => 'nullable|json',
        ]);

        $receivers = [];
        if (!empty($validated['receivers_json'])) {
            $decoded = json_decode($validated['receivers_json'], true);
            $receivers = is_array($decoded) ? $decoded : [];
        }

        $isEnabled = match ($validated['enabled_mode']) {
            'enabled' => true,
            'disabled' => false,
            default => null,
        };

        NotificationTriggerOverride::query()->updateOrCreate(
            ['trigger' => $trigger],
            [
                'name' => $validated['name'] ?: null,
                'description' => $validated['description'] ?: null,
                'module' => $validated['module'] ?: null,
                'is_enabled' => $isEnabled,
                'receivers' => empty($receivers) ? null : $receivers,
            ]
        );

        return redirect()->route('admin.notifications.registry.index')
            ->with('success', 'Override TriggerRegistry mis a jour.');
    }

    public function destroy(string $trigger): RedirectResponse
    {
        NotificationTriggerOverride::query()->where('trigger', $trigger)->delete();

        return redirect()->route('admin.notifications.registry.index')
            ->with('success', 'Override supprime.');
    }

    public function sync(NotificationWorkflowSynchronizer $synchronizer): RedirectResponse
    {
        $result = $synchronizer->sync();

        return redirect()->route('admin.notifications.registry.index')
            ->with('success', 'Sync terminee: '.$result['workflows'].' workflows, '.$result['receivers'].' receivers.');
    }
}
