<?php

namespace Mbindi\Telebridge\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Mbindi\Telebridge\Models\TelegramBot;
use Mbindi\Telebridge\Services\MessageRouter;

class TeleBridgeController extends Controller
{
    protected $messageRouter;

    public function __construct(MessageRouter $messageRouter)
    {
        $this->messageRouter = $messageRouter;
    }

    public function handleWebhook(Request $request, string $bot_token)
    {
        Log::info('TeleBridge Webhook Received for bot token: ' . $bot_token, $request->all());

        $bot = TelegramBot::where('token', $bot_token)->first();

        if (!$bot) {
            Log::warning('TeleBridge: Bot not found for token: ' . $bot_token);
            return response()->json(['status' => 'error', 'message' => 'Bot not found'], 404);
        }

        $this->messageRouter->handleUpdate($bot, $request->all());

        return response()->json(['status' => 'success']);
    }
}
