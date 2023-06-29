<?php

declare(strict_types=1);

namespace App\Entity;

use App\Feature\DeadLineNotify\Enum\NotifyTypeEnum;
use App\Repository\NotifyEventLogRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: NotifyEventLogRepository::class)]
class NotifyEventLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private int $boardId;

    #[ORM\Column(type: Types::BIGINT)]
    private int $cardId;

    #[ORM\Column(type: Types::BIGINT)]
    private int $userId;

    #[ORM\Column(type: Types::INTEGER, enumType: NotifyTypeEnum::class)]
    private NotifyTypeEnum $type;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $userId,
        int $boardId,
        int $cardId,
        NotifyTypeEnum $type
    ) {
        $this->userId = $userId;
        $this->boardId = $boardId;
        $this->cardId = $cardId;
        $this->type = $type;

        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setUpdated(): void
    {
        $this->updatedAt->modify("now");
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCardId(): int
    {
        return $this->cardId;
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getType(): NotifyTypeEnum
    {
        return $this->type;
    }
}