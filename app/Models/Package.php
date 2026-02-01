<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['package_code', 'name', 'type', 'unit', 'target_quantity', 'status', 'notes'];

    // علاقة المستفيدين
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    // حساب نسبة الإنجاز تلقائياً
    public function getProgressAttribute()
    {
        if ($this->target_quantity <= 0) return 0;
        return ($this->deliveries()->count() / $this->target_quantity) * 100;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // جلب آخر معرف مسجل
            $lastId = static::max('id') ?? 0;
            $nextNumber = $lastId + 1;

            // توليد الكود بتنسيق: PKG-00001
            $model->package_code = 'PKG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        });
    }
}
