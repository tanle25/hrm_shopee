<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    function variants() {
        return $this->hasMany(Variant::class);
    }
    function images() {
        return $this->hasMany(Image::class);
    }
    function video() {
        return $this->hasOne(video::class);
    }
}
