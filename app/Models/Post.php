<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Post extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;



    // Define the attributes you want to be translatable
    public array $translatable = ['title', 'body'];

    // Fillable attributes
    protected $guarded = [];
        public function registerMediaCollections(): void
    {
        // صورة رئيسية واحدة
        $this->addMediaCollection('main_image')
            ->singleFile();

        // صور إضافية متعددة
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200);
    }

    
}