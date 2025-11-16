<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\WhatsappService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    public function __construct(
        private WhatsappService $whatsappService
    ) {}

    /**
     * Handle incoming message webhook
     */
    public function handleMessageReceived(Request $request): JsonResponse
    {
        try {
            // Process the webhook payload
            $result = $this->whatsappService->processMessage($request->all());

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Message processed successfully',
                    'message_id' => $result['message_id'] ?? null
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['error'] ?? 'Failed to process message'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }
}