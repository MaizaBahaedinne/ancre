<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\UpdateParentRequest;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    private const CIN_SCAN_PREFIX = 'parents/cin-scans';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');

        $baseQuery = ParentModel::query()
            ->with('user')
            ->when($search, function ($query, $searchValue) {
                $query->where('nom', 'like', "%{$searchValue}%")
                    ->orWhere('prenom', 'like', "%{$searchValue}%")
                    ->orWhere('telephone', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });

        $statsQuery = clone $baseQuery;

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'with_user' => (clone $statsQuery)->whereNotNull('user_id')->count(),
            'without_user' => (clone $statsQuery)->whereNull('user_id')->count(),
            'with_email' => (clone $statsQuery)->whereNotNull('email')->where('email', '!=', '')->count(),
        ];

        $parents = $baseQuery
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('parents.index', compact('parents', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $scanToken = Str::random(40);

        return view('parents.create', compact('scanToken'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $scanToken = $request->string('cin_scan_token')->trim()->toString();

        if ($request->hasFile('cin_recto')) {
            $data['cin_recto'] = $request->file('cin_recto')->store('parents/cin', 'public');
        } elseif ($scanToken) {
            $data['cin_recto'] = $this->resolveScannedDocument($scanToken, 'cin_recto');
        }

        if ($request->hasFile('cin_verso')) {
            $data['cin_verso'] = $request->file('cin_verso')->store('parents/cin', 'public');
        } elseif ($scanToken) {
            $data['cin_verso'] = $this->resolveScannedDocument($scanToken, 'cin_verso');
        }

        if (empty($data['cin_recto']) || empty($data['cin_verso'])) {
            return back()->withErrors([
                'cin_recto' => 'Veuillez fournir le recto et le verso de la CIN, soit par upload direct, soit via le scan smartphone.',
            ])->withInput();
        }

        $data['verification_status'] = 'pending';

        $parent = ParentModel::create($data);

        if ($scanToken) {
            $this->cleanupScanDirectory($scanToken);
        }

        return redirect()
            ->route('parents.show', $parent)
            ->with('success', 'Parent ajoute avec succes. Une verification de compte est maintenant disponible.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentModel $parent): View
    {
        $parent->load(['user.roles'])->loadCount('enfants');

        $linkedEnfants = Enfant::query()
            ->with([
                'familyRelations' => fn ($query) => $query
                    ->where('parent_id', $parent->id),
            ])
            ->where('parent_id', $parent->id)
            ->orWhereHas('familyRelations', fn ($query) => $query->where('parent_id', $parent->id))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('parents.show', [
            'parent' => $parent,
            'linkedEnfants' => $linkedEnfants,
            'verificationUrl' => route('parents.verification', $parent),
        ]);
    }

    public function verification(ParentModel $parent): View
    {
        return view('parents.verification', [
            'parent' => $parent,
        ]);
    }

    public function submitVerification(Request $request, ParentModel $parent): RedirectResponse
    {
        $validated = $request->validate([
            'verification_signature' => ['required', 'string', 'max:255'],
            'terms_accepted' => ['accepted'],
            'identity_documents' => ['required', 'array', 'min:2'],
            'identity_documents.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        foreach ($request->file('identity_documents', []) as $file) {
            $file->store('parents/verifications/' . $parent->id, 'public');
        }

        $parent->update([
            'verification_status' => 'verified',
            'verification_submitted_at' => now(),
            'verified_at' => now(),
            'verification_signature' => $validated['verification_signature'],
            'verification_terms_accepted_at' => now(),
        ]);

        return redirect()
            ->route('parents.show', $parent)
            ->with('success', 'Verification de compte parent enregistree avec succes.');
    }

    public function createUser(ParentModel $parent, Request $request): RedirectResponse
    {
        if ($parent->user_id) {
            return redirect()->route('parents.show', $parent)->with('error', 'Ce parent a deja un compte utilisateur associe.');
        }

        if (empty($parent->email)) {
            return redirect()->route('parents.show', $parent)->with('error', 'Impossible de creer un compte utilisateur sans email pour ce parent.');
        }

        $temporaryPassword = null;
        $user = User::query()->where('email', $parent->email)->first();

        if (! $user) {
            $temporaryPassword = Str::password(10);

            $user = User::create([
                'name' => trim($parent->nom.' '.$parent->prenom),
                'email' => $parent->email,
                'password' => Hash::make($temporaryPassword),
            ]);
        }

        Role::firstOrCreate([
            'name' => 'Parent',
            'guard_name' => 'web',
        ]);

        $user->assignRole('Parent');

        $parent->update([
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('parents.show', $parent)
            ->with('success', 'Compte utilisateur associe au parent avec succes.')
            ->with('temporary_password', $temporaryPassword);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentModel $parent): View
    {
        return view('parents.edit', compact('parent'));
    }

    public function cinScanner(string $token): View
    {
        return view('parents.cin-scanner', [
            'scanToken' => $token,
            'uploadUrl' => route('parents.cin-scanner.store', $token),
            'statusUrl' => route('parents.cin-scanner.status', $token),
        ]);
    }

    public function storeCinScan(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'side' => ['required', 'in:cin_recto,cin_verso'],
            'cin_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $path = $this->storeScannedDocument($token, $validated['side'], $request->file('cin_file'));

        return response()->json([
            'success' => true,
            'side' => $validated['side'],
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function cinScanStatus(string $token): JsonResponse
    {
        $rectoPath = $this->scannedDocumentPath($token, 'cin_recto');
        $versoPath = $this->scannedDocumentPath($token, 'cin_verso');

        return response()->json([
            'token' => $token,
            'recto' => $rectoPath ? [
                'ready' => true,
                'path' => $rectoPath,
                'url' => Storage::disk('public')->url($rectoPath),
            ] : null,
            'verso' => $versoPath ? [
                'ready' => true,
                'path' => $versoPath,
                'url' => Storage::disk('public')->url($versoPath),
            ] : null,
            'completed' => $rectoPath && $versoPath,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParentRequest $request, ParentModel $parent): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cin_recto')) {
            if ($parent->cin_recto && Storage::disk('public')->exists($parent->cin_recto)) {
                Storage::disk('public')->delete($parent->cin_recto);
            }

            $data['cin_recto'] = $request->file('cin_recto')->store('parents/cin', 'public');
        }

        if ($request->hasFile('cin_verso')) {
            if ($parent->cin_verso && Storage::disk('public')->exists($parent->cin_verso)) {
                Storage::disk('public')->delete($parent->cin_verso);
            }

            $data['cin_verso'] = $request->file('cin_verso')->store('parents/cin', 'public');
        }

        $parent->update($data);

        return redirect()
            ->route('parents.index')
            ->with('success', 'Parent mis a jour avec succes.');
    }

    private function scannedDocumentPath(string $token, string $side): ?string
    {
        $directory = $this->scanDirectory($token);
        $files = Storage::disk('public')->files($directory);

        foreach ($files as $file) {
            if (Str::startsWith(basename($file), $side)) {
                return $file;
            }
        }

        return null;
    }

    private function resolveScannedDocument(string $token, string $side): ?string
    {
        $path = $this->scannedDocumentPath($token, $side);

        if (! $path) {
            return null;
        }

        $finalPath = 'parents/cin/'.basename($path);

        if (Storage::disk('public')->exists($finalPath)) {
            Storage::disk('public')->delete($finalPath);
        }

        Storage::disk('public')->move($path, $finalPath);

        return $finalPath;
    }

    private function storeScannedDocument(string $token, string $side, UploadedFile $file): string
    {
        $directory = $this->scanDirectory($token);
        Storage::disk('public')->makeDirectory($directory);

        $extension = $file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $side.'.'.strtolower($extension);
        $path = $directory.'/'.$filename;

        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return $path;
    }

    private function scanDirectory(string $token): string
    {
        return self::CIN_SCAN_PREFIX.'/'.$token;
    }

    private function cleanupScanDirectory(string $token): void
    {
        $directory = $this->scanDirectory($token);

        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentModel $parent): RedirectResponse
    {
        if ($parent->cin_recto && Storage::disk('public')->exists($parent->cin_recto)) {
            Storage::disk('public')->delete($parent->cin_recto);
        }

        if ($parent->cin_verso && Storage::disk('public')->exists($parent->cin_verso)) {
            Storage::disk('public')->delete($parent->cin_verso);
        }

        $parent->delete();

        return redirect()
            ->route('parents.index')
            ->with('success', 'Parent supprime avec succes.');
    }
}
