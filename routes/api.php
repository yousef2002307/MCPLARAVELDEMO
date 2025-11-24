<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
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