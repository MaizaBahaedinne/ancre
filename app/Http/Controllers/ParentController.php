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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    private const CIN_SCAN_PREFIX = 'parents/cin-scans';
    private const VERIFICATION_DRAFT_PREFIX = 'parents/verification-drafts';

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

        if ($this->parentColumnExists('verification_token')) {
            $data['verification_token'] = Str::random(64);
        }

        if ($this->parentColumnExists('verification_status')) {
            $data['verification_status'] = 'pending';
        }

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
            'verificationUrl' => URL::signedRoute('parents.verification', ['parent' => $parent->id]),
            'verificationStatusUrl' => URL::signedRoute('parents.verification.status', ['parent' => $parent->id]),
            'verificationCompleted' => $this->isParentVerified($parent),
        ]);
    }

    public function verification(ParentModel $parent): View
    {
        return view('parents.verification', [
            'parent' => $parent,
            'verificationUrl' => URL::signedRoute('parents.verification', ['parent' => $parent->id]),
            'verificationStatusUrl' => URL::signedRoute('parents.verification.status', ['parent' => $parent->id]),
            'verificationSubmitUrl' => URL::signedRoute('parents.verification.store', ['parent' => $parent->id]),
            'verificationDocumentUrl' => URL::signedRoute('parents.verification.document', ['parent' => $parent->id]),
            'verificationSignatureUrl' => URL::signedRoute('parents.verification.signature', ['parent' => $parent->id]),
            'verificationCompleted' => $this->isParentVerified($parent),
        ]);
    }

    public function verificationStatus(ParentModel $parent): JsonResponse
    {
        $draftRecto = $this->verificationDraftDocumentPath($parent, 'cin_recto');
        $draftVerso = $this->verificationDraftDocumentPath($parent, 'cin_verso');
        $draftSignature = $this->verificationDraftSignaturePath($parent);
        $storedSignature = $this->storedVerificationSignaturePath($parent);

        return response()->json([
            'parent_id' => $parent->id,
            'recto' => $this->verificationAssetPayload($draftRecto ?: $parent->cin_recto, $draftRecto ? 'smartphone' : ($parent->cin_recto ? 'stored' : null)),
            'verso' => $this->verificationAssetPayload($draftVerso ?: $parent->cin_verso, $draftVerso ? 'smartphone' : ($parent->cin_verso ? 'stored' : null)),
            'signature' => $this->verificationAssetPayload($draftSignature ?: $storedSignature, $draftSignature ? 'smartphone' : ($storedSignature ? 'stored' : null)),
            'verified' => $this->isParentVerified($parent),
            'user_created' => (bool) $parent->user_id,
        ]);
    }

    private function verificationAssetPayload(?string $path, ?string $source): array
    {
        $extension = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

        return [
            'ready' => (bool) $path,
            'source' => $source,
            'path' => $path,
            'url' => $path ? Storage::disk('public')->url($path) : null,
            'is_image' => $isImage,
            'extension' => $extension,
        ];
    }

    public function storeVerificationDocument(Request $request, ParentModel $parent): JsonResponse
    {
        $validated = $request->validate([
            'side' => ['required', 'in:cin_recto,cin_verso'],
            'cin_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:8192'],
        ]);

        $path = $this->storeVerificationDraftDocument($parent, $validated['side'], $request->file('cin_file'));

        return response()->json([
            'success' => true,
            'side' => $validated['side'],
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function storeVerificationSignature(Request $request, ParentModel $parent): JsonResponse
    {
        $validated = $request->validate([
            'signature_data' => ['required', 'string'],
        ]);

        $path = $this->storeVerificationDraftSignature($parent, $validated['signature_data']);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function submitVerification(Request $request, ParentModel $parent): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'terms_accepted' => ['accepted'],
        ]);

        $rectoPath = $this->finalizeVerificationDocument($parent, 'cin_recto');
        $versoPath = $this->finalizeVerificationDocument($parent, 'cin_verso');
        $signaturePath = $this->finalizeVerificationSignature($parent);

        if (! $rectoPath || ! $versoPath || ! $signaturePath) {
            return back()->withErrors([
                'verification' => 'Le recto, le verso et la signature manuscrite doivent etre completes depuis le smartphone avant validation.',
            ])->withInput();
        }

        $parent->update($this->filterParentUpdateAttributes([
            'email' => $validated['email'],
        ]));

        $temporaryPassword = null;
        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            $temporaryPassword = Str::password(10);

            $user = User::create([
                'name' => trim($parent->nom.' '.$parent->prenom),
                'email' => $validated['email'],
                'password' => Hash::make($temporaryPassword),
            ]);
        }

        Role::firstOrCreate([
            'name' => 'Parent',
            'guard_name' => 'web',
        ]);

        $user->assignRole('Parent');

        $parent->update($this->filterParentUpdateAttributes([
            'cin_recto' => $rectoPath,
            'cin_verso' => $versoPath,
            'verification_status' => 'verified',
            'verification_submitted_at' => now(),
            'verified_at' => now(),
            'verification_signature' => $signaturePath,
            'verification_terms_accepted_at' => now(),
            'user_id' => $user->id,
        ]));

        $this->cleanupVerificationDraftDirectory($parent);

        return redirect()
            ->to(URL::signedRoute('parents.verification', ['parent' => $parent->id]))
            ->with('success', 'Verification de compte parent enregistree avec succes.')
            ->with('temporary_password', $temporaryPassword);
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

    private function verificationDraftDirectory(ParentModel $parent): string
    {
        return self::VERIFICATION_DRAFT_PREFIX.'/'.$parent->id;
    }

    private function verificationDraftDocumentPath(ParentModel $parent, string $side): ?string
    {
        $directory = $this->verificationDraftDirectory($parent);

        foreach (Storage::disk('public')->files($directory) as $file) {
            if (Str::startsWith(basename($file), $side.'.')) {
                return $file;
            }
        }

        return null;
    }

    private function verificationDraftSignaturePath(ParentModel $parent): ?string
    {
        $path = $this->verificationDraftDirectory($parent).'/signature.png';

        return Storage::disk('public')->exists($path) ? $path : null;
    }

    private function storeVerificationDraftDocument(ParentModel $parent, string $side, UploadedFile $file): string
    {
        $directory = $this->verificationDraftDirectory($parent);
        Storage::disk('public')->makeDirectory($directory);

        $existing = $this->verificationDraftDocumentPath($parent, $side);

        if ($existing && Storage::disk('public')->exists($existing)) {
            Storage::disk('public')->delete($existing);
        }

        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $filename = $side.'.'.$extension;

        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return $directory.'/'.$filename;
    }

    private function storeVerificationDraftSignature(ParentModel $parent, string $signatureData): string
    {
        if (! preg_match('/^data:image\/(png|jpg|jpeg);base64,(.+)$/', $signatureData, $matches)) {
            abort(422, 'Format de signature invalide.');
        }

        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            abort(422, 'Signature illisible.');
        }

        $directory = $this->verificationDraftDirectory($parent);
        Storage::disk('public')->makeDirectory($directory);

        $path = $directory.'/signature.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function finalizeVerificationDocument(ParentModel $parent, string $side): ?string
    {
        $draftPath = $this->verificationDraftDocumentPath($parent, $side);

        if (! $draftPath) {
            return $parent->{$side};
        }

        $extension = pathinfo($draftPath, PATHINFO_EXTENSION) ?: 'jpg';
        $finalPath = 'parents/cin/'.$parent->id.'-'.$side.'.'.$extension;

        if ($parent->{$side} && Storage::disk('public')->exists($parent->{$side}) && $parent->{$side} !== $finalPath) {
            Storage::disk('public')->delete($parent->{$side});
        }

        if (Storage::disk('public')->exists($finalPath)) {
            Storage::disk('public')->delete($finalPath);
        }

        Storage::disk('public')->move($draftPath, $finalPath);

        return $finalPath;
    }

    private function finalizeVerificationSignature(ParentModel $parent): ?string
    {
        $draftPath = $this->verificationDraftSignaturePath($parent);

        if (! $draftPath) {
            return $this->storedVerificationSignaturePath($parent);
        }

        $finalPath = 'parents/signatures/'.$parent->id.'-signature.png';

        if ($parent->verification_signature && Storage::disk('public')->exists($parent->verification_signature) && $parent->verification_signature !== $finalPath) {
            Storage::disk('public')->delete($parent->verification_signature);
        }

        if (Storage::disk('public')->exists($finalPath)) {
            Storage::disk('public')->delete($finalPath);
        }

        Storage::disk('public')->move($draftPath, $finalPath);

        return $finalPath;
    }

    private function storedVerificationSignaturePath(ParentModel $parent): ?string
    {
        if ($this->parentColumnExists('verification_signature') && ! empty($parent->verification_signature)) {
            return $parent->verification_signature;
        }

        $fallbackPath = 'parents/signatures/'.$parent->id.'-signature.png';

        return Storage::disk('public')->exists($fallbackPath) ? $fallbackPath : null;
    }

    private function filterParentUpdateAttributes(array $attributes): array
    {
        return collect($attributes)
            ->filter(fn ($value, $key) => $this->parentColumnExists((string) $key))
            ->all();
    }

    private function parentColumnExists(string $column): bool
    {
        static $knownColumns = null;

        if ($knownColumns === null) {
            $knownColumns = array_flip(Schema::getColumnListing('parents'));
        }

        return isset($knownColumns[$column]);
    }

    private function isParentVerified(ParentModel $parent): bool
    {
        if ($this->parentColumnExists('verification_status') && ($parent->verification_status ?? null) === 'verified') {
            return true;
        }

        return (bool) ($parent->user_id && $parent->cin_recto && $parent->cin_verso && $this->storedVerificationSignaturePath($parent));
    }

    private function cleanupVerificationDraftDirectory(ParentModel $parent): void
    {
        $directory = $this->verificationDraftDirectory($parent);

        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }
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
