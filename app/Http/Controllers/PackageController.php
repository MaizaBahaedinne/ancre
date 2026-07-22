<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::query()
            ->orderByDesc('is_active')
            ->orderBy('nom')
            ->get();

        return view('packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('packages.create');
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        Package::create($request->validated());

        return redirect()
            ->route('packages.index')
            ->with('success', 'Package ajoute avec succes.');
    }

    public function show(Package $package): View
    {
        return view('packages.show', compact('package'));
    }

    public function edit(Package $package): View
    {
        return view('packages.edit', compact('package'));
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($request->validated());

        return redirect()
            ->route('packages.index')
            ->with('success', 'Package mis a jour avec succes.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        if ($package->inscriptions()->exists()) {
            return redirect()
                ->route('packages.index')
                ->with('error', 'Ce package est deja utilise par des inscriptions et ne peut pas etre supprime.');
        }

        $package->delete();

        return redirect()
            ->route('packages.index')
            ->with('success', 'Package supprime avec succes.');
    }
}