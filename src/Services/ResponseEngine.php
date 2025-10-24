<?php

namespace Mbindi\Telebridge\Services;

use Mbindi\Telebridge\Services\KeyboardBuilder;

class ResponseEngine
{
    public function generate(array $intent): array
    {
        $response = [];

        switch ($intent['intent']) {
            case 'ask_price':
                $response['text'] = 'Our prices start at 100 USD. Would you like to see our plans?';
                $response['reply_markup'] = KeyboardBuilder::inline()
                    ->row([
                        KeyboardBuilder::inline()->button('View Plans', ['callback_data' => 'view_plans']),
                        KeyboardBuilder::inline()->button('Contact Sales', ['callback_data' => 'contact_sales']),
                    ])
                    ->build();
                break;
            case 'ask_contact':
                $response['text'] = 'You can contact us at support@example.com or call us at +123456789.';
                break;
            default:
                $response['text'] = 'I am sorry, I did not understand that. Can you rephrase?';
                break;
        }

        return $response;
    }
}
