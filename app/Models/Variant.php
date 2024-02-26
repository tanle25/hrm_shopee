<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    function options() {
        return $this->hasMany(Option::class);
    }

    function option() {
        return $this->hasOne(Option::class);
    }

    function models() {
        return $this->belongsToMany(Option::class);
    }
}
