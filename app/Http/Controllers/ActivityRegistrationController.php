<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\ActivityRegistration;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\Personnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityRegistrationController extends Controller
{
    public function storeByParent(Request $request, Activite $activite): RedirectResponse
    {
        $parent = $this->currentParent($request);
        abort_unless($parent, 403);
        $this->ensureRegistrationWindowOpen($activite);

        $validated = $request->validate([
            'enfant_id' => ['required', 'integer', 'exists:enfants,id'],
            'is_paid' => ['nullable', 'boolean'],
            'payment_reference' => ['nullable', 'in:'.implode(',', array_keys(ActivityRegistration::PAYMENT_METHOD_OPTIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $child = $this->allowedChildrenQuery($parent)->whereKey($validated['enfant_id'])->first();
        abort_unless($child, 403);

        return $this->persistRegistration($activite, $child, $parent->id, $validated, false);
    }

    public function storeByStaff(Request $request, Activite $activite): RedirectResponse
    {
        $this->ensureStaffCanManageActivity($activite);
        $this->ensureRegistrationWindowOpen($activite);

        $validated = $request->validate([
            'enfant_id' => ['required', 'integer', 'exists:enfants,id'],
            'is_paid' => ['nullable', 'boolean'],
            'payment_reference' => ['nullable', 'in:'.implode(',', array_keys(ActivityRegistration::PAYMENT_METHOD_OPTIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $child = Enfant::query()->with('parent')->findOrFail($validated['enfant_id']);
        abort_unless($child->parent_id, 422, 'Cet enfant doit etre rattache a un parent avant inscription.');

        return $this->persistRegistration($activite, $child, (int) $child->parent_id, $validated, true);
    }

    private function persistRegistration(Activite $activite, Enfant $child, int $parentId, array $validated, bool $isStaffAction): RedirectResponse
    {
        $isPaid = (bool) ($validated['is_paid'] ?? false);
        $fee = (float) ($activite->frais_participation ?? 0);

        if ($isPaid && $fee > 0 && empty($validated['payment_reference'])) {
            return back()->withErrors([
                'payment_reference' => 'La reference de paiement est obligatoire pour valider cette inscription.',
            ])->withInput();
        }

        $existing = ActivityRegistration::query()
            ->where('activite_id', $activite->id)
            ->where('enfant_id', $child->id)
            ->first();

        $status = ActivityRegistration::STATUS_PENDING_PAYMENT;

        if ($isPaid) {
            $status = $this->canValidate($activite, $existing) ? ActivityRegistration::STATUS_VALIDATED : ActivityRegistration::STATUS_WAITLIST;
        }

        ActivityRegistration::updateOrCreate(
            [
                'activite_id' => $activite->id,
                'enfant_id' => $child->id,
            ],
            [
                'parent_id' => $parentId,
                'status' => $status,
                'amount_due' => $fee,
                'amount_paid' => $isPaid ? $fee : 0,
                'paid_at' => $isPaid ? now() : null,
                'payment_reference' => $validated['payment_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $message = match ($status) {
            ActivityRegistration::STATUS_VALIDATED => $isStaffAction
                ? 'Participant ajoute et inscription validee.'
                : 'Inscription validee. Votre enfant est inscrit a cette activite.',
            ActivityRegistration::STATUS_WAITLIST => $isStaffAction
                ? 'Participant ajoute en liste d\'attente car la capacite est atteinte.'
                : 'Capacite atteinte. Votre enfant est place en liste d\'attente.',
            default => $isStaffAction
                ? 'Participant ajoute en attente de paiement.'
                : 'Inscription enregistree en attente de paiement.',
        };

        return back()->with('success', $message);
    }

    public function markParticipation(Request $request, Activite $activite, ActivityRegistration $registration): RedirectResponse
    {
        abort_unless((int) $registration->activite_id === (int) $activite->id, 404);
        $this->ensureParticipationWindowOpen($activite);

        $validated = $request->validate([
            'participation_status' => ['nullable', 'in:present,absent'],
        ]);

        if ($registration->status !== ActivityRegistration::STATUS_VALIDATED) {
            return back()->with('error', 'Participation non modifiable tant que l\'inscription n\'est pas validee.');
        }

        $registration->update([
            'participation_status' => $validated['participation_status'] ?? null,
        ]);

        return back()->with('success', 'Participation mise a jour.');
    }

    public function markParticipationBatch(Request $request, Activite $activite): RedirectResponse
    {
        $this->ensureStaffCanManageActivity($activite);
        $this->ensureParticipationWindowOpen($activite);

        $validated = $request->validate([
            'participation_status' => ['nullable', 'in:present,absent'],
            'participation_statuses' => ['nullable', 'array'],
            'participation_statuses.*' => ['nullable', 'in:present,absent'],
        ]);

        if (! empty($validated['participation_status'])) {
            $updated = ActivityRegistration::query()
                ->where('activite_id', $activite->id)
                ->where('status', ActivityRegistration::STATUS_VALIDATED)
                ->update([
                    'participation_status' => $validated['participation_status'],
                ]);

            if ($updated === 0) {
                return back()->with('error', 'Aucune inscription validee a mettre a jour.');
            }

            return back()->with('success', 'Participation groupee mise a jour pour '.$updated.' participant(s).');
        }

        $statusMap = $validated['participation_statuses'] ?? [];

        if (empty($statusMap)) {
            return back()->with('error', 'Aucune mise a jour de presence n\'a ete soumise.');
        }

        $registrations = ActivityRegistration::query()
            ->where('activite_id', $activite->id)
            ->where('status', ActivityRegistration::STATUS_VALIDATED)
            ->whereIn('id', array_map('intval', array_keys($statusMap)))
            ->get();

        if ($registrations->isEmpty()) {
            return back()->with('error', 'Aucune inscription validee correspondante a mettre a jour.');
        }

        foreach ($registrations as $registration) {
            $registration->update([
                'participation_status' => $statusMap[$registration->id] ?: null,
            ]);
        }

        return back()->with('success', 'Toute la liste de presence a ete mise a jour.');
    }

    private function canValidate(Activite $activite, ?ActivityRegistration $existing): bool
    {
        if (! $activite->capacite || $activite->capacite <= 0) {
            return true;
        }

        $validatedCount = ActivityRegistration::query()
            ->where('activite_id', $activite->id)
            ->where('status', ActivityRegistration::STATUS_VALIDATED)
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->count();

        return $validatedCount < (int) $activite->capacite;
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
        return Enfant::query()
            ->where(function ($query) use ($parent) {
                $query->where('parent_id', $parent->id)
                    ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                        $relationScope->where('parent_id', $parent->id);
                    });
            });
    }

    private function ensureStaffCanManageActivity(Activite $activite): void
    {
        if (! $this->isRestrictedEducator()) {
            return;
        }

        $educatorPersonnel = $this->currentEducatorPersonnel();

        abort_unless(
            $educatorPersonnel && (int) $activite->responsable_personnel_id === (int) $educatorPersonnel->id,
            403
        );
    }

    private function currentEducatorPersonnel(): ?Personnel
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return Personnel::query()
            ->where('user_id', $user->id)
            ->first();
    }

    private function isRestrictedEducator(): bool
    {
        return (bool) auth()->user()?->hasRole('Educateur');
    }

    private function ensureRegistrationWindowOpen(Activite $activite): void
    {
        $endAt = $activite->endsAt();

        abort_if($endAt && now()->gte($endAt), 422, 'L\'ajout de participants est ferme depuis l\'heure de fin de l\'activite.');
    }

    private function ensureParticipationWindowOpen(Activite $activite): void
    {
        $cutoffAt = $activite->participationCutoffAt();

        abort_if($cutoffAt && now()->gt($cutoffAt), 422, 'La saisie de presence est fermee depuis plus d\'une heure apres la fin de l\'activite.');
    }
}
