<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Message;
use App\Models\Channel;

class WhatsappService
{
    /**
     * Process incoming message webhook
     */
    public function processMessage(array $payload): array
    {
        try {
            $channel = Channel::where('identifier', 'whatsapp')->first();

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
        $changes = $entry['changes'][0] ?? [];
        $value = $changes['value'] ?? [];
        
        $contacts = $value['contacts'][0] ?? [];
        $messages = $value['messages'][0] ?? [];

        return [
            'message_id' => $messages['id'] ?? null,
            'contact_identifier' => $contacts['wa_id'] ?? null,
            'contact_name' => $contacts['profile']['name'] ?? null,
            'message' => $messages['text']['body'] ?? null,
        ];
    }
}