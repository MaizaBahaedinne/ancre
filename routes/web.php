<?php

use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\NotificationWorkflowController;
use App\Http\Controllers\Admin\TriggerRegistryController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\ActivityRegistrationController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DeveloperToolsController;
use App\Http\Controllers\EnfantController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ParentIncidentController;
use App\Http\Controllers\ParentPortalRequestController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ParentActivityController;
use App\Http\Controllers\Admin\VitrineAdminController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlatformFeedController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\RequestSubjectController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EnfantEvaluationController;
use App\Http\Controllers\VitrineController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    if (! auth()->check()) {
        return redirect()->route('vitrine.home');
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

Route::name('vitrine.')->group(function () {
    Route::get('/', [VitrineController::class, 'home'])->name('home');
    Route::get('/a-propos', [VitrineController::class, 'about'])->name('about');
    Route::get('/services', [VitrineController::class, 'services'])->name('services');
    Route::get('/activites', [VitrineController::class, 'activities'])->name('activities');
    Route::get('/actualites', [VitrineController::class, 'blog'])->name('blog');
    Route::get('/actualites/{slug}', [VitrineController::class, 'blogShow'])->name('blog.show');
    Route::get('/contact', [VitrineController::class, 'contact'])->name('contact');
    Route::get('/privacy-policy-terms', [VitrineController::class, 'privacy'])->name('privacy');
    Route::get('/conditions', [VitrineController::class, 'conditions'])->name('conditions');
    Route::post('/contact', [VitrineController::class, 'submitContact'])->name('contact.submit');
    Route::post('/newsletter/subscribe', [VitrineController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
    Route::post('/visit-request', [VitrineController::class, 'submitVisitRequest'])->name('visit-request.submit');
});

Route::middleware(['auth', 'role:Parent'])->name('vitrine.')->group(function () {
    Route::post('/actualites/{blogPost}/commentaires', [VitrineController::class, 'storeBlogComment'])
        ->name('blog.comments.store')
        ->whereNumber('blogPost');

    Route::post('/actualites/{blogPost}/reactions', [VitrineController::class, 'storeBlogReaction'])
        ->name('blog.reactions.store')
        ->whereNumber('blogPost');
});

// Compatibilite legacy pour les anciennes URLs /vitrine/*
Route::prefix('vitrine')->group(function () {
    Route::get('/', fn () => redirect()->route('vitrine.home', [], 301));
    Route::get('/a-propos', fn () => redirect()->route('vitrine.about', [], 301));
    Route::get('/services', fn () => redirect()->route('vitrine.services', [], 301));
    Route::get('/activites', fn () => redirect()->route('vitrine.activities', [], 301));
    Route::get('/actualites', fn () => redirect()->route('vitrine.blog', [], 301));
    Route::get('/actualites/{slug}', fn (string $slug) => redirect()->route('vitrine.blog.show', ['slug' => $slug], 301));
    Route::get('/contact', fn () => redirect()->route('vitrine.contact', [], 301));
    Route::get('/privacy-policy-terms', fn () => redirect()->route('vitrine.privacy', [], 301));
    Route::get('/conditions', fn () => redirect()->route('vitrine.conditions', [], 301));
    Route::post('/contact', [VitrineController::class, 'submitContact']);
    Route::post('/newsletter/subscribe', [VitrineController::class, 'subscribeNewsletter']);
    Route::post('/visit-request', [VitrineController::class, 'submitVisitRequest']);
});

Route::middleware(['auth', 'role:Parent'])->prefix('vitrine')->group(function () {
    Route::post('/actualites/{blogPost}/commentaires', [VitrineController::class, 'storeBlogComment'])
        ->whereNumber('blogPost');

    Route::post('/actualites/{blogPost}/reactions', [VitrineController::class, 'storeBlogReaction'])
        ->whereNumber('blogPost');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    Route::get('/fil-actualite', [PlatformFeedController::class, 'index'])->name('platform.feed');
    Route::post('/fil-actualite/annonces', [PlatformFeedController::class, 'storeAnnouncement'])->name('platform.feed.announcements.store');
    Route::post('/fil-actualite/reactions', [PlatformFeedController::class, 'react'])->name('platform.feed.reactions.store');
    Route::post('/fil-actualite/commentaires', [PlatformFeedController::class, 'comment'])->name('platform.feed.comments.store');
    
    // Notification routes
    Route::get('/api/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/api/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/api/notifications/archive', [NotificationController::class, 'archive'])->name('notifications.archive');
    Route::post('/api/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        $request = request();
        $roleNames = $request->user()->roles()->pluck('name');

        abort_unless($roleNames->contains('Administrateur') || $roleNames->contains('admin'), 403);

        return app(DashboardController::class)->index($request);
    })->name('admin.dashboard');

    Route::get('/responsable', function () {
        $request = request();
        $roleNames = $request->user()->roles()->pluck('name');

        abort_unless($roleNames->contains('Responsable') || $roleNames->contains('responsable'), 403);

        return app(DashboardController::class)->index($request);
    })->name('responsable.dashboard');

    Route::get('/educateur', function () {
        $request = request();
        $roleNames = $request->user()->roles()->pluck('name');

        abort_unless($roleNames->contains('Educateur') || $roleNames->contains('educateur'), 403);

        return app(DashboardController::class)->index($request);
    })->name('educateur.dashboard');

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

Route::get('parents/{parent}/verification', [ParentController::class, 'verification'])
    ->name('parents.verification')
    ->middleware('signed')
    ->whereNumber('parent');

Route::get('parents/{parent}/verification/status', [ParentController::class, 'verificationStatus'])
    ->name('parents.verification.status')
    ->middleware('signed')
    ->whereNumber('parent');

Route::post('parents/{parent}/verification/document', [ParentController::class, 'storeVerificationDocument'])
    ->name('parents.verification.document')
    ->middleware('signed')
    ->whereNumber('parent');

Route::post('parents/{parent}/verification/signature', [ParentController::class, 'storeVerificationSignature'])
    ->name('parents.verification.signature')
    ->middleware('signed')
    ->whereNumber('parent');

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

Route::post('parents/{parent}/verification', [ParentController::class, 'submitVerification'])
    ->name('parents.verification.store')
    ->middleware('signed')
    ->whereNumber('parent');

Route::middleware(['auth', 'permission:parents.delete'])->group(function () {
    Route::delete('parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy')->whereNumber('parent');
});

Route::middleware(['auth', 'permission:users.manage'])->group(function () {
    Route::post('parents/{parent}/create-user', [ParentController::class, 'createUser'])->name('parents.create-user');
    Route::post('personnels/{personnel}/create-user', [PersonnelController::class, 'createUser'])->name('personnels.create-user');
});

Route::middleware(['auth', 'permission:users.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserManagementController::class)->except(['show']);
});

Route::middleware(['auth', 'permission:permissions.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleManagementController::class)->except(['show']);
});

Route::middleware(['auth', 'permission:developer.tools.view'])->prefix('admin/developer')->name('admin.developer.')->group(function () {
    Route::get('/', [DeveloperToolsController::class, 'index'])->name('index');
    Route::get('/logs', [DeveloperToolsController::class, 'logs'])->name('logs');
});

Route::middleware(['auth', 'permission:vitrine.manage'])->prefix('admin/vitrine')->name('admin.vitrine.')->group(function () {
    Route::get('/', [VitrineAdminController::class, 'index'])->name('index');
    Route::get('/settings', [VitrineAdminController::class, 'settingsPage'])->name('settings');
    Route::get('/pages', [VitrineAdminController::class, 'pagesPage'])->name('pages');
    Route::get('/services', [VitrineAdminController::class, 'servicesPage'])->name('services');
    Route::get('/schedules', [VitrineAdminController::class, 'schedulesPage'])->name('schedules');
    Route::get('/social-posts', [VitrineAdminController::class, 'socialPostsPage'])->name('social-posts');
    Route::get('/blog-posts', [VitrineAdminController::class, 'blogPostsPage'])->name('blog-posts');
    Route::get('/testimonials', [VitrineAdminController::class, 'testimonialsPage'])->name('testimonials');
    Route::get('/faqs', [VitrineAdminController::class, 'faqsPage'])->name('faqs');
    Route::get('/leads', [VitrineAdminController::class, 'leadsPage'])->name('leads');
    Route::get('/newsletters', [VitrineAdminController::class, 'newslettersPage'])->name('newsletters');
    Route::get('/newsletter/export', [VitrineAdminController::class, 'exportNewsletterCsv'])->name('newsletter.export');
    Route::put('/settings', [VitrineAdminController::class, 'updateSettings'])->name('settings.update');
    Route::put('/pages/{page}', [VitrineAdminController::class, 'updatePage'])->name('pages.update')->whereNumber('page');

    Route::post('/services', [VitrineAdminController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [VitrineAdminController::class, 'updateService'])->name('services.update')->whereNumber('service');
    Route::delete('/services/{service}', [VitrineAdminController::class, 'destroyService'])->name('services.destroy')->whereNumber('service');

    Route::post('/schedules', [VitrineAdminController::class, 'storeSchedule'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [VitrineAdminController::class, 'updateSchedule'])->name('schedules.update')->whereNumber('schedule');
    Route::delete('/schedules/{schedule}', [VitrineAdminController::class, 'destroySchedule'])->name('schedules.destroy')->whereNumber('schedule');

    Route::post('/social-posts', [VitrineAdminController::class, 'storeSocialPost'])->name('social-posts.store');
    Route::put('/social-posts/{socialPost}', [VitrineAdminController::class, 'updateSocialPost'])->name('social-posts.update')->whereNumber('socialPost');
    Route::delete('/social-posts/{socialPost}', [VitrineAdminController::class, 'destroySocialPost'])->name('social-posts.destroy')->whereNumber('socialPost');

    Route::post('/blog-posts', [VitrineAdminController::class, 'storeBlogPost'])->name('blog-posts.store');
    Route::put('/blog-posts/{blogPost}', [VitrineAdminController::class, 'updateBlogPost'])->name('blog-posts.update')->whereNumber('blogPost');
    Route::delete('/blog-posts/{blogPost}', [VitrineAdminController::class, 'destroyBlogPost'])->name('blog-posts.destroy')->whereNumber('blogPost');

    Route::post('/testimonials', [VitrineAdminController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::put('/testimonials/{testimonial}', [VitrineAdminController::class, 'updateTestimonial'])->name('testimonials.update')->whereNumber('testimonial');
    Route::delete('/testimonials/{testimonial}', [VitrineAdminController::class, 'destroyTestimonial'])->name('testimonials.destroy')->whereNumber('testimonial');

    Route::post('/faqs', [VitrineAdminController::class, 'storeFaq'])->name('faqs.store');
    Route::put('/faqs/{faq}', [VitrineAdminController::class, 'updateFaq'])->name('faqs.update')->whereNumber('faq');
    Route::delete('/faqs/{faq}', [VitrineAdminController::class, 'destroyFaq'])->name('faqs.destroy')->whereNumber('faq');
});

// Notification Workflows Admin Routes
Route::middleware(['auth', 'permission:notifications.manage'])->prefix('admin/notifications')->name('admin.notifications.')->group(function () {
    Route::get('/workflows', [NotificationWorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/logs', [NotificationWorkflowController::class, 'logs'])->name('logs.index');
    Route::get('/workflows/{notificationWorkflow}', [NotificationWorkflowController::class, 'show'])->name('workflows.show');
    Route::get('/workflows/{notificationWorkflow}/edit', [NotificationWorkflowController::class, 'edit'])->name('workflows.edit');
    Route::put('/workflows/{notificationWorkflow}', [NotificationWorkflowController::class, 'update'])->name('workflows.update');
    Route::post('/workflows/{notificationWorkflow}/test', [NotificationWorkflowController::class, 'test'])->name('workflows.test');
    Route::post('/workflows/{notificationWorkflow}/receivers', [NotificationWorkflowController::class, 'addReceiver'])->name('receivers.store');
    Route::post('/receivers/{notificationReceiver}/toggle', [NotificationWorkflowController::class, 'toggleReceiver'])->name('receivers.toggle');
    Route::delete('/receivers/{notificationReceiver}', [NotificationWorkflowController::class, 'removeReceiver'])->name('receivers.destroy');

    Route::get('/registry', [TriggerRegistryController::class, 'index'])->name('registry.index');
    Route::post('/registry/sync', [TriggerRegistryController::class, 'sync'])->name('registry.sync');
    Route::get('/registry/{trigger}/edit', [TriggerRegistryController::class, 'edit'])->name('registry.edit');
    Route::put('/registry/{trigger}', [TriggerRegistryController::class, 'update'])->name('registry.update');
    Route::delete('/registry/{trigger}', [TriggerRegistryController::class, 'destroy'])->name('registry.destroy');
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
    Route::post('enfants/{enfant}/photo', [EnfantController::class, 'uploadPhoto'])->name('enfants.photo.upload')->whereNumber('enfant');
    Route::post('enfants/{enfant}/evaluations', [EnfantEvaluationController::class, 'upsert'])->name('enfants.evaluations.upsert')->whereNumber('enfant');
    Route::put('enfants/{enfant}', [EnfantController::class, 'update'])->name('enfants.update')->whereNumber('enfant');
    Route::patch('enfants/{enfant}', [EnfantController::class, 'update'])->whereNumber('enfant');
});

Route::middleware(['auth', 'permission:subjects.view'])->group(function () {
    Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
});

Route::middleware(['auth', 'permission:subjects.create'])->group(function () {
    Route::get('subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
});

Route::middleware(['auth', 'permission:subjects.update'])->group(function () {
    Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit')->whereNumber('subject');
    Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update')->whereNumber('subject');
    Route::patch('subjects/{subject}', [SubjectController::class, 'update'])->whereNumber('subject');
});

Route::middleware(['auth', 'permission:subjects.delete'])->group(function () {
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy')->whereNumber('subject');
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
    Route::post('inscriptions/{inscription}/evaluations', [EnfantEvaluationController::class, 'upsertByInscription'])->name('inscriptions.evaluations.upsert')->whereNumber('inscription');
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
