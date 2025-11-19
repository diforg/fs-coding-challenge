<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Channel;
use App\Models\Contact;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->channel = Channel::factory()->create([
        'identifier' => 'telegram',
        'name' => 'Test Channel'
    ]);
    $this->service = app(TelegramService::class);
    
    // Payload padrão que funciona para a maioria dos testes
    $this->validPayload = [
        'message' => [
            'message_id' => 123,
            'from' => [
                'id' => 123456789,
                'first_name' => 'John Doe'
            ],
            'text' => 'Test message'
        ]
    ];
});

describe('TelegramService', function () {
    describe('processMessage()', function () {
        it('processes message successfully with valid payload', function () {
            // Act
            $result = $this->service->processMessage($this->validPayload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue()
                ->and($result['message_id'])->not->toBeNull()
                ->and($result['contact_id'])->not->toBeNull();

            // Verify database records
            $this->assertDatabaseHas('contacts', [
                'channel_id' => $this->channel->id,
                'identifier' => '123456789',
                'name' => 'John Doe'
            ]);

            $this->assertDatabaseHas('messages', [
                'contact_id' => $result['contact_id'],
                'message' => 'Test message',
                'origin' => 'incoming',
                'message_id' => '123'
            ]);
        });

        it('creates new contact when contact does not exist', function () {
            // Arrange - Modifica apenas o ID e nome para garantir novo contato
            $payload = array_merge_recursive([], $this->validPayload);
            $payload['message']['from']['id'] = 987654321;
            $payload['message']['from']['first_name'] = 'Jane Smith';
            $payload['message']['text'] = 'New contact message';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result['success'])->toBeTrue();
            $this->assertDatabaseCount('contacts', 1);
            $this->assertDatabaseHas('contacts', [
                'identifier' => '987654321',
                'name' => 'Jane Smith'
            ]);
        });

        it('uses existing contact when contact already exists', function () {
            // Arrange
            $contact = Contact::factory()->create([
                'channel_id' => $this->channel->id,
                'identifier' => '555555555',
                'name' => 'Existing User'
            ]);

            $payload = array_merge_recursive([], $this->validPayload);
            $payload['message']['from']['id'] = 555555555;
            $payload['message']['from']['first_name'] = 'Existing User';
            $payload['message']['text'] = 'Message from existing contact';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result['success'])->toBeTrue();
            $this->assertDatabaseCount('contacts', 1); // Não deve criar novo contato
            $this->assertDatabaseHas('messages', [
                'contact_id' => $contact->id,
                'message' => 'Message from existing contact'
            ]);
        });

        it('handles payload with missing optional fields', function () {
            // Arrange - Remove o campo first_name
            $payload = array_merge_recursive([], $this->validPayload);
            unset($payload['message']['from']['first_name']);
            $payload['message']['from']['id'] = 666666666;
            $payload['message']['text'] = 'Message without first name';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue()
                ->and($result['message_id'])->not->toBeNull();

            $this->assertDatabaseHas('contacts', [
                'identifier' => '666666666',
                'name' => 'Contato 666666666'
            ]);
        });

        it('handles malformed payload gracefully', function () {
            // Arrange
            $payload = [
                'invalid' => 'structure'
            ];

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeFalse()
                ->and($result['error'])->not->toBeNull();
        });

        it('extracts message data correctly from payload', function () {
            // Act
            $result = $this->service->processMessage($this->validPayload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue();

            $this->assertDatabaseHas('contacts', [
                'identifier' => '123456789',
                'name' => 'John Doe'
            ]);

            $this->assertDatabaseHas('messages', [
                'message' => 'Test message',
                'message_id' => '123'
            ]);
        });
    });
});