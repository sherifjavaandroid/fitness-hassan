<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CategoryDiet extends Model implements HasMedia

{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [ 'title','status' ];
}