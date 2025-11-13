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

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

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
                'name' => $name ?? 'Contato ' . $identifier,
                'photo' => $photo ?? '',
                'metadata' => []
            ]
        );
    }
}