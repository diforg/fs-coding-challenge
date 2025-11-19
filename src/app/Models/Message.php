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
        'message_id', // ID externo da mensagem
        'origin',
        'is_read',
        'status',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Relação com o contato
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Relação com o canal através do contato
     */
    public function channel()
    {
        return $this->throughContact()->hasChannel();
    }

    /**
     * Cria registro de mensagem recebida
     *
     * @param int $contact_id
     * @param string $message
     * @param string|null $message_id
     * @param array $metadata
     * @return Message
     */
    public static function receive($contact_id, $message, $message_id, $metadata = []): Message
    {
        return self::create([
            'contact_id' => $contact_id,
            'message' => $message,
            'message_id' => $message_id,
            'origin' => 'received',
            'is_read'    => 1,
            'status' => 'delivered',
            'metadata' => $metadata,
        ]);
    }

    /**
     * Marca mensagens como lidas para um contato
     *
     * @param int $contactId
     * @return int Número de mensagens atualizadas
     */
    public static function markAsRead($contactId)
    {
        return self::where('contact_id', $contactId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Envia uma mensagem (cria registro de mensagem enviada)
     */
    public static function send($contactId, $message)
    {
        return self::create([
            'contact_id' => $contactId,
            'message'    => $message,
            'origin'     => 'sent',
            'is_read'    => false,
            'status'     => 'pending',
        ]);
    }

    /**
     * Escopo para buscar mensagens de um contato
     */
    public static function forContact($contactId, $perPage = 20)
    {
        return self::where('contact_id', $contactId)
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Escopos para consultas
     */
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

    /** 
     * Constantes de status da mensagem
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_READ = 'read';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
}