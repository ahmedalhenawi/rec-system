<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Person extends Model
{
    protected $guarded = [];
    protected $table = 'persons';
    // مهم جداً لـ Filament ليتعامل مع التواريخ والحقول المنطقية
    protected $casts = [
        'dob' => 'date',
        'has_chronic_disease' => 'boolean',
        'has_disability' => 'boolean',
    ];

    // Accessor لحساب العمر تلقائياً
    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->dob)->age;
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function chronicDiseases(): HasMany
    {
        return $this->hasMany(ChronicDisease::class);
    }

    public function disabilities(): HasMany
    {
        return $this->hasMany(Disability::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }
}
