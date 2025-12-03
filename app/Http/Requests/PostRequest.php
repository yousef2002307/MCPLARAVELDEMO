<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            // Title can be a string or an array of translations
            'title' => 'required',
            'title.*' => 'string|max:255', // For translatable array format
            
            // Body can be a string or an array of translations
            'body' => 'required',
            'body.*' => 'string', // For translatable array format
            
            // Main image validation
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            
            // Gallery images validation
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
            
            // Optional: Replace all gallery images or add to existing
            'replace_gallery' => 'nullable|boolean',
            
            // Optional: Array of media IDs to delete from gallery
            'delete_gallery_ids' => 'nullable|array',
            'delete_gallery_ids.*' => 'integer|exists:media,id',
            
            // Video validation
            'video' => 'nullable|file|mimes:mp4,mpeg,mov,avi,webm|max:51200', // 50MB max
            
            // Optional: Delete video flag
            'delete_video' => 'nullable|boolean',
            
            // Video gallery validation
            'video_gallery' => 'nullable|array',
            'video_gallery.*' => 'file|mimes:mp4,mpeg,mov,avi,webm|max:51200', // 50MB max per video
            
            // Optional: Replace all video gallery items or add to existing
            'replace_video_gallery' => 'nullable|boolean',
            
            // Optional: Array of media IDs to delete from video gallery
            'delete_video_gallery_ids' => 'nullable|array',
            'delete_video_gallery_ids.*' => 'integer|exists:media,id',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => __('messages.Titleisrequired'),
            'title.*.string' => __('messages.Titlemustbeastring'),
            'title.*.max' => __('messages.Titlemaximumlength'),
            
            'body.required' => __('messages.Bodyisrequired'),
            'body.*.string' => __('messages.Bodymustbeastring'),
            
            'main_image.image' => __('messages.Mainimagemustsupportedformat'),
            'main_image.mimes' => __('messages.Mainimagemustsupportedformat'),
            'main_image.max' => __('messages.Mainimagetoobig'),
            
            'gallery.array' => __('messages.Gallerymustbeanarray'),
            'gallery.*.image' => __('messages.Galleryimagemustsupportedformat'),
            'gallery.*.mimes' => __('messages.Galleryimagemustsupportedformat'),
            'gallery.*.max' => __('messages.Galleryimagetoobig'),
            
            'delete_gallery_ids.array' => __('messages.Deletegalleryidsmustbeanarray'),
            'delete_gallery_ids.*.exists' => __('messages.Mediaidnotfound'),
            
            'video.file' => __('messages.Videomustsupportedformat'),
            'video.mimes' => __('messages.Videomustsupportedformat'),
            'video.max' => __('messages.Videotoobig'),
            
            'video_gallery.array' => __('messages.Videogallerymustbeanarray'),
            'video_gallery.*.file' => __('messages.Videogallerymustsupportedformat'),
            'video_gallery.*.mimes' => __('messages.Videogallerymustsupportedformat'),
            'video_gallery.*.max' => __('messages.Videogallerytoobig'),
            
            'delete_video_gallery_ids.array' => __('messages.Deletevideogalleryidsmustbeanarray'),
            'delete_video_gallery_ids.*.exists' => __('messages.Mediaidnotfound'),
        ];
    }
}
