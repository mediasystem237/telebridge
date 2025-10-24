<?php

if (!function_exists('telebridge')) {
    /**
     * Get the TeleBridge service instance.
     *
     * @return \Mbindi\Telebridge\Services\TelegramClient
     */
    function telebridge()
    {
        return app('telebridge');
    }
}
