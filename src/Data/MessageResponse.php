<?php

namespace Mbindi\Telebridge\Data;

class MessageResponse
{
    public int $messageId;
    public int $date;
    public array $chat;
    public ?array $from;
    public ?string $text;
    public ?array $entities;

    public function __construct(array $data)
    {
        $this->messageId = $data['message_id'];
        $this->date = $data['date'];
        $this->chat = $data['chat'];
        $this->from = $data['from'] ?? null;
        $this->text = $data['text'] ?? null;
        $this->entities = $data['entities'] ?? null;
    }

    public static function fromResponse(array $response): ?self
    {
        if (!isset($response['result'])) {
            return null;
        }

        return new self($response['result']);
    }

    public function getChatId(): int
    {
        return $this->chat['id'];
    }

    public function getChatType(): string
    {
        return $this->chat['type'];
    }

    public function isPrivateChat(): bool
    {
        return $this->getChatType() === 'private';
    }

    public function isGroupChat(): bool
    {
        return in_array($this->getChatType(), ['group', 'supergroup']);
    }

    public function getFromId(): ?int
    {
        return $this->from['id'] ?? null;
    }

    public function getFromUsername(): ?string
    {
        return $this->from['username'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
            'date' => $this->date,
            'chat' => $this->chat,
            'from' => $this->from,
            'text' => $this->text,
            'entities' => $this->entities,
        ];
    }
}

