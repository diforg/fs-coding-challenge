<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Channel;
use App\Models\Contact;
use App\Services\MessengerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->channel = Channel::factory()->create([
        'identifier' => 'messenger',
        'name' => 'Test Channel'
    ]);
    $this->service = app(MessengerService::class);
    
    // Payload padrão que funciona para a maioria dos testes
    $this->validPayload = [
        'entry' => [
            [
                'messaging' => [
                    [
                        'sender' => [
                            'id' => '12345678901234567'
                        ],
                        'message' => [
                            'mid' => 'mid.123456789012345:abcdef1234567890',
                            'text' => 'Test message'
                        ]
                    ]
                ]
            ]
        ]
    ];
});

describe('MessengerService', function () {
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
                'identifier' => '12345678901234567',
                'name' => 'Contato ' . substr('12345678901234567', -4)
            ]);

            $this->assertDatabaseHas('messages', [
                'contact_id' => $result['contact_id'],
                'message' => 'Test message',
                'message_id' => 'mid.123456789012345:abcdef1234567890',
            ]);
        });

        it('creates new contact when contact does not exist', function () {
            // Arrange - Modifica apenas o ID do sender para garantir novo contato
            $payload = array_merge_recursive([], $this->validPayload);
            $payload['entry'][0]['messaging'][0]['sender']['id'] = '98765432109876543';
            $payload['entry'][0]['messaging'][0]['message']['text'] = 'New contact message';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result['success'])->toBeTrue();
            $this->assertDatabaseCount('contacts', 1);
            $this->assertDatabaseHas('contacts', [
                'identifier' => '98765432109876543',
                'name' => 'Contato ' . substr('98765432109876543', -4)
            ]);
        });

        it('uses existing contact when contact already exists', function () {
            // Arrange
            $contact = Contact::factory()->create([
                'channel_id' => $this->channel->id,
                'identifier' => '55555555555555555',
                'name' => 'Existing User'
            ]);

            $payload = array_merge_recursive([], $this->validPayload);
            $payload['entry'][0]['messaging'][0]['sender']['id'] = '55555555555555555';
            $payload['entry'][0]['messaging'][0]['message']['text'] = 'Message from existing contact';

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
                'identifier' => '12345678901234567',
                'name' => 'Contato ' . substr('12345678901234567', -4)
            ]);

            $this->assertDatabaseHas('messages', [
                'message' => 'Test message',
                'message_id' => 'mid.123456789012345:abcdef1234567890'
            ]);
        });
    });
});