<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpSession extends Model
{
    protected $table = 'otp_sessions';
    protected $primaryKey = 'mobile';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mobile',
        'otp',
        'created_at',
        'verified',
        'purpose',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'created_at' => 'datetime',
    ];
}
