<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\ParentModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentIncidentController extends Controller
{
    public function show(Request $request, Incident $incident): View
    {
        $parent = ParentModel::query()->where('user_id', $request->user()->id)->firstOrFail();

        abort_unless(
            $incident->notify_parent && (
                (int) $incident->enfant_id &&
                ($incident->enfant?->parent_id === $parent->id || $incident->enfant?->familyRelations()->where('parent_id', $parent->id)->exists())
            ),
            404
        );

        $incident->load(['enfant.parent', 'responsablePersonnel']);

        return view('parent.incidents.show', [
            'incident' => $incident,
            'parent' => $parent,
        ]);
    }
}