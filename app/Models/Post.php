<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    // Define the attributes you want to be translatable
    public array $translatable = ['title', 'body'];

    // Fillable attributes
    protected $fillable = [
        'title', 
        'body',
        'slug'
    ];
}