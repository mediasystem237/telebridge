<?php

namespace Mbindi\Telebridge\Data;

class TelegramPhoto extends TelegramFile
{
    public int $width;
    public int $height;

    public function __construct(array $data, string $botToken)
    {
        parent::__construct($data, $botToken);
        
        $this->width = $data['width'] ?? 0;
        $this->height = $data['height'] ?? 0;
    }

    /**
     * Crée une instance depuis un tableau de photos Telegram
     * (Telegram envoie toujours plusieurs tailles)
     * 
     * @param array $photos Tableau de photos
     * @param string $botToken Token du bot
     * @param string $size 'largest', 'smallest', ou 'medium'
     */
    public static function fromPhotoArray(array $photos, string $botToken, string $size = 'largest'): ?self
    {
        if (empty($photos)) {
            return null;
        }

        $photo = match($size) {
            'smallest' => $photos[0],
            'largest' => end($photos),
            'medium' => $photos[count($photos) / 2] ?? $photos[0],
            default => end($photos),
        };

        return new self($photo, $botToken);
    }

    /**
     * Calcule le ratio d'aspect
     */
    public function getAspectRatio(): float
    {
        if ($this->height === 0) {
            return 0;
        }

        return $this->width / $this->height;
    }

    /**
     * Vérifie si la photo est en format portrait
     */
    public function isPortrait(): bool
    {
        return $this->height > $this->width;
    }

    /**
     * Vérifie si la photo est en format paysage
     */
    public function isLandscape(): bool
    {
        return $this->width > $this->height;
    }

    /**
     * Vérifie si la photo est carrée
     */
    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }

    /**
     * Dimensions formatées
     */
    public function getDimensions(): string
    {
        return "{$this->width}x{$this->height}";
    }

    /**
     * Convertit en array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'width' => $this->width,
            'height' => $this->height,
            'dimensions' => $this->getDimensions(),
            'aspect_ratio' => $this->getAspectRatio(),
            'orientation' => $this->isPortrait() ? 'portrait' : ($this->isLandscape() ? 'landscape' : 'square'),
        ]);
    }
}

