<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotifyUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifyUserRepository::class)]
class NotifyUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // notify when bugfixes are created
    #[ORM\Column]
    private bool $atBugfixCreated = false;

    // notify weekly by task time
    #[ORM\Column]
    private bool $atWeek = false;

    // notify 3 days before Deadline of the task
    #[ORM\Column]
    private bool $atThreeDays = false;

    #[ORM\Column]
    private bool $atTomorrow = false;

    #[ORM\Column]
    private bool $atToday = false;

    #[ORM\Column]
    private bool $atDeadline = false;

    // notify by deadlines after the day of delay each subsequent day
    #[ORM\Column]
    private bool $afterDeadlineByEveryDay = false;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $telegramUserId = null;

    #[ORM\Column(type: Types::BIGINT)]
    private int $boardId;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTelegramUserId(): ?int
    {
        return $this->telegramUserId;
    }

    public function isAtBugfixCreated(): bool
    {
        return $this->atBugfixCreated;
    }

    public function isAtWeek(): bool
    {
        return $this->atWeek;
    }

    public function isAtThreeDays(): bool
    {
        return $this->atThreeDays;
    }

    public function isAtTomorrow(): bool
    {
        return $this->atTomorrow;
    }

    public function isAtToday(): bool
    {
        return $this->atToday;
    }

    public function isAtDeadline(): bool
    {
        return $this->atDeadline;
    }

    public function isAfterDeadlineByEveryDay(): bool
    {
        return $this->afterDeadlineByEveryDay;
    }

    public function setAtBugfixCreated(bool $atBugfixCreated): void
    {
        $this->atBugfixCreated = $atBugfixCreated;
    }

    public function setAtWeek(bool $atWeek): void
    {
        $this->atWeek = $atWeek;
    }

    public function setAtThreeDays(bool $atThreeDays): void
    {
        $this->atThreeDays = $atThreeDays;
    }

    public function setAtTomorrow(bool $atTomorrow): void
    {
        $this->atTomorrow = $atTomorrow;
    }

    public function setAtToday(bool $atToday): void
    {
        $this->atToday = $atToday;
    }

    public function setAtDeadline(bool $atDeadline): void
    {
        $this->atDeadline = $atDeadline;
    }

    public function setAfterDeadlineByEveryDay(bool $afterDeadlineByEveryDay): void
    {
        $this->afterDeadlineByEveryDay = $afterDeadlineByEveryDay;
    }

    public function setTelegramUserId(?int $telegramUserId): void
    {
        $this->telegramUserId = $telegramUserId;
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function setBoardId(int $boardId): void
    {
        $this->boardId = $boardId;
    }
}