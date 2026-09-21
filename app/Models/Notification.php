<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'is_read_by_admin',
        'is_for_admin',
        'title',
        'message',
        'data',
        'is_read',
        'route',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    // Optional: relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update(['is_read' => 1]);
        }
    }

}
