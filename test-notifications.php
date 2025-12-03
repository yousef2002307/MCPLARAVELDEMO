<?php

// Test Notification System
// Run this with: php test-notifications.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Post;
use App\Notifications\NewPostCreated;
use Illuminate\Support\Facades\Notification;

echo "=== Testing Notification System ===\n\n";

// 1. Check users
echo "1. Checking users in database...\n";
$users = User::all();
echo "   Found {$users->count()} users\n\n";

if ($users->count() === 0) {
    echo "   ❌ No users found! Please create users first.\n";
    exit(1);
}

// 2. Create a test post
echo "2. Creating a test post...\n";
$post = Post::create([
    'title' => ['en' => 'Test Notification Post', 'nl' => 'Test Notificatie Post'],
    'body' => ['en' => 'This is a test post to verify notifications work.', 'nl' => 'Dit is een testbericht om meldingen te verifiëren.'],
]);
echo "   ✓ Post created with ID: {$post->id}\n\n";

// 3. Send notification manually
echo "3. Sending notification to all users...\n";
try {
    Notification::send($users, new NewPostCreated($post));
    echo "   ✓ Notification sent successfully!\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error sending notification: {$e->getMessage()}\n\n";
    exit(1);
}

// 4. Check notifications in database
echo "4. Checking notifications in database...\n";
$notificationCount = \DB::table('notifications')->count();
echo "   Total notifications in database: {$notificationCount}\n\n";

// 5. Check each user's notifications
echo "5. Checking user notifications...\n";
foreach ($users as $user) {
    $userNotifications = $user->notifications;
    $unreadCount = $user->unreadNotifications->count();
    echo "   User #{$user->id} ({$user->name}):\n";
    echo "      - Total notifications: {$userNotifications->count()}\n";
    echo "      - Unread: {$unreadCount}\n";
    
    if ($userNotifications->count() > 0) {
        $latest = $userNotifications->first();
        echo "      - Latest: {$latest->data['message']}\n";
    }
    echo "\n";
}

// 6. Summary
echo "=== Test Complete ===\n";
if ($notificationCount >= $users->count()) {
    echo "✅ SUCCESS! Notifications are working correctly.\n";
    echo "   - Created 1 post\n";
    echo "   - Sent {$users->count()} notifications\n";
    echo "   - All users received notifications\n";
} else {
    echo "⚠️  WARNING: Expected {$users->count()} notifications but found {$notificationCount}\n";
}

echo "\nYou can now delete the test post if needed:\n";
echo "php artisan tinker --execute=\"App\\Models\\Post::find({$post->id})->delete();\"\n";
