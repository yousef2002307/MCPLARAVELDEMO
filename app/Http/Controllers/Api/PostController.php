<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $posts = Post::with('media')
                ->latest()
                ->paginate($perPage);

            // Transform posts to include media URLs
            $posts->getCollection()->transform(function ($post) {
                return $this->transformPost($post);
            });

            return response()->json([
                'success' => true,
                'data' => $posts,
                'message' => 'Posts retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created post.
     */
    public function store(PostRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create the post with translatable fields
            $post = Post::create([
                'title' => $request->input('title'), // Can be array for multiple languages
                'body' => $request->input('body'),   // Can be array for multiple languages
            ]);

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $post->addMediaFromRequest('main_image')
                    ->toMediaCollection('main_image');
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $post->addMedia($image)
                        ->toMediaCollection('gallery');
                }
            }

            // Handle video upload
            if ($request->hasFile('video')) {
                $post->addMediaFromRequest('video')
                    ->toMediaCollection('video');
            }

            // Handle video gallery upload
            if ($request->hasFile('video_gallery')) {
                foreach ($request->file('video_gallery') as $video) {
                    $post->addMedia($video)
                        ->toMediaCollection('video_gallery');
                }
            }

            // Handle temporary videos from chunked upload
            if ($request->has('temp_videos')) {
                $tempVideos = $request->input('temp_videos');
                if (is_array($tempVideos)) {
                    foreach ($tempVideos as $filename) {
                        $tempPath = storage_path('app/public/temp-videos/' . $filename);
                        if (file_exists($tempPath)) {
                            $post->addMedia($tempPath)
                                ->toMediaCollection('video_gallery');
                            // Delete temp file after adding to media library
                            @unlink($tempPath);
                        }
                    }
                }
            }

            DB::commit();

            // Load media relationships
            $post->load('media');

            // Send notification to all users
            $users = User::find(1);
            
            Notification::send($users, new NewPostCreated($post));

            return response()->json([
                'success' => true,
                'data' => $this->transformPost($post),
                'message' => 'Post created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified post.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $post = Post::with('media')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $this->transformPost($post),
                'message' => 'Post retrieved successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified post.
     */
    public function update(PostRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $post = Post::findOrFail($id);

            // Update translatable fields
            $post->update([
                'title' => $request->input('title'),
                'body' => $request->input('body'),
            ]);

            // Handle main image update
            if ($request->hasFile('main_image')) {
                // Clear existing main image
                $post->clearMediaCollection('main_image');
                // Add new main image
                $post->addMediaFromRequest('main_image')
                    ->toMediaCollection('main_image');
            }

            // Handle gallery images update
            if ($request->hasFile('gallery')) {
                // Optionally clear existing gallery images
                if ($request->input('replace_gallery', false)) {
                    $post->clearMediaCollection('gallery');
                }
                
                // Add new gallery images
                foreach ($request->file('gallery') as $image) {
                    $post->addMedia($image)
                        ->toMediaCollection('gallery');
                }
            }

            // Handle gallery image deletion by IDs
            if ($request->has('delete_gallery_ids')) {
                $deleteIds = $request->input('delete_gallery_ids');
                if (is_array($deleteIds)) {
                    foreach ($deleteIds as $mediaId) {
                        $media = $post->media()->find($mediaId);
                        if ($media) {
                            $media->delete();
                        }
                    }
                }
            }

            // Handle video update
            if ($request->hasFile('video')) {
                // Clear existing video
                $post->clearMediaCollection('video');
                // Add new video
                $post->addMediaFromRequest('video')
                    ->toMediaCollection('video');
            }

            // Handle video deletion
            if ($request->input('delete_video', false)) {
                $post->clearMediaCollection('video');
            }

            // Handle video gallery update
            if ($request->hasFile('video_gallery')) {
                // Optionally clear existing video gallery
                if ($request->input('replace_video_gallery', false)) {
                    $post->clearMediaCollection('video_gallery');
                }
                
                // Add new video gallery items
                foreach ($request->file('video_gallery') as $video) {
                    $post->addMedia($video)
                        ->toMediaCollection('video_gallery');
                }
            }

            // Handle temporary videos from chunked upload
            if ($request->has('temp_videos')) {
                $tempVideos = $request->input('temp_videos');
                if (is_array($tempVideos)) {
                    foreach ($tempVideos as $filename) {
                        $tempPath = storage_path('app/public/temp-videos/' . $filename);
                        if (file_exists($tempPath)) {
                            $post->addMedia($tempPath)
                                ->toMediaCollection('video_gallery');
                            // Delete temp file after adding to media library
                            @unlink($tempPath);
                        }
                    }
                }
            }

            // Handle video gallery deletion by IDs
            if ($request->has('delete_video_gallery_ids')) {
                $deleteIds = $request->input('delete_video_gallery_ids');
                if (is_array($deleteIds)) {
                    foreach ($deleteIds as $mediaId) {
                        $media = $post->media()->find($mediaId);
                        if ($media && $media->collection_name === 'video_gallery') {
                            $media->delete();
                        }
                    }
                }
            }

            DB::commit();

            // Reload media relationships
            $post->load('media');

            return response()->json([
                'success' => true,
                'data' => $this->transformPost($post),
                'message' => 'Post updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified post.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $post = Post::findOrFail($id);
            
            // Media will be automatically deleted due to Spatie Media Library
            $post->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific media item from a post.
     */
    public function deleteMedia(string $postId, string $mediaId): JsonResponse
    {
        try {
            $post = Post::findOrFail($postId);
            $media = $post->media()->find($mediaId);

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found'
                ], 404);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting media: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform post data to include media URLs.
     */
    private function transformPost(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'main_image' => $post->getFirstMedia('main_image') ? [
                'id' => $post->getFirstMedia('main_image')->id,
                'url' => $post->getFirstMedia('main_image')->getUrl(),
                'thumb_url' => $post->getFirstMedia('main_image')->getUrl('thumb'),
                'name' => $post->getFirstMedia('main_image')->file_name,
                'size' => $post->getFirstMedia('main_image')->size,
            ] : null,
            'gallery' => $post->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'name' => $media->file_name,
                    'size' => $media->size,
                ];
            }),
            'video' => $post->getFirstMedia('video') ? [
                'id' => $post->getFirstMedia('video')->id,
                'url' => $post->getFirstMedia('video')->getUrl(),
                'name' => $post->getFirstMedia('video')->file_name,
                'size' => $post->getFirstMedia('video')->size,
                'mime_type' => $post->getFirstMedia('video')->mime_type,
            ] : null,
            'video_gallery' => $post->getMedia('video_gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                ];
            }),
        ];
    }
}
