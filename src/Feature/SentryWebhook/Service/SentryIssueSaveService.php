<?php

declare(strict_types=1);

namespace App\Feature\SentryWebhook\Service;

use App\Entity\SentryIssue;
use Doctrine\ORM\EntityManagerInterface;

final class SentryIssueSaveService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly bool $isEnable,
        private readonly string $boardId,
        private readonly string $boardListId,
    ) {
    }

    public function save(mixed $content): void
    {
        if (!$this->isEnable) {
            return;
        }

        $issue = new SentryIssue(
            boardId: (int)$this->boardId,
            listId: (int)$this->boardListId,
            issue: $content
        );

        $this->entityManager->persist($issue);
        $this->entityManager->flush();
    }
}