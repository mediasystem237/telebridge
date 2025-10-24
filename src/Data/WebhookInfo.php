<?php

namespace Mbindi\Telebridge\Data;

class WebhookInfo
{
    public string $url;
    public bool $hasCustomCertificate;
    public int $pendingUpdateCount;
    public ?string $ipAddress;
    public ?int $lastErrorDate;
    public ?string $lastErrorMessage;
    public ?int $lastSynchronizationErrorDate;
    public ?int $maxConnections;
    public ?array $allowedUpdates;

    public function __construct(array $data)
    {
        $this->url = $data['url'];
        $this->hasCustomCertificate = $data['has_custom_certificate'];
        $this->pendingUpdateCount = $data['pending_update_count'];
        $this->ipAddress = $data['ip_address'] ?? null;
        $this->lastErrorDate = $data['last_error_date'] ?? null;
        $this->lastErrorMessage = $data['last_error_message'] ?? null;
        $this->lastSynchronizationErrorDate = $data['last_synchronization_error_date'] ?? null;
        $this->maxConnections = $data['max_connections'] ?? null;
        $this->allowedUpdates = $data['allowed_updates'] ?? null;
    }

    public static function fromResponse(array $response): ?self
    {
        if (!isset($response['result'])) {
            return null;
        }

        return new self($response['result']);
    }

    public function isConfigured(): bool
    {
        return !empty($this->url);
    }

    public function hasError(): bool
    {
        return $this->lastErrorDate !== null;
    }

    public function getLastErrorDateTime(): ?\DateTime
    {
        if (!$this->lastErrorDate) {
            return null;
        }

        return (new \DateTime())->setTimestamp($this->lastErrorDate);
    }

    public function hasPendingUpdates(): bool
    {
        return $this->pendingUpdateCount > 0;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'has_custom_certificate' => $this->hasCustomCertificate,
            'pending_update_count' => $this->pendingUpdateCount,
            'ip_address' => $this->ipAddress,
            'last_error_date' => $this->lastErrorDate,
            'last_error_message' => $this->lastErrorMessage,
            'last_synchronization_error_date' => $this->lastSynchronizationErrorDate,
            'max_connections' => $this->maxConnections,
            'allowed_updates' => $this->allowedUpdates,
        ];
    }
}

