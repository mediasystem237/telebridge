<?php

namespace Mbindi\Telebridge\Services;

class ResponseEngine
{
    public function generate(array $intent): string
    {
        // Placeholder implementation. This could be expanded to use database-driven responses.
        $responses = [
            'ask_price' => 'Our prices start at 100 USD.',
            'ask_contact' => 'You can contact us at support@example.com.',
            'unknown' => 'I am sorry, I did not understand that. Can you rephrase?',
        ];

        return $responses[$intent['intent']] ?? $responses['unknown'];
    }
}
