<?php

namespace Mbindi\Telebridge\Notifications;

class TelegramMessage
{
    public string $text = '';
    public ?string $parseMode = 'Markdown';
    public ?array $keyboard = null;
    public ?string $photo = null;
    public ?string $document = null;
    public bool $disableWebPagePreview = false;

    /**
     * Crée une nouvelle instance
     */
    public static function create(string $text = ''): self
    {
        $instance = new self();
        $instance->text = $text;
        return $instance;
    }

    /**
     * Définit le texte
     */
    public function content(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Définit le mode de parsing
     */
    public function parseMode(?string $mode): self
    {
        $this->parseMode = $mode;
        return $this;
    }

    /**
     * Mode Markdown
     */
    public function markdown(): self
    {
        $this->parseMode = 'Markdown';
        return $this;
    }

    /**
     * Mode HTML
     */
    public function html(): self
    {
        $this->parseMode = 'HTML';
        return $this;
    }

    /**
     * Ajoute un clavier
     */
    public function keyboard(array $keyboard): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    /**
     * Ajoute une photo
     */
    public function photo(string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * Ajoute un document
     */
    public function document(string $document): self
    {
        $this->document = $document;
        return $this;
    }

    /**
     * Désactive l'aperçu des liens
     */
    public function withoutPreview(): self
    {
        $this->disableWebPagePreview = true;
        return $this;
    }

    /**
     * Bouton (helper pour clavier simple)
     */
    public function button(string $text, string $url): self
    {
        $this->keyboard = [
            'inline_keyboard' => [[
                ['text' => $text, 'url' => $url]
            ]]
        ];
        return $this;
    }

    /**
     * Boutons (helper pour plusieurs boutons)
     */
    public function buttons(array $buttons): self
    {
        $keyboard = [];
        
        foreach ($buttons as $row) {
            $keyboardRow = [];
            foreach ($row as $text => $urlOrCallback) {
                if (filter_var($urlOrCallback, FILTER_VALIDATE_URL)) {
                    $keyboardRow[] = ['text' => $text, 'url' => $urlOrCallback];
                } else {
                    $keyboardRow[] = ['text' => $text, 'callback_data' => $urlOrCallback];
                }
            }
            $keyboard[] = $keyboardRow;
        }

        $this->keyboard = ['inline_keyboard' => $keyboard];
        return $this;
    }
}

