<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SentryIssueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity(repositoryClass: SentryIssueRepository::class)]
#[Index(columns: ["is_created"], name: "is_created_idx")]
class SentryIssue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $boardId;

    #[ORM\Column(type: 'bigint')]
    private ?int $listId;

    #[ORM\Column(nullable: true)]
    private ?int $cardNumber = null;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $cardId = null;

    #[ORM\Column(type: 'json')]
    private array $issue;

    #[ORM\Column]
    private bool $isCreated = false;

    public function __construct(int $boardId, int $listId, array $issue)
    {
        $this->boardId = $boardId;
        $this->listId = $listId;
        $this->issue = $issue;
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function getCardNumber(): int
    {
        return $this->cardNumber;
    }

    public function setCardNumber(int $number): int
    {
        return $this->cardNumber = $number;
    }

    public function getIssue(): array
    {
        return $this->issue;
    }

    public function isCreated(): bool
    {
        return $this->isCreated;
    }

    public function setIsCreated(bool $state): void
    {
        $this->isCreated = $state;
    }

    public function getListId(): ?int
    {
        return $this->listId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCardId(int $cardId): void
    {
        $this->cardId = $cardId;
    }
}