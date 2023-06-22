<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Service;

use App\Entity\BoardCardDetails;
use App\Feature\AutoNumerator\Dto\PrefixNumberContext;
use App\Repository\BoardCardDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;

final class NextNumberCardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BoardCardDetailsRepository $repository,
    ) {
    }

    public function getNextNumber(PrefixNumberContext $context, int $boardId): int
    {
        $cardDetail = $this->repository->findLastNumberByBoardId($boardId);

        $last = $cardDetail?->getLastCardNumber() ?? 0;

        foreach ($context->getNumberedCards() as $card) {
            $current = PrefixTemplateResolver::retrieveNumber($card->name, $context->getPrefix());

            if ($current > $last) {
                $last = $current;
            }
        }

        if ($cardDetail === null) {
            $cardDetail = $this->createEntity($boardId);
        }

        $cardDetail->setLastCardNumber($last);

        $this->save($cardDetail);

        return ++$last;
    }

    public function saveByNextNumber(int $nextNumber, int $boardId): void
    {
        $cardDetail = $this->repository->findLastNumberByBoardId($boardId);

        if ($cardDetail === null) {
            $cardDetail = $this->createEntity($boardId);
        }

        $cardDetail->setLastCardNumber($nextNumber - 1);

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