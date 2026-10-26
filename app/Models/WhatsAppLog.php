<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'user_id',
        'phone_number',
        'message_type',
        'message_body',
        'attachment_url',
        'status',
        'api_response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
