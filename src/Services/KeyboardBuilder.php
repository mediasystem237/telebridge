<?php

namespace Mbindi\Telebridge\Services;

class KeyboardBuilder
{
    protected $type; // 'inline' or 'reply'
    protected $buttons = [];

    public function __construct(string $type = 'inline')
    {
        if (!in_array($type, ['inline', 'reply'])) {
            throw new \InvalidArgumentException('Keyboard type must be "inline" or "reply".');
        }
        $this->type = $type;
    }

    public static function inline(): self
    {
        return new self('inline');
    }

    public static function reply(): self
    {
        return new self('reply');
    }

    public function row(array $buttons): self
    {
        $this->buttons[] = $buttons;
        return $this;
    }

    public function button(string $text, array $options = []): array
    {
        $button = ['text' => $text];
        if ($this->type === 'inline') {
            if (isset($options['url'])) {
                $button['url'] = $options['url'];
            } elseif (isset($options['callback_data'])) {
                $button['callback_data'] = $options['callback_data'];
            }
            // Add other inline button types as needed (login_url, switch_inline_query, etc.)
        } elseif ($this->type === 'reply') {
            if (isset($options['request_contact'])) {
                $button['request_contact'] = true;
            } elseif (isset($options['request_location'])) {
                $button['request_location'] = true;
            }
            // Add other reply button types as needed
        }
        return $button;
    }

    public function build(): array
    {
        if ($this->type === 'inline') {
            return ['inline_keyboard' => $this->buttons];
        } elseif ($this->type === 'reply') {
            return ['keyboard' => $this->buttons, 'resize_keyboard' => true, 'one_time_keyboard' => false];
        }
        return []; // Should not happen
    }
}
