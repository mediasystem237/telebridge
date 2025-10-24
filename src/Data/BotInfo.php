<?php

namespace Mbindi\Telebridge\Data;

class BotInfo
{
    public int $id;
    public bool $isBot;
    public string $firstName;
    public ?string $lastName;
    public string $username;
    public ?string $languageCode;
    public ?bool $canJoinGroups;
    public ?bool $canReadAllGroupMessages;
    public ?bool $supportsInlineQueries;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->isBot = $data['is_bot'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'] ?? null;
        $this->username = $data['username'];
        $this->languageCode = $data['language_code'] ?? null;
        $this->canJoinGroups = $data['can_join_groups'] ?? null;
        $this->canReadAllGroupMessages = $data['can_read_all_group_messages'] ?? null;
        $this->supportsInlineQueries = $data['supports_inline_queries'] ?? null;
    }

    public static function fromResponse(array $response): ?self
    {
        if (!isset($response['result'])) {
            return null;
        }

        return new self($response['result']);
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . ($this->lastName ?? ''));
    }

    public function getMention(): string
    {
        return '@' . $this->username;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'is_bot' => $this->isBot,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'username' => $this->username,
            'language_code' => $this->languageCode,
            'can_join_groups' => $this->canJoinGroups,
            'can_read_all_group_messages' => $this->canReadAllGroupMessages,
            'supports_inline_queries' => $this->supportsInlineQueries,
        ];
    }
}

