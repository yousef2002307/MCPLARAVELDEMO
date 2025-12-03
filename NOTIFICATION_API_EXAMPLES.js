// NOTIFICATION API EXAMPLES
// Base URL: http://localhost/api
// All endpoints require authentication (Bearer token)

const API_URL = 'http://localhost/api';
const TOKEN = 'YOUR_AUTH_TOKEN_HERE'; // Replace with actual token

// Helper function for API calls
const apiCall = async (endpoint, method = 'GET', data = null) => {
    const config = {
        method,
        headers: {
            'Authorization': `Bearer ${TOKEN}`,
            'Accept': 'application/json',
        }
    };

    if (data && method !== 'GET') {
        config.headers['Content-Type'] = 'application/json';
        config.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, config);
        const result = await response.json();
        console.log(`${method} ${endpoint}:`, result);
        return result;
    } catch (error) {
        console.error(`Error ${method} ${endpoint}:`, error);
        throw error;
    }
};

// ============================================
// NOTIFICATION EXAMPLES
// ============================================

// 1. GET ALL NOTIFICATIONS
async function getAllNotifications() {
    return await apiCall('/notifications?per_page=10');
}

// 2. GET UNREAD NOTIFICATIONS ONLY
async function getUnreadNotifications() {
    return await apiCall('/notifications/unread?per_page=10');
}

// 3. GET NOTIFICATION STATISTICS
async function getNotificationStats() {
    return await apiCall('/notifications/stats');
}

// 4. MARK SPECIFIC NOTIFICATION AS READ
async function markNotificationAsRead(notificationId) {
    return await apiCall(`/notifications/${notificationId}/read`, 'PATCH');
}

// 5. MARK ALL NOTIFICATIONS AS READ
async function markAllNotificationsAsRead() {
    return await apiCall('/notifications/mark-all-read', 'POST');
}

// 6. DELETE A SPECIFIC NOTIFICATION
async function deleteNotification(notificationId) {
    return await apiCall(`/notifications/${notificationId}`, 'DELETE');
}

// 7. DELETE ALL READ NOTIFICATIONS
async function deleteAllReadNotifications() {
    return await apiCall('/notifications/read/all', 'DELETE');
}

// ============================================
// COMPLETE WORKFLOW EXAMPLE
// ============================================

async function notificationWorkflow() {
    console.log('=== NOTIFICATION WORKFLOW DEMO ===\n');

    // Step 1: Get statistics
    console.log('1. Getting notification statistics...');
    const stats = await getNotificationStats();
    console.log(`   Total: ${stats.data.total_notifications}`);
    console.log(`   Unread: ${stats.data.unread_count}`);
    console.log(`   Read: ${stats.data.read_count}\n`);

    // Step 2: Get unread notifications
    console.log('2. Fetching unread notifications...');
    const unread = await getUnreadNotifications();
    console.log(`   Found ${unread.data.data.length} unread notifications\n`);

    // Step 3: Mark first notification as read (if exists)
    if (unread.data.data.length > 0) {
        const firstNotification = unread.data.data[0];
        console.log('3. Marking first notification as read...');
        console.log(`   Notification: ${firstNotification.data.message}`);
        await markNotificationAsRead(firstNotification.id);
        console.log('   ✓ Marked as read\n');
    }

    // Step 4: Get all notifications
    console.log('4. Fetching all notifications...');
    const all = await getAllNotifications();
    console.log(`   Total notifications: ${all.data.total}\n`);

    // Step 5: Display notification details
    console.log('5. Notification details:');
    all.data.data.forEach((notification, index) => {
        console.log(`   ${index + 1}. ${notification.data.message}`);
        console.log(`      Post ID: ${notification.data.post_id}`);
        console.log(`      Status: ${notification.read_at ? 'Read' : 'Unread'}`);
        console.log(`      Created: ${notification.created_at}\n`);
    });
}

// ============================================
// REAL-TIME NOTIFICATION BADGE UPDATER
// ============================================

class NotificationBadge {
    constructor(badgeElementId, intervalMs = 30000) {
        this.badgeElement = document.getElementById(badgeElementId);
        this.intervalMs = intervalMs;
        this.intervalId = null;
    }

    async update() {
        try {
            const stats = await getNotificationStats();
            const unreadCount = stats.data.unread_count;

            if (this.badgeElement) {
                this.badgeElement.textContent = unreadCount;
                this.badgeElement.style.display = unreadCount > 0 ? 'inline-block' : 'none';
            }

            return unreadCount;
        } catch (error) {
            console.error('Failed to update notification badge:', error);
        }
    }

    start() {
        // Update immediately
        this.update();

        // Then update at intervals
        this.intervalId = setInterval(() => this.update(), this.intervalMs);
        console.log(`Notification badge updater started (every ${this.intervalMs}ms)`);
    }

    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.log('Notification badge updater stopped');
        }
    }
}

// ============================================
// NOTIFICATION LIST COMPONENT (Example)
// ============================================

class NotificationList {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.notifications = [];
    }

    async load() {
        const response = await getAllNotifications();
        this.notifications = response.data.data;
        this.render();
    }

    render() {
        if (!this.container) return;

        const html = this.notifications.map(notification => `
            <div class="notification-item ${notification.read_at ? 'read' : 'unread'}" 
                 data-id="${notification.id}">
                <div class="notification-content">
                    <p class="notification-message">${notification.data.message}</p>
                    <small class="notification-time">${new Date(notification.created_at).toLocaleString()}</small>
                </div>
                <div class="notification-actions">
                    ${!notification.read_at ?
                `<button onclick="markAsRead('${notification.id}')">Mark as Read</button>` :
                ''}
                    <button onclick="deleteNotif('${notification.id}')">Delete</button>
                </div>
            </div>
        `).join('');

        this.container.innerHTML = html || '<p>No notifications</p>';
    }

    async markAsRead(notificationId) {
        await markNotificationAsRead(notificationId);
        await this.load(); // Reload list
    }

    async delete(notificationId) {
        await deleteNotification(notificationId);
        await this.load(); // Reload list
    }
}

// ============================================
// USAGE EXAMPLES
// ============================================

// Example 1: Run complete workflow
// notificationWorkflow();

// Example 2: Initialize notification badge
// const badge = new NotificationBadge('notification-badge', 30000);
// badge.start();

// Example 3: Initialize notification list
// const notificationList = new NotificationList('notifications-container');
// notificationList.load();

// Example 4: Get and display unread count
async function displayUnreadCount() {
    const stats = await getNotificationStats();
    console.log(`You have ${stats.data.unread_count} unread notifications`);
}

// Example 5: Mark all as read and show confirmation
async function clearAllNotifications() {
    const confirm = window.confirm('Mark all notifications as read?');
    if (confirm) {
        await markAllNotificationsAsRead();
        console.log('All notifications marked as read!');
    }
}

// ============================================
// AXIOS VERSION (Alternative)
// ============================================

// If using Axios instead of fetch:
/*
const axios = require('axios');

const axiosInstance = axios.create({
    baseURL: API_URL,
    headers: {
        'Authorization': `Bearer ${TOKEN}`,
        'Accept': 'application/json'
    }
});

// Get all notifications
async function getAllNotificationsAxios() {
    const response = await axiosInstance.get('/notifications', {
        params: { per_page: 10 }
    });
    return response.data;
}

// Mark as read
async function markAsReadAxios(notificationId) {
    const response = await axiosInstance.patch(`/notifications/${notificationId}/read`);
    return response.data;
}
*/

console.log('Notification API examples loaded!');
console.log('Available functions:');
console.log('- getAllNotifications()');
console.log('- getUnreadNotifications()');
console.log('- getNotificationStats()');
console.log('- markNotificationAsRead(id)');
console.log('- markAllNotificationsAsRead()');
console.log('- deleteNotification(id)');
console.log('- deleteAllReadNotifications()');
console.log('- notificationWorkflow()');
console.log('\nClasses:');
console.log('- NotificationBadge');
console.log('- NotificationList');
