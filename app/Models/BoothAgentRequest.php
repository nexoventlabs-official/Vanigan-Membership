<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoothAgentRequest extends Model
{
    protected $table = 'booth_agent_requests';
    public $timestamps = false;

    protected $fillable = [
        'ptc_code',
        'epic_no',
        'name',
        'mobile',
        'assembly',
        'photo_url',
        'status',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
        'source',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function generatedVoter()
    {
        return $this->belongsTo(GeneratedVoter::class, 'ptc_code', 'ptc_code');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'confirmed' => '<span class="badge badge-success">Confirmed</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
        ];
        
        return $badges[$this->status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    public function getSourceBadgeAttribute()
    {
        $badges = [
            'web' => '<span class="badge badge-primary">Web</span>',
            'whatsapp' => '<span class="badge badge-success">WhatsApp</span>',
        ];
        
        return $badges[$this->source] ?? '<span class="badge badge-secondary">Unknown</span>';
    }
}
