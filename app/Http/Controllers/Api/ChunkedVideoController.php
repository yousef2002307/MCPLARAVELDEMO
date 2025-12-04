<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ChunkedVideoController extends Controller
{
    /**
     * Handle chunked video upload
     * 
     * This endpoint receives video chunks and stores them temporarily.
     * Once all chunks are received, they are automatically merged.
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            // Validate the file input
            $request->validate([
                'file' => 'required|file',
            ]);

            // Create the file receiver
            $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

            // Check if the upload is complete
            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }

            // Receive the file
            $save = $receiver->receive();

            // Check if the upload has finished (all chunks received)
            if ($save->isFinished()) {
                // Get the uploaded file
                $file = $save->getFile();
                
                // Generate a unique filename
                $filename = $this->createFilename($file);
                
                // Store the file in the public disk under 'temp-videos' directory
                $finalPath = Storage::disk('public')->putFileAs(
                    'temp-videos',
                    $file,
                    $filename
                );

                // Delete the temporary chunk file
                unlink($file->getPathname());

                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully',
                    'data' => [
                        'path' => $finalPath,
                        'filename' => $filename,
                        'url' => Storage::disk('public')->url($finalPath),
                    ]
                ], 200);
            }

            // If upload is not finished, return the progress percentage
            /** @var AbstractHandler $handler */
            $handler = $save->handler();

            return response()->json([
                'success' => true,
                'message' => 'Chunk uploaded successfully',
                'data' => [
                    'done' => $handler->getPercentageDone(),
                    'status' => 'uploading'
                ]
            ], 200);

        } catch (UploadMissingFileException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No file was uploaded',
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Chunked upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading video chunk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete the upload and attach video to a post
     * 
     * This endpoint is called after all chunks are uploaded and merged.
     * It attaches the video to the specified post using Spatie Media Library.
     */
    public function complete(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'post_id' => 'required|integer|exists:posts,id',
                'filename' => 'required|string',
                'collection' => 'required|string|in:video,video_gallery',
            ]);

            // Find the post
            $post = Post::findOrFail($validated['post_id']);

            // Get the temporary file path
            $tempPath = 'temp-videos/' . $validated['filename'];
            $fullPath = Storage::disk('public')->path($tempPath);

            // Check if file exists
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uploaded file not found'
                ], 404);
            }

            // Validate that it's a video file
            $mimeType = mime_content_type($fullPath);
            $allowedMimeTypes = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/webm'];
            
            if (!in_array($mimeType, $allowedMimeTypes)) {
                // Delete the invalid file
                Storage::disk('public')->delete($tempPath);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file type. Only video files are allowed.'
                ], 422);
            }

            // If collection is 'video' (single video), clear existing video first
            if ($validated['collection'] === 'video') {
                $post->clearMediaCollection('video');
            }

            // Add the video to the post using Spatie Media Library
            $media = $post->addMedia($fullPath)
                ->toMediaCollection($validated['collection']);

            // Delete the temporary file
            Storage::disk('public')->delete($tempPath);

            // Reload the post with media
            $post->load('media');

            return response()->json([
                'success' => true,
                'message' => 'Video attached to post successfully',
                'data' => [
                    'media_id' => $media->id,
                    'media_url' => $media->getUrl(),
                    'media_name' => $media->file_name,
                    'media_size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'collection' => $validated['collection'],
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error completing video upload: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error attaching video to post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a unique filename for the uploaded video
     */
    private function createFilename(\Symfony\Component\HttpFoundation\File\UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace('.' . $extension, '', $file->getClientOriginalName());
        $filename = preg_replace('/[^A-Za-z0-9\-_]/', '_', $filename);
        
        return $filename . '_' . time() . '.' . $extension;
    }
}
