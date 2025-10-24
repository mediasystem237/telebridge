<?php

namespace Mbindi\Telebridge\Data;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramFile
{
    public string $fileId;
    public string $fileUniqueId;
    public ?int $fileSize;
    public ?string $filePath;
    protected string $botToken;

    public function __construct(array $data, string $botToken)
    {
        $this->fileId = $data['file_id'];
        $this->fileUniqueId = $data['file_unique_id'];
        $this->fileSize = $data['file_size'] ?? null;
        $this->filePath = $data['file_path'] ?? null;
        $this->botToken = $botToken;
    }

    /**
     * Crée une instance depuis une réponse API Telegram
     */
    public static function fromTelegramResponse(array $response, string $botToken): ?self
    {
        if (!isset($response['result'])) {
            return null;
        }

        return new self($response['result'], $botToken);
    }

    /**
     * Récupère l'URL de téléchargement du fichier
     */
    public function getDownloadUrl(): ?string
    {
        if (!$this->filePath) {
            return null;
        }

        return "https://api.telegram.org/file/bot{$this->botToken}/{$this->filePath}";
    }

    /**
     * Télécharge le fichier localement
     * 
     * @param string|null $disk Disk Laravel Storage (null = défaut)
     * @param string|null $path Chemin de destination (null = génère auto)
     * @return string|null Chemin du fichier téléchargé ou null si erreur
     */
    public function download(?string $disk = null, ?string $path = null): ?string
    {
        $url = $this->getDownloadUrl();
        
        if (!$url) {
            return null;
        }

        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return null;
            }

            // Générer un chemin si non fourni
            if (!$path) {
                $extension = $this->getExtension();
                $path = 'telegram/' . date('Y/m/d') . '/' . $this->fileUniqueId . ($extension ? '.' . $extension : '');
            }

            // Sauvegarder le fichier
            Storage::disk($disk)->put($path, $response->body());

            return $path;

        } catch (\Exception $e) {
            \Log::error('Failed to download Telegram file', [
                'file_id' => $this->fileId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Tente de déterminer l'extension du fichier
     */
    public function getExtension(): ?string
    {
        if (!$this->filePath) {
            return null;
        }

        return pathinfo($this->filePath, PATHINFO_EXTENSION) ?: null;
    }

    /**
     * Récupère le MIME type approximatif
     */
    public function getMimeType(): ?string
    {
        $extension = $this->getExtension();
        
        if (!$extension) {
            return null;
        }

        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'ogg' => 'audio/ogg',
        ];

        return $mimeTypes[$extension] ?? null;
    }

    /**
     * Taille formatée lisible
     */
    public function getFormattedSize(): string
    {
        if (!$this->fileSize) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->fileSize;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Convertit en array
     */
    public function toArray(): array
    {
        return [
            'file_id' => $this->fileId,
            'file_unique_id' => $this->fileUniqueId,
            'file_size' => $this->fileSize,
            'file_path' => $this->filePath,
            'download_url' => $this->getDownloadUrl(),
            'extension' => $this->getExtension(),
            'mime_type' => $this->getMimeType(),
            'formatted_size' => $this->getFormattedSize(),
        ];
    }
}

