# Laravel Notifications System - Complete Guide

## Overview
This guide explains how to use the Laravel notifications system with the User model. When a new post is created, all users receive a notification stored in the database.

## Database Table Structure

The `notifications` table has the following structure:
```sql
- id (UUID, primary key)
- type (string) - The notification class name
- notifiable_type (string) - The model type (e.g., App\Models\User)
- notifiable_id (bigint) - The model ID
- data (text/json) - The notification data
- read_at (timestamp, nullable) - When the notification was read
- created_at (timestamp)
- updated_at (timestamp)
```

## How It Works

### 1. User Model Setup
The `User` model already has the `Notifiable` trait:
```php
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    // ...
}
```

### 2. Notification Class
`App\Notifications\NewPostCreated` - Sent when a new post is created.

**Data stored in database:**
```json
{
    "post_id": 1,
    "title": "Post Title",
    "body": "First 100 characters of the post...",
    "message": "A new post has been created: Post Title",
    "action_url": "/posts/1",
    "created_at": "2025-11-26 15:30:00"
}
```

### 3. Automatic Notification Sending
When a post is created via `POST /api/posts`, all users automatically receive a notification.

## API Endpoints

### Authentication Required
All notification endpoints require authentication using Laravel Sanctum.

**Headers:**
```
Authorization: Bearer {your-token}
Accept: application/json
```

---

### 1. Get All Notifications
**Endpoint:** `GET /api/notifications`

**Query Parameters:**
- `per_page` (optional, default: 15) - Number of notifications per page

**Example Request:**
```bash
curl -X GET "http://localhost/api/notifications?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
                "type": "App\\Notifications\\NewPostCreated",
                "notifiable_type": "App\\Models\\User",
                "notifiable_id": 1,
                "data": {
                    "post_id": 5,
                    "title": "New Laravel Features",
                    "body": "Laravel 11 introduces amazing new features...",
                    "message": "A new post has been created: New Laravel Features",
                    "action_url": "/posts/5",
                    "created_at": "2025-11-26 15:30:00"
                },
                "read_at": null,
                "created_at": "2025-11-26T15:30:00.000000Z",
                "updated_at": "2025-11-26T15:30:00.000000Z"
            }
        ],
        "per_page": 10,
        "total": 25
    },
    "unread_count": 15,
    "message": "Notifications retrieved successfully"
}
```

---

### 2. Get Unread Notifications Only
**Endpoint:** `GET /api/notifications/unread`

**Query Parameters:**
- `per_page` (optional, default: 15)

**Example Request:**
```bash
curl -X GET "http://localhost/api/notifications/unread" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": "9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a",
                "type": "App\\Notifications\\NewPostCreated",
                "data": {
                    "post_id": 5,
                    "title": "New Laravel Features",
                    "message": "A new post has been created: New Laravel Features"
                },
                "read_at": null,
                "created_at": "2025-11-26T15:30:00.000000Z"
            }
        ]
    },
    "unread_count": 15,
    "message": "Unread notifications retrieved successfully"
}
```

---

### 3. Get Notification Statistics
**Endpoint:** `GET /api/notifications/stats`

**Example Request:**
```bash
curl -X GET "http://localhost/api/notifications/stats" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "total_notifications": 25,
        "unread_count": 15,
        "read_count": 10
    },
    "message": "Notification statistics retrieved successfully"
}
```

---

### 4. Mark Notification as Read
**Endpoint:** `PATCH /api/notifications/{id}/read`

**Example Request:**
```bash
curl -X PATCH "http://localhost/api/notifications/9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a/read" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "message": "Notification marked as read",
    "unread_count": 14
}
```

---

### 5. Mark All Notifications as Read
**Endpoint:** `POST /api/notifications/mark-all-read`

**Example Request:**
```bash
curl -X POST "http://localhost/api/notifications/mark-all-read" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "message": "All notifications marked as read",
    "unread_count": 0
}
```

---

### 6. Delete a Notification
**Endpoint:** `DELETE /api/notifications/{id}`

**Example Request:**
```bash
curl -X DELETE "http://localhost/api/notifications/9d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "message": "Notification deleted successfully",
    "unread_count": 14
}
```

---

### 7. Delete All Read Notifications
**Endpoint:** `DELETE /api/notifications/read/all`

**Example Request:**
```bash
curl -X DELETE "http://localhost/api/notifications/read/all" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
    "success": true,
    "message": "All read notifications deleted successfully",
    "unread_count": 15
}
```

---

## JavaScript/Axios Examples

### Get Notifications
```javascript
const getNotifications = async () => {
    try {
        const response = await axios.get('/api/notifications', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            params: {
                per_page: 10
            }
        });
        
        console.log('Notifications:', response.data.data);
        console.log('Unread count:', response.data.unread_count);
    } catch (error) {
        console.error('Error:', error.response.data);
    }
};
```

### Mark as Read
```javascript
const markAsRead = async (notificationId) => {
    try {
        const response = await axios.patch(
            `/api/notifications/${notificationId}/read`,
            {},
            {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        );
        
        console.log('Updated unread count:', response.data.unread_count);
    } catch (error) {
        console.error('Error:', error.response.data);
    }
};
```

### Real-time Notification Badge
```javascript
const updateNotificationBadge = async () => {
    try {
        const response = await axios.get('/api/notifications/stats', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const badge = document.getElementById('notification-badge');
        badge.textContent = response.data.data.unread_count;
        badge.style.display = response.data.data.unread_count > 0 ? 'block' : 'none';
    } catch (error) {
        console.error('Error:', error.response.data);
    }
};

// Update badge every 30 seconds
setInterval(updateNotificationBadge, 30000);
```

---

## Creating Custom Notifications

### Step 1: Create Notification Class
```bash
php artisan make:notification YourNotificationName
```

### Step 2: Define Notification
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class YourNotificationName extends Notification
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // Can also include 'mail', 'broadcast', etc.
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your custom message',
            'data' => $this->data,
            'action_url' => '/your-url',
        ];
    }
}
```

### Step 3: Send Notification
```php
use App\Models\User;
use App\Notifications\YourNotificationName;

// Send to one user
$user = User::find(1);
$user->notify(new YourNotificationName($data));

// Send to multiple users
$users = User::where('role', 'admin')->get();
Notification::send($users, new YourNotificationName($data));

// Send to all users
$users = User::all();
Notification::send($users, new YourNotificationName($data));
```

---

## Using Notifications in Code

### Check if User Has Unread Notifications
```php
$user = auth()->user();

if ($user->unreadNotifications->count() > 0) {
    // User has unread notifications
}
```

### Get Specific Notification Type
```php
$postNotifications = $user->notifications()
    ->where('type', NewPostCreated::class)
    ->get();
```

### Access Notification Data
```php
foreach ($user->notifications as $notification) {
    echo $notification->data['message'];
    echo $notification->data['post_id'];
    echo $notification->created_at;
}
```

---

## Testing the Notification System

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Create a Post
```bash
curl -X POST "http://localhost/api/posts" \
  -H "Accept: application/json" \
  -H "Content-Type: multipart/form-data" \
  -F "title[en]=Test Post" \
  -F "body[en]=This is a test post"
```

### 3. Check Notifications
```bash
curl -X GET "http://localhost/api/notifications" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## Best Practices

1. **Queue Notifications**: For better performance, implement `ShouldQueue` interface (already done in `NewPostCreated`)
2. **Limit Recipients**: Instead of sending to all users, consider filtering by user preferences
3. **Clean Old Notifications**: Regularly delete old read notifications
4. **Use Notification Channels**: Combine database with email, SMS, or push notifications
5. **Add User Preferences**: Let users choose which notifications they want to receive

---

## Troubleshooting

### Notifications Not Appearing
1. Check if migration ran: `php artisan migrate:status`
2. Verify User model has `Notifiable` trait
3. Check queue is running if using `ShouldQueue`: `php artisan queue:work`

### Authentication Issues
1. Ensure you're using the correct token
2. Check middleware is `auth:sanctum` in routes
3. Verify token hasn't expired

---

## Summary

✅ User model has `Notifiable` trait  
✅ Notifications table created via migration  
✅ `NewPostCreated` notification sends to all users when post is created  
✅ Full API for managing notifications (list, read, delete)  
✅ Supports pagination and filtering  
✅ Real-time unread count tracking  

The notification system is now fully functional and ready to use!
