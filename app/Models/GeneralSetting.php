<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_title',
        'site_logo',
        'site_favicon',
        'email',
        'phone',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'business_start_date',
        'reuse_enabled',
    ];

    protected $casts = [
        'reuse_enabled' => 'boolean',
    ];

    /**
     * Whether the re-use (clone) feature is switched on. Off = legacy behaviour.
     */
    public static function reuseEnabled(): bool
    {
        return (bool) optional(static::first())->reuse_enabled;
    }

}
