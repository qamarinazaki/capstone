<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NinjavanData extends Model
{
    use HasFactory;

    protected $table = 'ninjavan_data';

    protected $fillable = [
        'tracking_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'pickup_code',
        'locker_number',
        'picked_up_at',
        // Add any other custom tracking columns you need to mass assign below
    ];

    /**
     * Relationship to the current assignment (if any)
     */
    public function currentAssignment()
    {
        return $this->hasOne(ParcelAssignment::class, 'parcel_id')->latestOfMany();
    }
}