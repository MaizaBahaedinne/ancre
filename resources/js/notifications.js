document.addEventListener('DOMContentLoaded', function() {
    const notificationToggle = document.querySelector('.modern-notifications-toggle');
    const notificationMenu = document.querySelector('.modern-notifications-menu');
    const notificationList = document.getElementById('notifications-list');
    const notificationCount = document.getElementById('notifications-count');

    if (!notificationToggle) return;

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationMenu.contains(e.target)) {
            notificationMenu.removeAttribute('open');
        }
    });

    // Load notifications on page load
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);

    function loadNotifications() {
        fetch('/api/notifications/unread')
            .then(response => response.json())
            .then(data => {
                updateNotificationList(data);
                updateNotificationCount(data.length);
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    function updateNotificationCount(count) {
        if (count > 0) {
            notificationCount.textContent = count > 99 ? '99+' : count;
            notificationCount.style.display = 'flex';
        } else {
            notificationCount.style.display = 'none';
        }
    }

    function updateNotificationList(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="modern-notifications-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Aucune notification</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            html += `
                <div class="modern-notification-item" data-id="${notification.id}">
                    <div class="modern-notification-content">
                        <strong>${notification.subject}</strong>
                        <p>${notification.description}</p>
                        <small>${new Date(notification.created_at).toLocaleString('fr-FR')}</small>
                    </div>
                    <button class="modern-notification-close" onclick="markNotificationAsRead(${notification.id})">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </div>
            `;
        });

        notificationList.innerHTML = html;
    }
});

function markNotificationAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-id="${notificationId}"]`).remove();
            // Reload notifications
            loadNotifications();
        }
    });
}

function loadNotifications() {
    fetch('/api/notifications/unread')
        .then(response => response.json())
        .then(data => {
            updateNotificationList(data);
            updateNotificationCount(data.length);
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function updateNotificationCount(count) {
    const badge = document.getElementById('notifications-count');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

function updateNotificationList(notifications) {
    const list = document.getElementById('notifications-list');
    if (notifications.length === 0) {
        list.innerHTML = `
            <div class="modern-notifications-empty">
                <i class="fa-solid fa-inbox"></i>
                <p>Aucune notification</p>
            </div>
        `;
        return;
    }

    let html = '';
    notifications.forEach(notification => {
        html += `
            <div class="modern-notification-item" data-id="${notification.id}">
                <div class="modern-notification-content">
                    <strong>${notification.subject}</strong>
                    <p>${notification.description}</p>
                    <small>${new Date(notification.created_at).toLocaleString('fr-FR')}</small>
                </div>
                <button class="modern-notification-close" onclick="markNotificationAsRead(${notification.id})">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
        `;
    });

    list.innerHTML = html;
}
