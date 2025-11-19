<?php

namespace Tests\Feature\Controllers\Webhook;

use App\Models\Channel;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Exception;

uses(RefreshDatabase::class);

beforeEach(function () {
    Channel::factory()->create([
        'identifier' => 'telegram',
        'name' => 'WhatsApp'
    ]);

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

describe('TelegramWebhookController', function () {
    describe('handleMessageReceived()', function () {
        it('returns success response when message is processed successfully', function () {
            // Act
            $response = $this->postJson(route('webhook.telegram.received'), $this->validPayload);

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
            $mockService = $this->mock(TelegramService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andReturn([
                        'success' => false,
                        'error' => 'Invalid message format'
                    ]);
            });

            $payload = ['invalid' => 'payload'];

            // Act
            $response = $this->postJson(route('webhook.telegram.received'), $payload);

            // Assert
            $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid message format'
                ]);
        });

        it('returns internal server error when exception occurs', function () {
            // Arrange
            $mockService = $this->mock(TelegramService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andThrow(new Exception('Unexpected error'));
            });

            $payload = ['message' => 'test'];

            // Act
            $response = $this->postJson(route('webhook.telegram.received'), $payload);

            // Assert
            $response->assertStatus(500)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Internal server error'
                ]);
        });

        it('validates required webhook payload structure', function () {
            // Act - Send empty payload
            $response = $this->postJson(route('webhook.telegram.received'), []);

            // Assert - Should still process but might fail in service
            $response->assertStatus(422); // Or whatever status your service returns for invalid data
        });

        it('accepts valid webhook payload format', function () {
            // Act
            $response = $this->postJson(route('webhook.telegram.received'), $this->validPayload);

            // Assert
            $response->assertStatus(200);
        });        

    });
});