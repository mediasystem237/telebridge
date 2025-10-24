<?php

namespace Mbindi\Telebridge\Services;

interface TeleBridgeIntegration
{
    public function send($data);
    public function receive($payload);
}

class IntegrationManager
{
    protected static $integrations = [];

    public static function register(string $key, TeleBridgeIntegration $instance)
    {
        static::$integrations[$key] = $instance;
    }

    public static function get(string $key): ?TeleBridgeIntegration
    {
        return static::$integrations[$key] ?? null;
    }
}
