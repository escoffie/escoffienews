<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'category',
        'channel',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
