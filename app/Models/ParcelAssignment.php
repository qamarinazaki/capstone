<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParcelAssignment extends Model
{
    protected $fillable = [
        'parcel_id', 'locker_id', 'pickup_code', 'customer_phone',
        'assigned_at', 'picked_up_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
    ];

    public function parcel()
    {
        return $this->belongsTo(NinjavanData::class, 'parcel_id');
    }

    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }
}