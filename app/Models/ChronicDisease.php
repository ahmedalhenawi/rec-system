<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChronicDisease extends Model
{
    protected $guarded = [];

    public function person()
    {
         return $this->belongsTo(Person::class);
    }
}
