<?php

declare(strict_types=1);

namespace App\Feature\TelegramNotify\Service;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

final class TelegramNotifyService
{
    private Telegram $client;

    public function __construct(
        string $apiKey,
        string $botName
    ) {
        $this->client = new Telegram($apiKey, $botName);
    }

    public function send(?int $userId, string $message): void
    {
        if ($userId === null) {
            return;
        }

        Request::sendMessage([
            'chat_id' => $userId,
            'text'    => $message,
        ]);
    }
}