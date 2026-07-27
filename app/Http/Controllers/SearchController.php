<?php

namespace App\Http\Controllers;

use App\Models\Enfant;
use App\Models\Inscription;
use App\Models\ParentModel;
use App\Models\Presence;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) < 2) {
            return view('search.index', compact('query', 'results'));
        }

        $searchTerm = '%' . $query . '%';

        // Search parents
        if ($this->userCan('parents.view')) {
            $parents = ParentModel::where('nom', 'LIKE', $searchTerm)
                ->orWhere('prenom', 'LIKE', $searchTerm)
                ->orWhere('email', 'LIKE', $searchTerm)
                ->orWhere('telephone', 'LIKE', $searchTerm)
                ->limit(5)
                ->get()
                ->map(function ($parent) {
                    return [
                        'type' => 'parent',
                        'label' => "{$parent->nom} {$parent->prenom}",
                        'subtitle' => $parent->email,
                        'icon' => 'fa-user',
                        'url' => route('parents.show', $parent->id),
                    ];
                });
            $results = array_merge($results, $parents->toArray());
        }

        // Search children
        if ($this->userCan('children.view')) {
            $children = Enfant::where('nom', 'LIKE', $searchTerm)
                ->orWhere('prenom', 'LIKE', $searchTerm)
                ->limit(5)
                ->get()
                ->map(function ($child) {
                    return [
                        'type' => 'child',
                        'label' => "{$child->nom} {$child->prenom}",
                        'subtitle' => $child->parent?->nom ?? 'No parent',
                        'icon' => 'fa-child',
                        'url' => route('enfants.show', $child->id),
                    ];
                });
            $results = array_merge($results, $children->toArray());
        }

        // Search inscriptions
        if ($this->userCan('registrations.view')) {
            $inscriptions = Inscription::with(['enfant', 'package'])
                ->whereHas('enfant', function ($q) use ($searchTerm) {
                    $q->where('nom', 'LIKE', $searchTerm)
                        ->orWhere('prenom', 'LIKE', $searchTerm);
                })
                ->limit(5)
                ->get()
                ->map(function ($inscription) {
                    return [
                        'type' => 'inscription',
                        'label' => "Inscription: {$inscription->enfant->nom} {$inscription->enfant->prenom}",
                        'subtitle' => "{$inscription->package?->nom} - {$inscription->annee_scolaire}",
                        'icon' => 'fa-file-signature',
                        'url' => route('inscriptions.show', $inscription->id),
                    ];
                });
            $results = array_merge($results, $inscriptions->toArray());
        }

        return view('search.index', compact('query', 'results'));
    }

    private function userCan(string $permission): bool
    {
        return auth()->user()?->can($permission) ?? false;
    }
}
