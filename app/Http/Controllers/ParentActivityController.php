<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\ActivityRegistration;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentActivityController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);

        $childIds = $this->allowedChildrenQuery($parent)->pluck('id')->all();

        $activites = Activite::query()
            ->with(['salle'])
            ->withCount([
                'registrations as validated_registrations_count' => fn ($query) => $query->where('status', ActivityRegistration::STATUS_VALIDATED),
            ])
            ->whereDate('date', '>=', Carbon::today()->subDays(1))
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->orderBy('heure')
            ->get();

        $myRegistrationsByActivity = ActivityRegistration::query()
            ->whereIn('enfant_id', $childIds)
            ->get()
            ->groupBy('activite_id');

        return view('parent.activities.index', [
            'parent' => $parent,
            'activites' => $activites,
            'myRegistrationsByActivity' => $myRegistrationsByActivity,
        ]);
    }

    public function show(Request $request, Activite $activite): View
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);

        $activite->load('salle');
        $activityEndAt = $activite->endsAt();
        $canAddParticipants = ! $activityEndAt || now()->lt($activityEndAt);

        $children = $this->allowedChildrenQuery($parent)
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();

        $registrations = ActivityRegistration::query()
            ->where('activite_id', $activite->id)
            ->whereIn('enfant_id', $children->pluck('id')->all())
            ->with('enfant')
            ->get()
            ->keyBy('enfant_id');
        $registrableChildren = $children->reject(
            fn ($child) => $registrations->has($child->id)
        )->values();

        $validatedCount = ActivityRegistration::query()
            ->where('activite_id', $activite->id)
            ->where('status', ActivityRegistration::STATUS_VALIDATED)
            ->count();

        return view('parent.activities.show', [
            'parent' => $parent,
            'activite' => $activite,
            'children' => $children,
            'registrableChildren' => $registrableChildren,
            'registrations' => $registrations,
            'validatedCount' => $validatedCount,
            'canAddParticipants' => $canAddParticipants,
            'activityEndAt' => $activityEndAt,
            'paymentMethodOptions' => ActivityRegistration::PAYMENT_METHOD_OPTIONS,
            'statusOptions' => ActivityRegistration::STATUS_OPTIONS,
        ]);
    }

    private function currentParent(Request $request): ?ParentModel
    {
        return ParentModel::query()
            ->where('user_id', $request->user()->id)
            ->with('enfants')
            ->first();
    }

    private function allowedChildrenQuery(ParentModel $parent)
    {
        return \App\Models\Enfant::query()
            ->where(function ($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                    ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                        $relationScope->where('parent_id', $parent->id);
                    });
            });
    }
}
