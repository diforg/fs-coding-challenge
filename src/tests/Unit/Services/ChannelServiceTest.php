<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Services\ChannelService;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Mock concreto para testar a classe abstrata
class TestableChannelService extends ChannelService
{
    protected string $channelIdentifier = 'test-channel';

    public function sendMessage(string $to, string $contactName, string $content): array
    {
        $this->simulateNetworkDelay();
        
        return [
            'status' => 'success',
            'message_id' => uniqid('msg_', true),
            'to' => $to,
            'contact_name' => $contactName,
            'content' => $content
        ];
    }

    public function processIncomingMessage(array $data): Message
    {
        return $this->saveMessage($data);
    }

    // Expor o método protected para testes
    public function testSaveMessage(array $data): Message
    {
        return $this->saveMessage($data);
    }
}

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->channel = Channel::factory()->create([
        'identifier' => 'test-channel',
        'name' => 'Test Channel'
    ]);
    
    $this->channelService = new TestableChannelService();
});

describe('ChannelService', function () {
    describe('sendMessage()', function () {
        it('deve enviar mensagem com sucesso', function () {
            // Arrange
            $to = '5511999999999';
            $contactName = 'John Doe';
            $content = 'Hello, this is a test message!';

            // Act
            $result = $this->channelService->sendMessage($to, $contactName, $content);

            // Assert
            expect($result)->toBeArray()
                ->and($result['status'])->toBe('success')
                ->and($result['to'])->toBe($to)
                ->and($result['contact_name'])->toBe($contactName)
                ->and($result['content'])->toBe($content)
                ->and($result['message_id'])->toMatch('/^msg_/');
        });

        it('deve enviar mensagem com conteúdo vazio', function () {
            // Act
            $result = $this->channelService->sendMessage('5511888888888', 'Jane Doe', '');

            // Assert
            expect($result['status'])->toBe('success')
                ->and($result['content'])->toBe('');
        });

        it('deve enviar mensagem com caracteres especiais', function () {
            // Arrange
            $content = 'Mensagem com ç, á, é, í, ó, ú! @#$% 😀';

            // Act
            $result = $this->channelService->sendMessage('5511777777777', 'Maria José', $content);

            // Assert
            expect($result['content'])->toBe($content);
        });
    });

    describe('saveMessage()', function () {
        it('deve salvar mensagem com dados completos', function () {
            // Arrange
            $timestamp = time();
            $messageData = [
                'contact_identifier' => 'contact-123',
                'contact_name' => 'Test Contact',
                'contact_photo' => 'https://example.com/photo.jpg',
                'content' => 'This is a test message content',
                'origin' => 'incoming',
                'is_read' => true,
                'message_id' => 'external-msg-123',
                'status' => Message::STATUS_DELIVERED,
                'metadata' => ['key' => 'value', 'timestamp' => $timestamp]
            ];

            // Act
            $message = $this->channelService->testSaveMessage($messageData);

            // Assert
            expect($message)->toBeInstanceOf(Message::class)
                ->and($message->message)->toBe('This is a test message content')
                ->and($message->origin)->toBe('incoming')
                ->and($message->is_read)->toBeTrue()
                ->and($message->message_id)->toBe('external-msg-123')
                ->and($message->status)->toBe(Message::STATUS_DELIVERED)
                ->and($message->metadata)->toMatchArray(['key' => 'value', 'timestamp' => $timestamp])
                ->and($message->contact)->toBeInstanceOf(Contact::class)
                ->and($message->contact->identifier)->toBe('contact-123')
                ->and($message->contact->name)->toBe('Test Contact')
                ->and($message->contact->photo)->toBe('https://example.com/photo.jpg')
                ->and($message->contact->channel_id)->toBe($this->channel->id);
        });

        it('deve salvar mensagem com valores padrão', function () {
            // Arrange
            $messageData = [
                'contact_identifier' => 'contact-456',
                'content' => 'Simple message',
                'origin' => 'outgoing'
            ];

            // Act
            $message = $this->channelService->testSaveMessage($messageData);

            // Assert
            expect($message->is_read)->toBeFalse()
                ->and($message->message_id)->toBeNull()
                ->and($message->status)->toBe(Message::STATUS_SENT)
                ->and($message->metadata)->toBe([]);
        });

        it('deve reutilizar contato existente', function () {
            // Arrange
            $existingContact = Contact::factory()->create([
                'channel_id' => $this->channel->id,
                'identifier' => 'existing-contact',
                'name' => 'Original Name'
            ]);

            $messageData = [
                'contact_identifier' => 'existing-contact',
                'contact_name' => 'New Name Should Not Override',
                'content' => 'Test message',
                'origin' => 'incoming'
            ];

            // Act
            $message = $this->channelService->testSaveMessage($messageData);

            // Assert
            expect($message->contact->id)->toBe($existingContact->id)
                ->and($message->contact->name)->toBe('Original Name'); // Mantém o nome original
        });
    });

    describe('processIncomingMessage()', function () {
        it('deve processar mensagem recebida corretamente', function () {
            // Arrange
            $incomingData = [
                'contact_identifier' => 'incoming-contact-789',
                'contact_name' => 'Incoming User',
                'contact_photo' => 'https://example.com/incoming.jpg',
                'content' => 'Hello from incoming message!',
                'origin' => 'incoming',
                'is_read' => false,
                'message_id' => 'incoming-123',
                'status' => Message::STATUS_DELIVERED,
                'metadata' => ['source' => 'webhook']
            ];

            // Act
            $message = $this->channelService->processIncomingMessage($incomingData);

            // Assert
            expect($message)->toBeInstanceOf(Message::class)
                ->and($message->origin)->toBe('incoming')
                ->and($message->message)->toBe('Hello from incoming message!')
                ->and($message->status)->toBe(Message::STATUS_DELIVERED)
                ->and($message->contact->identifier)->toBe('incoming-contact-789')
                ->and($message->metadata)->toBe(['source' => 'webhook']);
        });

        it('deve processar mensagem com mínimo de dados', function () {
            // Arrange
            $incomingData = [
                'contact_identifier' => 'minimal-contact',
                'content' => 'Minimal message',
                'origin' => 'incoming'
            ];

            // Act
            $message = $this->channelService->processIncomingMessage($incomingData);

            // Assert
            expect($message->message)->toBe('Minimal message')
                ->and($message->origin)->toBe('incoming')
                ->and($message->contact->identifier)->toBe('minimal-contact');
        });
    });
});