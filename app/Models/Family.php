<?php
namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Family extends Model
{
//    use BelongsToUser;
    protected $guarded = [];

    // علاقة لجلب رب الأسرة فقط (الحل الذي اخترناه)
    public function breadwinner(): HasOne
    {
        return $this->hasOne(Person::class)->where('relation', 'head');
    }

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Person::class);
    }


    public function incomeSource(): BelongsTo
    {
        return $this->belongsTo(IncomeSource::class);
    }

    public function displacementCenter(): BelongsTo
    {
        return $this->belongsTo(DisplacementCenter::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Get only the numeric part of the last code
            $lastRecord = static::latest('id')->first();
            $nextNumber = $lastRecord ? ($lastRecord->id + 1) : 1;

            // Use str_pad if you want leading zeros (e.g., FAM-001)
            $model->family_code = 'FAM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $model->user_id = auth()->id();
        });

//        static::updating(function ($model) {
//            $model->user_id = auth()->id();
//        });


        static::addGlobalScope('user_filter', function (Builder $builder) {
            // 1. التأكد من أن هناك مستخدم مسجل دخول (لتجنب الأخطاء في التنبيهات أو الـ Console)
            if (Auth::check()) {
                $user = Auth::user();

                // 2. التحقق مما إذا كان المستخدم يملك صلاحية super_admin
                // ملاحظة: افترضنا أنك تستخدم Spatie Permission أو ميثود hasRole
                if (! $user->hasRole('super_admin')) {
                    $builder->where('user_id', $user->id);
                }
            }
        });





    }

    public function additionSource(): BelongsTo
    {
        return $this->belongsTo(AdditionSource::class);
    }

    public function additionReason(): BelongsTo
    {
        return $this->belongsTo(AdditionReason::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
