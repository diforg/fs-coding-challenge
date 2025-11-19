<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Channel;
use App\Models\Contact;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->channel = Channel::factory()->create([
        'identifier' => 'whatsapp',
        'name' => 'Test Channel'
    ]);
    $this->service = app(WhatsappService::class);
    
    $this->validPayload = [
        'entry' => [
            [
                'changes' => [
                    [
                        'value' => [
                            'contacts' => [
                                [
                                    'profile' => ['name' => 'John Doe'],
                                    'wa_id' => '5511999999999'
                                ]
                            ],
                            'messages' => [
                                [
                                    'id' => 'wamid.123456',
                                    'text' => ['body' => 'Test message']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
});

describe('WhatsappService', function () {
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
                'identifier' => '5511999999999',
                'name' => 'John Doe'
            ]);

            $this->assertDatabaseHas('messages', [
                'contact_id' => $result['contact_id'],
                'message' => 'Test message',
                'message_id' => 'wamid.123456'
            ]);
        });

        it('creates new contact when contact does not exist', function () {
            // Arrange - Modifica apenas o telefone e nome para garantir novo contato
            $payload = array_merge_recursive([], $this->validPayload);
            $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] = '5511888888888';
            $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] = 'Jane Smith';
            $payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] = 'New contact message';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result['success'])->toBeTrue();
            $this->assertDatabaseCount('contacts', 1);
            $this->assertDatabaseHas('contacts', [
                'identifier' => '5511888888888',
                'name' => 'Jane Smith'
            ]);
        });

        it('uses existing contact when contact already exists', function () {
            // Arrange
            $contact = Contact::factory()->create([
                'channel_id' => $this->channel->id,
                'identifier' => '5511777777777',
                'name' => 'Existing User'
            ]);

            $payload = array_merge_recursive([], $this->validPayload);
            $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] = '5511777777777';
            $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] = 'Existing User';
            $payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] = 'Message from existing contact';

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
            // Arrange - Remove o campo name
            $payload = array_merge_recursive([], $this->validPayload);
            unset($payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name']);
            $payload['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] = '5511666666666';
            $payload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] = 'Message without name';

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue()
                ->and($result['message_id'])->not->toBeNull();

            $this->assertDatabaseHas('contacts', [
                'identifier' => '5511666666666',
                'name' => 'Contato ' . substr('5511666666666', -4)
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
                'identifier' => '5511999999999',
                'name' => 'John Doe'
            ]);

            $this->assertDatabaseHas('messages', [
                'message' => 'Test message',
                'message_id' => 'wamid.123456'
            ]);
        });
    });
});