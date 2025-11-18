<?php

namespace Tests\Feature\Controllers\Webhook;

use App\Models\Channel;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Exception;

uses(RefreshDatabase::class);

beforeEach(function () {
    Channel::factory()->create([
        'identifier' => 'whatsapp',
        'name' => 'WhatsApp'
    ]);
});

describe('WhatsappWebhookController', function () {
    describe('handleMessageReceived()', function () {
        it('returns success response when message is processed successfully', function () {
            // Arrange
            $payload = [
                'message' => [
                    'id' => 'wamid.123456',
                    'from' => [
                        'phone' => '5511999999999',
                        'name' => 'John Doe'
                    ],
                    'content' => [
                        'text' => 'Test message'
                    ]
                ]
            ];

            // Act
            $response = $this->postJson(route('webhook.whatsapp.received'), $payload);

            // Assert
            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Message processed successfully'
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'message_id'
                ]);
        });

        it('returns error response when service fails to process message', function () {
            // Arrange
            $mockService = $this->mock(WhatsappService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andReturn([
                        'success' => false,
                        'error' => 'Invalid message format'
                    ]);
            });

            $payload = ['invalid' => 'payload'];

            // Act
            $response = $this->postJson(route('webhook.whatsapp.received'), $payload);

            // Assert
            $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid message format'
                ]);
        });

        it('returns internal server error when exception occurs', function () {
            // Arrange
            $mockService = $this->mock(WhatsappService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andThrow(new Exception('Unexpected error'));
            });

            $payload = ['message' => 'test'];

            // Act
            $response = $this->postJson(route('webhook.whatsapp.received'), $payload);

            // Assert
            $response->assertStatus(500)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Internal server error'
                ]);
        });

        it('validates required webhook payload structure', function () {
            // Act - Send empty payload
            $response = $this->postJson(route('webhook.whatsapp.received'), []);

            // Assert - Should still process but might fail in service
            $response->assertStatus(422); // Or whatever status your service returns for invalid data
        });

        it('accepts valid webhook payload format', function () {
            // Arrange
            $validPayload = [
                'message' => [
                    'id' => 'wamid.HBgNNTUxMTk5OTk5OTk5NRUCABIYFDNGRkI3MkM5QzZCNTc0M0IxN0Y4EQ==',
                    'from' => [
                        'phone' => '5511999999999',
                        'name' => 'Test User'
                    ],
                    'timestamp' => '1704064800',
                    'type' => 'text',
                    'content' => [
                        'text' => 'This is a test message from WhatsApp'
                    ]
                ]
            ];

            // Act
            $response = $this->postJson(route('webhook.whatsapp.received'), $validPayload);

            // Assert
            $response->assertStatus(200);
        });        

    });
});