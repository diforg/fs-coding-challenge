<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'message',
        'origin',
        'is_read',
        'message_id', // ID externo da mensagem
        'status',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function channel()
    {
        return $this->throughContact()->hasChannel();
    }

    // Escopos úteis
    public function scopeOrigin($query, $origin)
    {
        return $query->where('origin', $origin);
    }

    public function scopeIncoming($query)
    {
        return $query->where('origin', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('origin', 'outgoing');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeChannel($query, $channelIdentifier)
    {
        return $query->whereHas('contact.channel', function ($q) use ($channelIdentifier) {
            $q->where('identifier', $channelIdentifier);
        });
    }

    // Status possíveis
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
}