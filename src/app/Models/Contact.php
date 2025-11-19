<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'name',
        'photo',
        'identifier', // telefone, username, etc
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    /**
     * Relação com o canal
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Relação com as mensagens
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Encontra ou cria um contato pelo identificador
     */
    public static function findOrCreate($channelId, $identifier, $name = null, $photo = null)
    {
        return static::firstOrCreate(
            [
                'channel_id' => $channelId,
                'identifier' => $identifier
            ],
            [
                'name' => $name ?? 'Contato ' . substr($identifier, -4),
                'photo' => $photo ?? 'https://placehold.co/150x150?text=' . urlencode($name),
                'metadata' => []
            ]
        );
    }

    /**
     * Obtém a última mensagem do contato
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)
                    ->latest('id');
    }

    /**
     * Obtém mensagens não lidas do contato
     */
    public function unreadMessages()
    {
        return $this->hasMany(Message::class)
                    ->where('is_read', false);
    }

    /**
     * Escopo para filtrar contatos por canal
     */
    public static function forChannel($channelName = null)
    {
        $query = self::with(['channel', 'lastMessage'])
            ->withCount(['unreadMessages as unread_messages_count' => function ($q) {
                $q->where('origin', 'received');
            }]);

        if ($channelName && $channelName !== 'all') {
            $channelId = Channel::where('name', $channelName)->value('id');
            if ($channelId) {
                $query->where('channel_id', $channelId);
            }
        }

        return $query->get();
    }

    /**
     * Escopo para buscar contatos do modal por canal
     */
    public static function modalContacts($channelName)
    {
        $query = self::with('channel');

        $channelId = Channel::where('name', $channelName)->value('id');
        if ($channelId) {
            $query->where('channel_id', $channelId);
        }

        return $query->get();
    }

}