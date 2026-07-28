document.addEventListener('DOMContentLoaded', () => {
    const notificationMenu = document.querySelector('.modern-notifications-menu');
    const notificationList = document.getElementById('notifications-list');
    const notificationCount = document.getElementById('notifications-count');
    const subtitle = document.getElementById('notifications-subtitle');
    const markAllButton = document.getElementById('notifications-mark-all');
    const viewSwitchButtons = Array.from(document.querySelectorAll('.modern-notifications-switch-btn'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let activeView = 'unread';

    if (!notificationMenu || !notificationList || !notificationCount) {
        return;
    }

    document.addEventListener('click', (event) => {
        if (!notificationMenu.contains(event.target)) {
            notificationMenu.removeAttribute('open');
        }
    });

    if (markAllButton) {
        markAllButton.addEventListener('click', async () => {
            await markAllAsRead();
            await loadUnreadNotifications();
            if (activeView === 'archive') {
                await loadArchiveNotifications();
            }
        });
    }

    if (viewSwitchButtons.length > 0) {
        viewSwitchButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const nextView = button.dataset.view;
                if (!nextView || nextView === activeView) {
                    return;
                }

                activeView = nextView;
                syncActiveViewButton();

                if (activeView === 'archive') {
                    await loadArchiveNotifications();
                } else {
                    await loadUnreadNotifications();
                }
            });
        });
    }

    notificationList.addEventListener('click', async (event) => {
        const item = event.target.closest('[data-notification-item="true"]');
        if (!item) {
            return;
        }

        const id = item.dataset.id;
        if (!id) {
            return;
        }

        const actionUrl = item.dataset.actionUrl || null;

        if (activeView === 'unread') {
            await markNotificationAsRead(id);
        }

        if (actionUrl) {
            window.location.href = actionUrl;
            return;
        }

        if (activeView === 'archive') {
            await loadArchiveNotifications();
            return;
        }

        await loadUnreadNotifications();
    });

    loadUnreadNotifications();
    setInterval(() => {
        if (activeView === 'unread') {
            loadUnreadNotifications();
        }
    }, 30000);

    async function loadUnreadNotifications() {
        try {
            const response = await fetch('/api/notifications/unread', {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load notifications');
            }

            const notifications = await response.json();
            updateNotificationCount(Array.isArray(notifications) ? notifications.length : 0);
            updateNotificationList(Array.isArray(notifications) ? notifications : [], 'unread');
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    async function loadArchiveNotifications() {
        try {
            const response = await fetch('/api/notifications/archive?limit=50', {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load notifications archive');
            }

            const notifications = await response.json();
            updateNotificationList(Array.isArray(notifications) ? notifications : [], 'archive');
            if (subtitle) {
                subtitle.textContent = 'Archive des notifications';
            }
            if (markAllButton) {
                markAllButton.disabled = true;
            }
        } catch (error) {
            console.error('Error loading notifications archive:', error);
        }
    }

    function updateNotificationCount(count) {
        if (count > 0) {
            notificationCount.textContent = count > 99 ? '99+' : String(count);
            notificationCount.style.display = 'flex';
            if (subtitle) {
                subtitle.textContent = `${count} notification${count > 1 ? 's' : ''} non lue${count > 1 ? 's' : ''}`;
            }
            if (markAllButton) {
                markAllButton.disabled = false;
            }
            return;
        }

        notificationCount.style.display = 'none';
        if (subtitle) {
            subtitle.textContent = 'Aucune notification non lue';
        }
        if (markAllButton) {
            markAllButton.disabled = true;
        }
    }

    function updateNotificationList(notifications, view) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="modern-notifications-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <p>${view === 'archive' ? 'Archive vide' : 'Aucune notification'}</p>
                    <small>${view === 'archive' ? 'Aucune notification a afficher pour le moment' : 'Tout est a jour'}</small>
                </div>
            `;
            return;
        }

        const html = notifications.map((notification) => {
            const subject = escapeHtml(notification.subject || 'Notification');
            const description = formatDescription(notification.description || '', notification.metadata || {});
            const date = formatDate(notification.created_at);
            const actorName = escapeHtml(notification.actor_name || 'Systeme');
            const actorAvatarUrl = notification.actor_avatar_url ? escapeHtml(notification.actor_avatar_url) : '';
            const actorInitial = escapeHtml((notification.actor_name || 'S').trim().charAt(0).toUpperCase() || 'S');
            const actionUrl = notification.action_url ? escapeHtml(notification.action_url) : '';

            return `
                <article class="modern-notification-item" data-notification-item="true" data-id="${notification.id}" data-action-url="${actionUrl}">
                    <div class="modern-notification-avatar" title="Declencheur: ${actorName}">
                        ${actorAvatarUrl ? `<img src="${actorAvatarUrl}" alt="Avatar de ${actorName}">` : `<span>${actorInitial}</span>`}
                    </div>
                    <div class="modern-notification-content">
                        <div class="modern-notification-topline">
                            <strong>${subject}</strong>
                            <span class="modern-notification-actor">${actorName}</span>
                        </div>
                        ${description ? `<p>${description}</p>` : ''}
                        <small>${date}</small>
                    </div>
                </article>
            `;
        }).join('');

        notificationList.innerHTML = html;
    }

    function syncActiveViewButton() {
        viewSwitchButtons.forEach((button) => {
            const isActive = button.dataset.view === activeView;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    async function markNotificationAsRead(notificationId) {
        if (!csrfToken) {
            return;
        }

        await fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });
    }

    async function markAllAsRead() {
        if (!csrfToken) {
            return;
        }

        await fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });
    }

    function formatDate(rawDate) {
        if (!rawDate) {
            return '';
        }

        const date = new Date(rawDate);
        return date.toLocaleString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDescription(rawDescription, metadata) {
        const escaped = escapeHtml(rawDescription);
        const safeMetadata = metadata && typeof metadata === 'object' ? metadata : {};

        const withValues = escaped.replace(/\{([a-zA-Z0-9_]+)\}/g, (match, key) => {
            if (Object.prototype.hasOwnProperty.call(safeMetadata, key) && safeMetadata[key] !== null && safeMetadata[key] !== '') {
                return `<strong>${escapeHtml(String(safeMetadata[key]))}</strong>`;
            }

            return '';
        });

        return withValues
            .replace(/\s{2,}/g, ' ')
            .replace(/\s+\./g, '.')
            .trim();
    }
});
