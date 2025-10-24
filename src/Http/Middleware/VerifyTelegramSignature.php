<?php

namespace Mbindi\Telebridge\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyTelegramSignature
{
    public function handle(Request $request, Closure $next)
    {
        $secretToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
        $configuredSecret = config('telebridge.webhook.secret_token');

        if (empty($configuredSecret)) {
            // If no secret is configured, skip verification
            return $next($request);
        }

        if ($secretToken !== $configuredSecret) {
            abort(403, 'Invalid secret token.');
        }

        return $next($request);
    }
}
