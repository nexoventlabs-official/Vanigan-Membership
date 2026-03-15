<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedVoter extends Model
{
    protected $table = 'generated_voters';
    
    // Only use created_at, not updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'ptc_code',
        'AC_NO',
        'ASSEMBLY_NAME',
        'PART_NO',
        'SECTION_NO',
        'SLNOINPART',
        'C_HOUSE_NO',
        'C_HOUSE_NO_V1',
        'FM_NAME_EN',
        'LASTNAME_EN',
        'FM_NAME_V1',
        'LASTNAME_V1',
        'EPIC_NO',
        'NAME',
        'NAME_V1',
        'RLN_TYPE',
        'RLN_FM_NM_EN',
        'RLN_L_NM_EN',
        'RLN_FM_NM_V1',
        'RLN_L_NM_V1',
        'SEX',
        'AGE',
        'DOB',
        'MOBILE_NO',
        'card_url',
        'photo_url',
        'generated_at',
        'secret_pin',
        'referral_id',
        'referral_link',
        'referred_members_count',
        'referred_by_ptc',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'referred_members_count' => 'integer',
    ];

    /**
     * Get the referrer who referred this voter
     */
    public function referrer()
    {
        return $this->belongsTo(GeneratedVoter::class, 'referred_by_ptc', 'ptc_code');
    }

    /**
     * Get the members referred by this voter
     */
    public function referredMembers()
    {
        return $this->hasMany(GeneratedVoter::class, 'referred_by_ptc', 'ptc_code');
    }
}
