<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Service;

use App\Entity\BoardCardDetails;
use App\Repository\BoardCardDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Planka\Bridge\Views\Dto\Board\BoardDto;

final class NextNumberCardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BoardCardDetailsRepository $repository,
    ) {
    }

    public function getMaxNumberByBoardDto(BoardDto $board, string $cardPrefix): int
    {
        $max = 0;

        foreach ($board->included->cards as $card) {
            $number = PrefixTemplateResolver::retrieveNumber($card->name, $cardPrefix);

            if ($number > $max) {
                $max = $number;
            }
        }

        return $max;
    }

    public function getSavedNumber(string $boardId): int
    {
        $entity = $this->repository->findLastNumberByBoardId((int)$boardId);

        if ($entity === null) {
            return 0;
        }

        return $entity->getLastCardNumber() ?? 0;
    }

    public function saveMaxNumber(int $number, int $boardId): void
    {
        $cardDetail = $this->repository->findLastNumberByBoardId($boardId);

        if ($cardDetail === null) {
            $cardDetail = $this->createEntity($boardId);
        }

        $cardDetail->setLastCardNumber($number);

        $this->save($cardDetail);
    }

    public function save(BoardCardDetails $cardDetail): void
    {
        $this->entityManager->persist($cardDetail);
        $this->entityManager->flush();
    }

    private function createEntity(int $boardId): BoardCardDetails
    {
        $cardDetail = new BoardCardDetails();
        $cardDetail->setBoardId($boardId);

        return $cardDetail;
    }
}