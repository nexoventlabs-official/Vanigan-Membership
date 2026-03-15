<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenerationStat extends Model
{
    protected $table = 'generation_stats';
    protected $primaryKey = 'epic_no';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'epic_no',
        'card_url',
        'photo_url',
        'last_generated',
        'auth_mobile',
        'count',
        'secret_pin',
    ];

    protected $casts = [
        'last_generated' => 'datetime',
        'count' => 'integer',
    ];
}
