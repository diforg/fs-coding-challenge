<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Message;
use App\Models\Channel;

class TelegramService
{
    /**
     * Process incoming message webhook
     */
    public function processMessage(array $payload): array
    {
        try {
            $channel = Channel::where('identifier', 'telegram')->first();

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
        $message = $payload['message'] ?? [];
        $from = $message['from'] ?? [];

        return [
            'message_id' => $message['message_id'] ?? null,
            'contact_identifier' => $from['id'] ?? null,
            'contact_name' => $from['first_name'] ?? null,
            'message' => $message['text'] ?? null,
        ];
    }
}