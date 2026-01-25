<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdditionSource extends Model
{
    protected $guarded = [];

    // اختياري: إذا أردت معرفة العائلات المرتبطة بهذا المصدر مستقبلاً
    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }
}
