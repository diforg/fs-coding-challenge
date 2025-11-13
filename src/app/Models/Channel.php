<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['name', 'identifier', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function messages()
    {
        return $this->hasManyThrough(Message::class, Contact::class);
    }

    // Canais pré-definidos
    const CHANNEL_WHATSAPP = 'whatsapp';
    const CHANNEL_TELEGRAM = 'telegram';
    const CHANNEL_MESSENGER = 'messenger';
}