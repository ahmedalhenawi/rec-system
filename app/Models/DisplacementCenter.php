<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisplacementCenter extends Model
{
    protected $fillable = ['name', 'type', 'governorate'];

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }
}
