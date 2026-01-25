<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdditionReason extends Model
{
    protected $guarded = [];

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }
}
