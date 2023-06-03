<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BoardCardDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardCardDetailsRepository::class)]
class BoardCardDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $boardId = null;

    #[ORM\Column]
    private ?int $lastCardNumber = null;

    public function getLastCardNumber(): ?int
    {
        return $this->lastCardNumber;
    }

    public function setLastCardNumber(?int $lastCardNumber): void
    {
        $this->lastCardNumber = $lastCardNumber;
    }

    public function getBoardId(): ?int
    {
        return $this->boardId;
    }

    public function setBoardId(?int $boardId): void
    {
        $this->boardId = $boardId;
    }

    public function getId(): int
    {
        return $this->id;
    }
}