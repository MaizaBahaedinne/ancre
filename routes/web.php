<?php

use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\ActivityRegistrationController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnfantController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ParentIncidentController;
use App\Http\Controllers\ParentPortalRequestController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ParentActivityController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\RequestSubjectController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if ($user->hasRole('Administrateur')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('Responsable')) {
        return redirect()->route('responsable.dashboard');
    }

    if ($user->hasRole('Educateur')) {
        return redirect()->route('educateur.dashboard');
    }

    if ($user->hasRole('Parent')) {
        return redirect()->route('parent.dashboard');
    }

    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:Responsable'])->group(function () {
    Route::get('/responsable', [DashboardController::class, 'index'])->name('responsable.dashboard');
});

Route::middleware(['auth', 'role:Educateur'])->group(function () {
    Route::get('/educateur', [DashboardController::class, 'index'])->name('educateur.dashboard');
});

Route::middleware(['auth', 'role:Parent'])->group(function () {
    Route::get('/parent', [DashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('/parent/incidents/{incident}', [ParentIncidentController::class, 'show'])->name('parent.incidents.show');
    Route::get('/parent/activites', [ParentActivityController::class, 'index'])->name('parent.activites.index');
    Route::get('/parent/activites/{activite}', [ParentActivityController::class, 'show'])->name('parent.activites.show')->whereNumber('activite');
    Route::post('/parent/activites/{activite}/inscriptions', [ActivityRegistrationController::class, 'storeByParent'])->name('parent.activites.registrations.store')->whereNumber('activite');
});

Route::middleware(['auth', 'permission:requests.parent'])->group(function () {
    Route::get('/parent/demandes', [ParentPortalRequestController::class, 'index'])->name('parent.demandes.index');
    Route::get('/parent/demandes/create', [ParentPortalRequestController::class, 'create'])->name('parent.demandes.create');
    Route::post('/parent/demandes', [ParentPortalRequestController::class, 'store'])->name('parent.demandes.store');
    Route::get('/parent/demandes/{parentRequest}', [ParentPortalRequestController::class, 'show'])->name('parent.demandes.show')->whereNumber('parentRequest');
    Route::post('/parent/demandes/{parentRequest}/messages', [ParentPortalRequestController::class, 'storeMessage'])->name('parent.demandes.messages.store')->whereNumber('parentRequest');
});

Route::middleware(['auth', 'permission:parents.view'])->group(function () {
    Route::get('parents', [ParentController::class, 'index'])->name('parents.index');
    Route::get('parents/{parent}', [ParentController::class, 'show'])->name('parents.show')->whereNumber('parent');
});

Route::get('parents/verification/{token}', [ParentController::class, 'verification'])
    ->name('parents.verification')
    ->where('token', '[A-Za-z0-9]+');

Route::middleware(['auth', 'permission:parents.create'])->group(function () {
    Route::get('parents/create', [ParentController::class, 'create'])->name('parents.create');
    Route::post('parents', [ParentController::class, 'store'])->name('parents.store');
});

Route::get('parents/create/scanner/{token}', [ParentController::class, 'cinScanner'])
    ->name('parents.cin-scanner')
    ->where('token', '[A-Za-z0-9]+');

Route::post('parents/create/scanner/{token}', [ParentController::class, 'storeCinScan'])
    ->name('parents.cin-scanner.store')
    ->where('token', '[A-Za-z0-9]+');

Route::get('parents/create/scanner/{token}/status', [ParentController::class, 'cinScanStatus'])
    ->name('parents.cin-scanner.status')
    ->where('token', '[A-Za-z0-9]+');

Route::middleware(['auth', 'permission:parents.update'])->group(function () {
    Route::get('parents/{parent}/edit', [ParentController::class, 'edit'])->name('parents.edit')->whereNumber('parent');
    Route::put('parents/{parent}', [ParentController::class, 'update'])->name('parents.update')->whereNumber('parent');
    Route::patch('parents/{parent}', [ParentController::class, 'update'])->whereNumber('parent');
});

Route::post('parents/verification/{token}', [ParentController::class, 'submitVerification'])
    ->name('parents.verification.store')
    ->where('token', '[A-Za-z0-9]+');

Route::middleware(['auth', 'permission:parents.delete'])->group(function () {
    Route::delete('parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy')->whereNumber('parent');
});

Route::middleware(['auth', 'permission:users.manage'])->group(function () {
    Route::post('parents/{parent}/create-user', [ParentController::class, 'createUser'])->name('parents.create-user');
    Route::post('personnels/{personnel}/create-user', [PersonnelController::class, 'createUser'])->name('personnels.create-user');
});

Route::middleware(['auth', 'permission:users.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserManagementController::class)->except(['show']);
    Route::resource('roles', RoleManagementController::class)->except(['show']);
});

Route::middleware(['auth', 'permission:children.view'])->group(function () {
    Route::get('enfants', [EnfantController::class, 'index'])->name('enfants.index');
    Route::get('enfants/{enfant}', [EnfantController::class, 'show'])->name('enfants.show')->whereNumber('enfant');
});

Route::middleware(['auth', 'permission:children.create'])->group(function () {
    Route::get('enfants/create', [EnfantController::class, 'create'])->name('enfants.create');
    Route::post('enfants', [EnfantController::class, 'store'])->name('enfants.store');
});

Route::middleware(['auth', 'permission:children.update'])->group(function () {
    Route::get('enfants/{enfant}/edit', [EnfantController::class, 'edit'])->name('enfants.edit')->whereNumber('enfant');
    Route::put('enfants/{enfant}', [EnfantController::class, 'update'])->name('enfants.update')->whereNumber('enfant');
    Route::patch('enfants/{enfant}', [EnfantController::class, 'update'])->whereNumber('enfant');
});

Route::middleware(['auth', 'permission:children.delete'])->group(function () {
    Route::delete('enfants/{enfant}', [EnfantController::class, 'destroy'])->name('enfants.destroy')->whereNumber('enfant');
});

Route::middleware(['auth', 'permission:registrations.view'])->group(function () {
    Route::get('inscriptions', [InscriptionController::class, 'index'])->name('inscriptions.index');
    Route::get('inscriptions/{inscription}', [InscriptionController::class, 'show'])->name('inscriptions.show')->whereNumber('inscription');
});

Route::middleware(['auth', 'permission:registrations.create'])->group(function () {
    Route::get('inscriptions/create', [InscriptionController::class, 'create'])->name('inscriptions.create');
    Route::post('inscriptions', [InscriptionController::class, 'store'])->name('inscriptions.store');
    Route::post('enfants/{enfant}/inscriptions', [EnfantController::class, 'storeCurrentYearInscription'])->name('enfants.inscriptions.store')->whereNumber('enfant');
});

Route::middleware(['auth', 'permission:registrations.update'])->group(function () {
    Route::get('inscriptions/{inscription}/edit', [InscriptionController::class, 'edit'])->name('inscriptions.edit')->whereNumber('inscription');
    Route::put('inscriptions/{inscription}', [InscriptionController::class, 'update'])->name('inscriptions.update')->whereNumber('inscription');
    Route::patch('inscriptions/{inscription}', [InscriptionController::class, 'update'])->whereNumber('inscription');
});

Route::middleware(['auth', 'permission:registrations.delete'])->group(function () {
    Route::delete('inscriptions/{inscription}', [InscriptionController::class, 'destroy'])->name('inscriptions.destroy')->whereNumber('inscription');
});

Route::middleware(['auth', 'permission:packages.view'])->group(function () {
    Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('packages/{package}', [PackageController::class, 'show'])->name('packages.show')->whereNumber('package');
});

Route::middleware(['auth', 'permission:packages.create'])->group(function () {
    Route::get('packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
});

Route::middleware(['auth', 'permission:packages.update'])->group(function () {
    Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit')->whereNumber('package');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update')->whereNumber('package');
    Route::patch('packages/{package}', [PackageController::class, 'update'])->whereNumber('package');
});

Route::middleware(['auth', 'permission:packages.delete'])->group(function () {
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy')->whereNumber('package');
});

Route::middleware(['auth', 'permission:attendance.view'])->group(function () {
    Route::get('presences', [PresenceController::class, 'index'])->name('presences.index');
    Route::get('presences/{presence}', [PresenceController::class, 'show'])->name('presences.show')->whereNumber('presence');
});

Route::middleware(['auth', 'permission:attendance.create'])->group(function () {
    Route::get('presences/create', [PresenceController::class, 'create'])->name('presences.create');
    Route::post('presences', [PresenceController::class, 'store'])->name('presences.store');
});

Route::middleware(['auth', 'permission:attendance.update'])->group(function () {
    Route::get('presences/{presence}/edit', [PresenceController::class, 'edit'])->name('presences.edit')->whereNumber('presence');
    Route::put('presences/{presence}', [PresenceController::class, 'update'])->name('presences.update')->whereNumber('presence');
    Route::patch('presences/{presence}', [PresenceController::class, 'update'])->whereNumber('presence');
});

Route::middleware(['auth', 'permission:attendance.delete'])->group(function () {
    Route::delete('presences/{presence}', [PresenceController::class, 'destroy'])->name('presences.destroy')->whereNumber('presence');
});

Route::middleware(['auth', 'permission:activities.view'])->group(function () {
    Route::get('activites', [ActiviteController::class, 'index'])->name('activites.index');
    Route::get('activites/{activite}', [ActiviteController::class, 'show'])->name('activites.show')->whereNumber('activite');
});

Route::middleware(['auth', 'permission:activities.create'])->group(function () {
    Route::get('activites/create', [ActiviteController::class, 'create'])->name('activites.create');
    Route::post('activites', [ActiviteController::class, 'store'])->name('activites.store');
});

Route::middleware(['auth', 'permission:activities.update'])->group(function () {
    Route::get('activites/{activite}/edit', [ActiviteController::class, 'edit'])->name('activites.edit')->whereNumber('activite');
    Route::put('activites/{activite}', [ActiviteController::class, 'update'])->name('activites.update')->whereNumber('activite');
    Route::patch('activites/{activite}', [ActiviteController::class, 'update'])->whereNumber('activite');
    Route::post('activites/{activite}/inscriptions', [ActivityRegistrationController::class, 'storeByStaff'])->name('activites.registrations.store')->whereNumber('activite');
    Route::patch('activites/{activite}/inscriptions/participation/grouped', [ActivityRegistrationController::class, 'markParticipationBatch'])->name('activites.registrations.participation.batch')->whereNumber('activite');
    Route::patch('activites/{activite}/inscriptions/{registration}/participation', [ActivityRegistrationController::class, 'markParticipation'])->name('activites.registrations.participation')->whereNumber('activite')->whereNumber('registration');
});

Route::middleware(['auth', 'permission:activities.delete'])->group(function () {
    Route::delete('activites/{activite}', [ActiviteController::class, 'destroy'])->name('activites.destroy')->whereNumber('activite');
});

Route::middleware(['auth', 'permission:rooms.create'])->group(function () {
    Route::get('salles/create', [SalleController::class, 'create'])->name('salles.create');
    Route::post('salles', [SalleController::class, 'store'])->name('salles.store');
});

Route::middleware(['auth', 'permission:rooms.view'])->group(function () {
    Route::get('salles', [SalleController::class, 'index'])->name('salles.index');
    Route::get('salles/{salle}', [SalleController::class, 'show'])->name('salles.show')->whereNumber('salle');
});

Route::middleware(['auth', 'permission:rooms.update'])->group(function () {
    Route::get('salles/{salle}/edit', [SalleController::class, 'edit'])->name('salles.edit')->whereNumber('salle');
    Route::put('salles/{salle}', [SalleController::class, 'update'])->name('salles.update')->whereNumber('salle');
    Route::patch('salles/{salle}', [SalleController::class, 'update'])->whereNumber('salle');
});

Route::middleware(['auth', 'permission:rooms.delete'])->group(function () {
    Route::delete('salles/{salle}', [SalleController::class, 'destroy'])->name('salles.destroy')->whereNumber('salle');
});

Route::middleware(['auth', 'permission:schools.create'])->group(function () {
    Route::get('schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('schools', [SchoolController::class, 'store'])->name('schools.store');
});

Route::middleware(['auth', 'permission:schools.view'])->group(function () {
    Route::get('schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::get('schools/{school}', [SchoolController::class, 'show'])->name('schools.show')->whereNumber('school');
});

Route::middleware(['auth', 'permission:schools.update'])->group(function () {
    Route::get('schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit')->whereNumber('school');
    Route::put('schools/{school}', [SchoolController::class, 'update'])->name('schools.update')->whereNumber('school');
    Route::patch('schools/{school}', [SchoolController::class, 'update'])->whereNumber('school');
});

Route::middleware(['auth', 'permission:schools.delete'])->group(function () {
    Route::delete('schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy')->whereNumber('school');
});

Route::middleware(['auth', 'permission:academic-years.create'])->group(function () {
    Route::get('academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
});

Route::middleware(['auth', 'permission:academic-years.view'])->group(function () {
    Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('academic-years/{academic_year}', [AcademicYearController::class, 'show'])->name('academic-years.show')->whereNumber('academic_year');
});

Route::middleware(['auth', 'permission:academic-years.update'])->group(function () {
    Route::get('academic-years/{academic_year}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit')->whereNumber('academic_year');
    Route::put('academic-years/{academic_year}', [AcademicYearController::class, 'update'])->name('academic-years.update')->whereNumber('academic_year');
    Route::patch('academic-years/{academic_year}', [AcademicYearController::class, 'update'])->whereNumber('academic_year');
});

Route::middleware(['auth', 'permission:academic-years.delete'])->group(function () {
    Route::delete('academic-years/{academic_year}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy')->whereNumber('academic_year');
});

Route::middleware(['auth', 'permission:payments.view'])->group(function () {
    Route::get('paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::get('paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show')->whereNumber('paiement');
    Route::get('paiements/{paiement}/receipt', [PaiementController::class, 'receipt'])->name('paiements.receipt')->whereNumber('paiement');
});

Route::middleware(['auth', 'permission:payments.create'])->group(function () {
    Route::get('paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('paiements', [PaiementController::class, 'store'])->name('paiements.store');
    Route::post('inscriptions/{inscription}/paiements', [InscriptionController::class, 'storeQuickPayment'])->name('inscriptions.payments.store')->whereNumber('inscription');
});

Route::middleware(['auth', 'permission:payments.update'])->group(function () {
    Route::get('paiements/{paiement}/edit', [PaiementController::class, 'edit'])->name('paiements.edit')->whereNumber('paiement');
    Route::put('paiements/{paiement}', [PaiementController::class, 'update'])->name('paiements.update')->whereNumber('paiement');
    Route::patch('paiements/{paiement}', [PaiementController::class, 'update'])->whereNumber('paiement');
});

Route::middleware(['auth', 'permission:payments.delete'])->group(function () {
    Route::delete('paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy')->whereNumber('paiement');
});

Route::middleware(['auth', 'permission:personnels.create'])->group(function () {
    Route::get('personnels/create', [PersonnelController::class, 'create'])->name('personnels.create');
    Route::post('personnels', [PersonnelController::class, 'store'])->name('personnels.store');
});

Route::middleware(['auth', 'permission:personnels.view'])->group(function () {
    Route::get('personnels', [PersonnelController::class, 'index'])->name('personnels.index');
    Route::get('personnels/{personnel}', [PersonnelController::class, 'show'])->name('personnels.show')->whereNumber('personnel');
});

Route::middleware(['auth', 'permission:personnels.update'])->group(function () {
    Route::get('personnels/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnels.edit')->whereNumber('personnel');
    Route::put('personnels/{personnel}', [PersonnelController::class, 'update'])->name('personnels.update')->whereNumber('personnel');
    Route::patch('personnels/{personnel}', [PersonnelController::class, 'update'])->whereNumber('personnel');
});

Route::middleware(['auth', 'permission:personnels.delete'])->group(function () {
    Route::delete('personnels/{personnel}', [PersonnelController::class, 'destroy'])->name('personnels.destroy')->whereNumber('personnel');
});

Route::middleware(['auth', 'permission:incidents.create'])->group(function () {
    Route::get('incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('incidents', [IncidentController::class, 'store'])->name('incidents.store');
});

Route::middleware(['auth', 'permission:incidents.view'])->group(function () {
    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show')->whereNumber('incident');
});

Route::middleware(['auth', 'permission:incidents.update'])->group(function () {
    Route::get('incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit')->whereNumber('incident');
    Route::put('incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update')->whereNumber('incident');
    Route::patch('incidents/{incident}', [IncidentController::class, 'update'])->whereNumber('incident');
});

Route::middleware(['auth', 'permission:incidents.delete'])->group(function () {
    Route::delete('incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy')->whereNumber('incident');
});

Route::middleware(['auth', 'permission:requests.view'])->group(function () {
    Route::get('demandes', [RequestManagementController::class, 'index'])->name('demandes.index');
    Route::get('demandes/{parentRequest}', [RequestManagementController::class, 'show'])->name('demandes.show')->whereNumber('parentRequest');
});

Route::middleware(['auth', 'permission:requests.update'])->group(function () {
    Route::patch('demandes/{parentRequest}/workflow', [RequestManagementController::class, 'updateWorkflow'])->name('demandes.workflow.update')->whereNumber('parentRequest');
    Route::post('demandes/{parentRequest}/messages', [RequestManagementController::class, 'storeMessage'])->name('demandes.messages.store')->whereNumber('parentRequest');
});

Route::middleware(['auth', 'permission:requests.subjects.manage'])->group(function () {
    Route::get('demandes-sujets', [RequestSubjectController::class, 'index'])->name('demandes-sujets.index');
    Route::get('demandes-sujets/create', [RequestSubjectController::class, 'create'])->name('demandes-sujets.create');
    Route::post('demandes-sujets', [RequestSubjectController::class, 'store'])->name('demandes-sujets.store');
    Route::get('demandes-sujets/{demandes_sujet}/edit', [RequestSubjectController::class, 'edit'])->name('demandes-sujets.edit')->whereNumber('demandes_sujet');
    Route::put('demandes-sujets/{demandes_sujet}', [RequestSubjectController::class, 'update'])->name('demandes-sujets.update')->whereNumber('demandes_sujet');
    Route::patch('demandes-sujets/{demandes_sujet}', [RequestSubjectController::class, 'update'])->whereNumber('demandes_sujet');
    Route::delete('demandes-sujets/{demandes_sujet}', [RequestSubjectController::class, 'destroy'])->name('demandes-sujets.destroy')->whereNumber('demandes_sujet');
});

require __DIR__.'/auth.php';
