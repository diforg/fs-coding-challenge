<?php

namespace Tests\Feature\Controllers\Webhook;

use App\Models\Channel;
use App\Services\MessengerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Exception;

uses(RefreshDatabase::class);

beforeEach(function () {
    Channel::factory()->create([
        'identifier' => 'messenger',
        'name' => 'WhatsApp'
    ]);

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

describe('MessengerWebhookController', function () {
    describe('handleMessageReceived()', function () {
        it('returns success response when message is processed successfully', function () {
            // Act
            $response = $this->postJson(route('webhook.messenger.received'), $this->validPayload);

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
            $mockService = $this->mock(MessengerService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andReturn([
                        'success' => false,
                        'error' => 'Invalid message format'
                    ]);
            });

            $payload = ['invalid' => 'payload'];

            // Act
            $response = $this->postJson(route('webhook.messenger.received'), $payload);

            // Assert
            $response->assertStatus(422)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid message format'
                ]);
        });

        it('returns internal server error when exception occurs', function () {
            // Arrange
            $mockService = $this->mock(MessengerService::class, function (MockInterface $mock) {
                $mock->shouldReceive('processMessage')
                    ->once()
                    ->andThrow(new Exception('Unexpected error'));
            });

            $payload = ['message' => 'test'];

            // Act
            $response = $this->postJson(route('webhook.messenger.received'), $payload);

            // Assert
            $response->assertStatus(500)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Internal server error'
                ]);
        });

        it('validates required webhook payload structure', function () {
            // Act - Send empty payload
            $response = $this->postJson(route('webhook.messenger.received'), []);

            // Assert - Should still process but might fail in service
            $response->assertStatus(422); // Or whatever status your service returns for invalid data
        });

        it('accepts valid webhook payload format', function () {
            // Act
            $response = $this->postJson(route('webhook.messenger.received'), $this->validPayload);

            // Assert
            $response->assertStatus(200);
        });        

    });
});