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
});

describe('WhatsappService', function () {
    describe('processMessage()', function () {
        it('processes message successfully with valid payload', function () {
            // Arrange
            $payload = [
                'message' => [
                    'id' => 'wamid.123456',
                    'from' => [
                        'phone' => '5511999999999',
                        'name' => 'John Doe'
                    ],
                    'content' => [
                        'text' => 'Hello, this is a test message'
                    ]
                ]
            ];

            // Act
            $result = $this->service->processMessage($payload);

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
                'message' => 'Hello, this is a test message',
                'origin' => 'incoming',
                'message_id' => 'wamid.123456'
            ]);
        });

        it('creates new contact when contact does not exist', function () {
            // Arrange
            $payload = [
                'message' => [
                    'id' => 'wamid.123456',
                    'from' => [
                        'phone' => '5511888888888',
                        'name' => 'Jane Smith'
                    ],
                    'content' => [
                        'text' => 'New contact message'
                    ]
                ]
            ];

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

            $payload = [
                'message' => [
                    'id' => 'wamid.789012',
                    'from' => [
                        'phone' => '5511777777777',
                        'name' => 'Existing User'
                    ],
                    'content' => [
                        'text' => 'Message from existing contact'
                    ]
                ]
            ];

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
            // Arrange
            $payload = [
                'message' => [
                    'id' => 'wamid.123456',
                    'from' => [
                        'phone' => '5511666666666'
                        // name is missing
                    ],
                    'content' => [
                        'text' => 'Message without name'
                    ]
                ]
            ];

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue()
                ->and($result['message_id'])->not->toBeNull();

            $this->assertDatabaseHas('contacts', [
                'identifier' => '5511666666666',
                'name' => 'Contato 5511666666666'
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
            // Arrange
            $payload = [
                'message' => [
                    'id' => 'wamid.123456',
                    'from' => [
                        'phone' => '5511999999999',
                        'name' => 'John Doe'
                    ],
                    'content' => [
                        'text' => 'Hello World'
                    ]
                ]
            ];

            // Act
            $result = $this->service->processMessage($payload);

            // Assert
            expect($result)->toBeArray()
                ->and($result['success'])->toBeTrue();

            $this->assertDatabaseHas('contacts', [
                'identifier' => '5511999999999',
                'name' => 'John Doe'
            ]);

            $this->assertDatabaseHas('messages', [
                'message' => 'Hello World',
                'message_id' => 'wamid.123456'
            ]);
        });
    });
});