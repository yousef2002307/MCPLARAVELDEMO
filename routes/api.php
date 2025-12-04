<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Middleware\AcceptLanguageMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Post CRUD Routes with file upload support
Route::prefix('posts')->middleware(AcceptLanguageMiddleware::class)->group(function () {
    // Get all posts (with pagination)
    Route::get('/', [PostController::class, 'index']);
    
    // Get a single post
    Route::get('/{id}', [PostController::class, 'show']);
    
    // Create a new post (with file uploads)
    Route::post('/', [PostController::class, 'store']);
    
    // Update a post (with file uploads)
    Route::post('/{id}', [PostController::class, 'update']); // Using POST for file uploads
    
    // Delete a post
    Route::delete('/{id}', [PostController::class, 'destroy']);
    
    // Delete a specific media item from a post
    Route::delete('/{postId}/media/{mediaId}', [PostController::class, 'deleteMedia']);
});

// Chunked Video Upload Routes
Route::prefix('chunked-upload')->middleware(AcceptLanguageMiddleware::class)->group(function () {
    // Upload video chunks
    Route::post('/video', [\App\Http\Controllers\Api\ChunkedVideoController::class, 'upload']);
    
    // Complete upload and attach to post
    Route::post('/video/complete', [\App\Http\Controllers\Api\ChunkedVideoController::class, 'complete']);
});

// Notification Routes (requires authentication)
Route::prefix('notifications')->group(function () {
    // Get all notifications
    Route::get('/', [NotificationController::class, 'index']);
    
    // Get unread notifications only
    Route::get('/unread', [NotificationController::class, 'unread']);
    
    // Get notification statistics
    Route::get('/stats', [NotificationController::class, 'stats']);
    
    // Mark a specific notification as read
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    
    // Mark all notifications as read
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    
    // Delete a specific notification
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    
    // Delete all read notifications
    Route::delete('/read/all', [NotificationController::class, 'deleteAllRead']);
});