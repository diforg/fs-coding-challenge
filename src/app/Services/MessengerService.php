<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Message;
use App\Models\Channel;

class MessengerService
{
    /**
     * Process incoming message webhook
     */
    public function processMessage(array $payload): array
    {
        try {
            $channel = Channel::where('identifier', 'messenger')->first();

            // Extract message data from payload
            $messageData = $this->extractMessageData($payload);

            // Find or create contact
            $contact = Contact::findOrCreate(
                $channel->id, 
                $messageData['contact_identifier'], 
                $messageData['contact_name']
            );

            // Create message record
            $message = Message::receive($contact->id, $messageData['message'], $messageData['message_id'], $messageData);

            return [
                'success' => true,
                'message_id' => $message->id,
                'contact_id' => $contact->id
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract and normalize message data from payload
     */
    private function extractMessageData(array $payload): array
    {
        $entry = $payload['entry'][0] ?? [];
        $messaging = $entry['messaging'][0] ?? [];
        $sender = $messaging['sender'] ?? [];
        $message = $messaging['message'] ?? [];

        return [
            'message_id' => $message['mid'] ?? null,
            'contact_identifier' => $sender['id'] ?? null,
            'contact_name' => null, // Não disponível no webhook inicial
            'message' => $message['text'] ?? null,
        ];
    }
}