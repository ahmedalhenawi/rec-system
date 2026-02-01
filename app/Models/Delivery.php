<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $guarded = [];

    protected $casts = [
        'received_at' => 'datetime',
    ];


    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
