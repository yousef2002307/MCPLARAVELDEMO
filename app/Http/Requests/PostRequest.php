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
        ];
    }
}
