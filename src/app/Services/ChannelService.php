<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;

abstract class ChannelService
{
    protected string $channelIdentifier;

    /**
     * Envia mensagem pelo canal
     */
    abstract public function sendMessage(string $to, string $contactName, string $content): array;

    /**
     * Processa mensagem recebida do canal
     */
    abstract public function processIncomingMessage(array $data): Message;

    /**
     * Obtém o canal
     */
    protected function getChannel(): Channel
    {
        return Channel::where('identifier', $this->channelIdentifier)->firstOrFail();
    }

    /**
     * Encontra ou cria contato
     */
    protected function findOrCreateContact(string $identifier, string $name, string $photo): Contact
    {
        $channel = $this->getChannel();
        return Contact::findOrCreate($channel->id, $identifier, $name, $photo);
    }

    /**
     * Salva mensagem no banco
     */
    protected function saveMessage(array $data): Message
    {
        $contact = $this->findOrCreateContact(
            $data['contact_identifier'],
            $data['contact_name'] ?? null,
            $data['contact_photo'] ?? null
        );

        return Message::create([
            'contact_id' => $contact->id,
            'message' => $data['content'],
            'origin' => $data['origin'],
            'is_read' => $data['is_read'] ?? false,
            'message_id' => $data['message_id'] ?? null,
            'status' => $data['status'] ?? Message::STATUS_SENT,
            'metadata' => $data['metadata'] ?? []
        ]);
    }

    /**
     * Simula delay de rede
     */
    protected function simulateNetworkDelay(): void
    {
        usleep(rand(500000, 2000000)); // 0.5 a 2 segundos
    }
}