<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifiedMobile extends Model
{
    protected $table = 'verified_mobiles';
    protected $primaryKey = 'mobile';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'mobile',
        'epic_no',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}
