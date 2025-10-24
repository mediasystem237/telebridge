<?php

namespace Mbindi\Telebridge\Services;

class IntentDetector
{
    public function detect(string $text): array
    {
        // Placeholder implementation. This should be replaced with a more robust
        // system using the configured driver (e.g., regex, AI).
        $text = strtolower($text);

        if (str_contains($text, 'price') || str_contains($text, 'how much')) {
            return ['intent' => 'ask_price', 'confidence' => 0.9];
        }

        if (str_contains($text, 'contact') || str_contains($text, 'phone')) {
            return ['intent' => 'ask_contact', 'confidence' => 0.9];
        }

        return ['intent' => 'unknown', 'confidence' => 1.0];
    }
}
