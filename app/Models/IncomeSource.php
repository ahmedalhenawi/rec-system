<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeSource extends Model
{
    protected $fillable = ['name', 'detail'];

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }
}
