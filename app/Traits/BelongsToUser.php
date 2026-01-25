<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    public static function bootBelongsToUser()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });

        static::addGlobalScope('mine', function (Builder $builder) {
            if (Auth::check() && !auth()->user()->hasRole('super_admin')) {
                $builder->where('user_id', Auth::id());
            }
        });
    }
}
