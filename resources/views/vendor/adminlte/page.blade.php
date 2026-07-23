<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.bootstrap5.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="modern-admin-shell">
    <div class="modern-admin-bg"></div>

    <div class="modern-admin-layout" data-sidebar-state="desktop">
        <aside class="modern-sidebar" id="app-sidebar">
            <div class="modern-brand">
                <a href="{{ route('home') }}" class="modern-brand-link">
                    <span class="modern-brand-mark modern-brand-mark-image">
                        <img src="{{ asset('images/logo-ancre-des-elites.svg') }}" alt="Logo Ancre Des Elites" class="modern-brand-logo">
                    </span>
                    <span>
                        <strong>Ancre Des Elites</strong>
                        <small>Administration</small>
                    </span>
                </a>
            </div>

            <nav class="modern-nav">
                @can('dashboard.view')
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Tableau de bord</span>
                        <a href="{{ route('home') }}" class="modern-nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('responsable.dashboard') || request()->routeIs('educateur.dashboard') || request()->routeIs('parent.dashboard') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Accueil</span>
                        </a>
                    </div>
                @endcan

                @canany(['parents.view', 'children.view'])
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Famille</span>
                        @can('parents.view')
                            <a href="{{ route('parents.index') }}" class="modern-nav-link {{ request()->routeIs('parents.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-people-roof"></i>
                                <span>Parents</span>
                            </a>
                        @endcan
                        @can('children.view')
                            <a href="{{ route('enfants.index') }}" class="modern-nav-link {{ request()->routeIs('enfants.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-children"></i>
                                <span>Enfants</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                @canany(['registrations.view', 'attendance.view', 'activities.view', 'incidents.view'])
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Vie scolaire</span>
                        @can('registrations.view')
                            <a href="{{ route('inscriptions.index') }}" class="modern-nav-link {{ request()->routeIs('inscriptions.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-file-signature"></i>
                                <span>Inscriptions</span>
                            </a>
                        @endcan
                        @can('attendance.view')
                            <a href="{{ route('presences.index') }}" class="modern-nav-link {{ request()->routeIs('presences.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Présences</span>
                            </a>
                        @endcan
                        @can('activities.view')
                            <a href="{{ route('activites.index') }}" class="modern-nav-link {{ request()->routeIs('activites.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-palette"></i>
                                <span>Activités</span>
                            </a>
                        @endcan
                        @can('incidents.view')
                            <a href="{{ route('incidents.index') }}" class="modern-nav-link {{ request()->routeIs('incidents.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Incidents</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                @canany(['packages.view', 'payments.view', 'personnels.view'])
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Finances et équipe</span>
                        @can('packages.view')
                            <a href="{{ route('packages.index') }}" class="modern-nav-link {{ request()->routeIs('packages.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Packages</span>
                            </a>
                        @endcan
                        @can('payments.view')
                            <a href="{{ route('paiements.index') }}" class="modern-nav-link {{ request()->routeIs('paiements.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span>Paiements</span>
                            </a>
                        @endcan
                        @can('personnels.view')
                            <a href="{{ route('personnels.index') }}" class="modern-nav-link {{ request()->routeIs('personnels.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i>
                                <span>Personnel</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                @canany(['rooms.view', 'schools.view', 'academic-years.view'])
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Structure</span>
                        @can('rooms.view')
                            <a href="{{ route('salles.index') }}" class="modern-nav-link {{ request()->routeIs('salles.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-door-open"></i>
                                <span>Salles</span>
                            </a>
                        @endcan
                        @can('schools.view')
                            <a href="{{ route('schools.index') }}" class="modern-nav-link {{ request()->routeIs('schools.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-school"></i>
                                <span>Écoles</span>
                            </a>
                        @endcan
                        @can('academic-years.view')
                            <a href="{{ route('academic-years.index') }}" class="modern-nav-link {{ request()->routeIs('academic-years.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-calendar-week"></i>
                                <span>Années scolaires</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                @canany(['requests.view', 'requests.subjects.manage'])
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Communication</span>
                        @can('requests.view')
                            <a href="{{ route('demandes.index') }}" class="modern-nav-link {{ request()->routeIs('demandes.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-comments"></i>
                                <span>Demandes</span>
                            </a>
                        @endcan
                        @can('requests.subjects.manage')
                            <a href="{{ route('demandes-sujets.index') }}" class="modern-nav-link {{ request()->routeIs('demandes-sujets.*') ? 'is-active' : '' }}">
                                <i class="fa-solid fa-list-check"></i>
                                <span>Sujets Demandes</span>
                            </a>
                        @endcan
                    </div>
                @endcanany

                @can('requests.parent')
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Communication Parent</span>
                        <a href="{{ route('parent.demandes.index') }}" class="modern-nav-link {{ request()->routeIs('parent.demandes.*') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-message"></i>
                            <span>Mes Demandes</span>
                        </a>
                    </div>
                @endcan

                @can('users.manage')
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Administration</span>
                        <a href="{{ route('admin.users.index') }}" class="modern-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-users"></i>
                            <span>Users</span>
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="modern-nav-link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Rôles et permissions</span>
                        </a>
                    </div>
                @endcan

                @can('developer.tools.view')
                    <div class="modern-nav-section">
                        <span class="modern-nav-label">Developpeur</span>
                        <a href="{{ route('admin.developer.index') }}" class="modern-nav-link {{ request()->routeIs('admin.developer.index') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-rocket"></i>
                            <span>Deploiement</span>
                        </a>
                        <a href="{{ route('admin.developer.logs') }}" class="modern-nav-link {{ request()->routeIs('admin.developer.logs') ? 'is-active' : '' }}">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>Logs</span>
                        </a>
                    </div>
                @endcan

                <div class="modern-nav-section">
                    <span class="modern-nav-label">Compte</span>
                    <a href="{{ route('profile.edit') }}" class="modern-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-user"></i>
                        <span>Profil</span>
                    </a>
                </div>
            </nav>
        </aside>

        <div class="modern-sidebar-overlay" data-sidebar-overlay></div>

        <div class="modern-main">
            <header class="modern-topbar">
                <div class="modern-topbar-left">
                    <button type="button" class="modern-sidebar-toggle" data-sidebar-toggle aria-label="Ouvrir le menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <p class="modern-topbar-kicker">Garderie</p>
                        <h1 class="modern-topbar-title">@yield('title', 'Tableau de Bord')</h1>
                    </div>
                </div>

                <div class="modern-topbar-right">
                    <div class="modern-role-pill">
                        <i class="fa-solid fa-shield-heart"></i>
                        <span>{{ auth()->user()->getRoleNames()->join(', ') ?: 'Aucun role' }}</span>
                    </div>

                    <details class="modern-user-menu">
                        <summary>
                            <span class="modern-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="modern-user-meta">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </summary>

                        <div class="modern-user-dropdown">
                            <a href="{{ route('profile.edit') }}" class="modern-user-dropdown-link">
                                <i class="fa-solid fa-user-gear"></i>
                                <span>Mon profil</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="modern-user-dropdown-link is-danger">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Deconnexion</span>
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>

            <main class="modern-content">
                @hasSection('content_header')
                    <section class="modern-page-head">
                        @yield('content_header')
                    </section>
                @endif

                <section class="modern-page-body">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/responsive.bootstrap5.min.js"></script>
    <script>
        (() => {
            const layout = document.querySelector('.modern-admin-layout');
            const toggle = document.querySelector('[data-sidebar-toggle]');
            const overlay = document.querySelector('[data-sidebar-overlay]');

            if (!layout || !toggle || !overlay) {
                return;
            }

            const closeSidebar = () => layout.classList.remove('is-sidebar-open');
            const toggleSidebar = () => layout.classList.toggle('is-sidebar-open');

            toggle.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });
        })();

        (() => {
            const enhanced = new WeakSet();
            const initSearchableSelects = () => {
                document.querySelectorAll('select').forEach((element) => {
                    if (enhanced.has(element) || element.multiple || element.dataset.nativeSelect === 'true') {
                        return;
                    }

                    const shouldEnhance = element.dataset.enhanceSelect === 'true' || element.options.length > 8;

                    if (!shouldEnhance) {
                        return;
                    }

                    new TomSelect(element, {
                        create: false,
                        allowEmptyOption: true,
                        maxOptions: 500,
                        searchField: ['text'],
                        placeholder: element.options[0]?.text ?? 'Rechercher...',
                    });

                    enhanced.add(element);
                });
            };

            document.addEventListener('DOMContentLoaded', initSearchableSelects);
        })();

        (() => {
            const editors = [];

            const createRichEditors = () => {
                document.querySelectorAll('[data-rich-editor]').forEach((element) => {
                    if (element.dataset.editorReady === 'true') {
                        return;
                    }

                    const inputId = element.dataset.input;
                    const hiddenInput = document.getElementById(inputId);

                    if (!hiddenInput) {
                        return;
                    }

                    const quill = new Quill(element, {
                        theme: 'snow',
                        placeholder: element.dataset.placeholder || 'Saisissez votre texte...',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ align: [] }],
                                ['blockquote', 'link'],
                                ['clean'],
                            ],
                        },
                    });

                    if (hiddenInput.value) {
                        quill.clipboard.dangerouslyPasteHTML(hiddenInput.value);
                    }

                    quill.on('text-change', () => {
                        const html = quill.root.innerHTML;
                        hiddenInput.value = html === '<p><br></p>' ? '' : html;
                    });

                    element.dataset.editorReady = 'true';
                    editors.push({ quill, hiddenInput });
                });

                document.querySelectorAll('form').forEach((form) => {
                    if (form.dataset.richEditorsBound === 'true') {
                        return;
                    }

                    form.addEventListener('submit', () => {
                        editors.forEach(({ quill, hiddenInput }) => {
                            if (form.contains(hiddenInput)) {
                                const html = quill.root.innerHTML;
                                hiddenInput.value = html === '<p><br></p>' ? '' : html;
                            }
                        });
                    });

                    form.dataset.richEditorsBound = 'true';
                });
            };

            document.addEventListener('DOMContentLoaded', createRichEditors);
        })();

        (() => {
            const initDataTables = () => {
                if (!window.jQuery || !window.DataTable) {
                    return;
                }

                const $ = window.jQuery;

                $('.js-data-table').each(function () {
                    if ($.fn.dataTable.isDataTable(this)) {
                        return;
                    }

                    const $table = $(this);
                    const $singleRow = $table.find('tbody > tr').first();
                    const $singleCell = $singleRow.find('td').first();

                    // Remove server-rendered "empty" row with colspan to avoid DataTables column mismatch warnings.
                    if (
                        $table.find('tbody > tr').length === 1 &&
                        $singleRow.find('td').length === 1 &&
                        Number($singleCell.attr('colspan') || 0) > 1
                    ) {
                        $singleRow.remove();
                    }

                    $table.DataTable({
                        responsive: true,
                        autoWidth: false,
                        pageLength: -1,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Tous']],
                        language: {
                            search: 'Rechercher :',
                            lengthMenu: 'Afficher _MENU_ lignes',
                            info: 'Affichage de _START_ a _END_ sur _TOTAL_ elements',
                            infoEmpty: 'Aucun element a afficher',
                            infoFiltered: '(filtre sur _MAX_ elements)',
                            zeroRecords: 'Aucun resultat trouve',
                            paginate: {
                                first: 'Premier',
                                last: 'Dernier',
                                next: 'Suivant',
                                previous: 'Precedent',
                            },
                            emptyTable: 'Aucune donnee disponible',
                        },
                        dom: '<"modern-table-toolbar d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-3"lf>rt<"modern-table-footer d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mt-3"ip>',
                        columnDefs: [
                            {
                                targets: 'no-sort',
                                orderable: false,
                                searchable: false,
                            },
                            {
                                targets: 0,
                                responsivePriority: 1,
                            },
                        ],
                    });
                });
            };

            document.addEventListener('DOMContentLoaded', initDataTables);
        })();
    </script>

    @yield('js')
</body>
</html>