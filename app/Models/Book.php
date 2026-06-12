<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
    'title',
    'description',
    'book_url',
    'cover_image_url',
    'material_id',
];
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
