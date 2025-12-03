# 🔔 Notification System - Quick Reference

## 📌 Quick Commands

```bash
# Run migration (if not already done)
php artisan migrate

# Create new notification
php artisan make:notification NotificationName

# Run queue worker (for async notifications)
php artisan queue:work
```

---

## 🔥 Most Common Use Cases

### 1️⃣ Get Unread Count
```javascript
const response = await fetch('/api/notifications/stats', {
    headers: { 'Authorization': 'Bearer TOKEN' }
});
const data = await response.json();
console.log(data.data.unread_count); // e.g., 5
```

### 2️⃣ Display Notifications
```javascript
const response = await fetch('/api/notifications?per_page=10', {
    headers: { 'Authorization': 'Bearer TOKEN' }
});
const data = await response.json();
data.data.data.forEach(n => {
    console.log(n.data.message);
});
```

### 3️⃣ Mark as Read
```javascript
await fetch(`/api/notifications/${notificationId}/read`, {
    method: 'PATCH',
    headers: { 'Authorization': 'Bearer TOKEN' }
});
```

### 4️⃣ Send Notification (Backend)
```php
use App\Models\User;
use App\Notifications\NewPostCreated;

// To one user
$user = User::find(1);
$user->notify(new NewPostCreated($post));

// To all users
$users = User::all();
Notification::send($users, new NewPostCreated($post));
```

---

## 🎯 API Endpoints Cheat Sheet

| What You Want | Method | Endpoint |
|---------------|--------|----------|
| Get all notifications | GET | `/api/notifications` |
| Get unread only | GET | `/api/notifications/unread` |
| Get counts | GET | `/api/notifications/stats` |
| Mark one as read | PATCH | `/api/notifications/{id}/read` |
| Mark all as read | POST | `/api/notifications/mark-all-read` |
| Delete one | DELETE | `/api/notifications/{id}` |
| Delete all read | DELETE | `/api/notifications/read/all` |

**All require**: `Authorization: Bearer YOUR_TOKEN`

---

## 💾 Notification Data Structure

```json
{
    "id": "uuid-here",
    "type": "App\\Notifications\\NewPostCreated",
    "data": {
        "post_id": 1,
        "title": "Post Title",
        "message": "A new post has been created: Post Title",
        "action_url": "/posts/1"
    },
    "read_at": null,
    "created_at": "2025-11-26T15:30:00.000000Z"
}
```

---

## 🎨 HTML Badge Example

```html
<div class="notification-icon">
    🔔
    <span id="badge">0</span>
</div>

<script>
async function updateBadge() {
    const res = await fetch('/api/notifications/stats', {
        headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    document.getElementById('badge').textContent = data.data.unread_count;
}

// Update every 30 seconds
setInterval(updateBadge, 30000);
</script>
```

---

## 🔍 Common Queries (Backend)

```php
// Get user's notifications
$notifications = auth()->user()->notifications;

// Get unread only
$unread = auth()->user()->unreadNotifications;

// Count unread
$count = auth()->user()->unreadNotifications->count();

// Mark all as read
auth()->user()->unreadNotifications->markAsRead();

// Delete old notifications
auth()->user()->notifications()
    ->where('created_at', '<', now()->subDays(30))
    ->delete();
```

---

## 🚀 Testing Flow

1. **Create a post** → Triggers notification
   ```bash
   POST /api/posts
   ```

2. **Check notifications** → See the new notification
   ```bash
   GET /api/notifications
   ```

3. **Mark as read** → Update status
   ```bash
   PATCH /api/notifications/{id}/read
   ```

4. **Verify stats** → Check counts
   ```bash
   GET /api/notifications/stats
   ```

---

## 📱 Real-World Example

```javascript
// Notification Bell Component
class NotificationBell {
    constructor() {
        this.token = localStorage.getItem('token');
        this.updateInterval = 30000; // 30 seconds
    }

    async getCount() {
        const res = await fetch('/api/notifications/stats', {
            headers: { 'Authorization': `Bearer ${this.token}` }
        });
        const data = await res.json();
        return data.data.unread_count;
    }

    async updateBadge() {
        const count = await this.getCount();
        document.getElementById('notification-badge').textContent = count;
        document.getElementById('notification-badge').style.display = 
            count > 0 ? 'block' : 'none';
    }

    start() {
        this.updateBadge();
        setInterval(() => this.updateBadge(), this.updateInterval);
    }
}

// Usage
const bell = new NotificationBell();
bell.start();
```

---

## 🎓 Key Concepts

| Concept | Explanation |
|---------|-------------|
| **Notifiable** | Trait that allows a model to receive notifications |
| **Notification Class** | Defines what data to send and how |
| **Channels** | Where to send (database, mail, SMS, etc.) |
| **Polymorphic** | Any model can receive notifications |
| **Queue** | Send notifications asynchronously |

---

## ⚡ Performance Tips

1. **Use Queues**: Implement `ShouldQueue` interface
2. **Limit Recipients**: Don't send to all users if not needed
3. **Clean Old Data**: Delete old notifications regularly
4. **Pagination**: Always paginate notification lists
5. **Eager Loading**: Use `with()` when loading relationships

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| No notifications appearing | Check migration ran, User has Notifiable trait |
| 401 Unauthorized | Verify token is correct and not expired |
| Notifications not sending | Check queue is running if using ShouldQueue |
| Slow performance | Enable queues, add database indexes |

---

## 📚 Files Reference

- **Guide**: `NOTIFICATIONS_GUIDE.md`
- **Examples**: `NOTIFICATION_API_EXAMPLES.js`
- **Demo**: `notification-demo.html`
- **Summary**: `NOTIFICATION_SUMMARY.md`
- **This**: `NOTIFICATION_QUICK_REFERENCE.md`

---

## ✅ Checklist

- [x] Migration created and ran
- [x] User model has Notifiable trait
- [x] Notification class created
- [x] Controller created
- [x] Routes configured
- [x] Documentation complete
- [x] Examples ready
- [x] Demo page available

**You're all set! 🎉**

---

**Need help?** Check `NOTIFICATIONS_GUIDE.md` for detailed documentation.
