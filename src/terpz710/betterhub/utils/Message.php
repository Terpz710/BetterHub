<?php

declare(strict_types=1);

namespace terpz710\betterhub\utils;

use pocketmine\utils\TextFormat;

use terpz710\betterhub\Hub;

class Message {

    private string $message;

    public function __construct(string $msgKey, array|string|null $tags = null, array|string|null $replacements = null) {
        $msg = Hub::getInstance()->messages->get($msgKey);

        if ($tags !== null && $replacements !== null) {
            $tags = (array) $tags;
            $replacements = (array) $replacements;

            $msg = str_replace($tags, $replacements, $msg);
        }

        $this->message = TextFormat::colorize($msg);
    }

    public function __toString() : string{
        return $this->message;
    }
}