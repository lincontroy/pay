<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'destination', 'conversation', 'status',
        'mobile','email', 'ride_type', 'source_latitude',
        'source_longitude', 'source_name', 'received_source', 
        'destination_latitude', 'destination_longitude', 'payment_type', 'paynot',
        'destination_name', 'received_destination', 'ride_id',
        'driver_id',
    ];
}
