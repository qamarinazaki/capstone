<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
   protected $fillable = [
    'locker_number',
    'esp_ip',
    'status',
    'door_closed',
    'parcel_present',
    'last_updated',
];

    protected $casts = [
        'door_closed' => 'boolean',
        'parcel_present' => 'boolean',
        'last_updated' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(ParcelAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(ParcelAssignment::class)->latestOfMany();
    }
}