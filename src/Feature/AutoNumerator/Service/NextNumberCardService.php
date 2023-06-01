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
        private readonly int $boardId
    ) {
    }

    public function getNextNumber(PrefixNumberContext $context): int
    {
        $cardDetail = $this->repository->findLastNumberByBoardId($this->boardId);

        $last = $cardDetail?->getLastCardNumber() ?? 0;

        foreach ($context->getNumberedCards() as $card) {
            $current = PrefixTemplateResolver::retrieveNumber($card->name, $context->getPrefix());

            if ($current > $last) {
                $last = $current;
            }
        }

        $this->saveLastNumber($cardDetail, $last);

        return ++$last;
    }

    private function saveLastNumber(?BoardCardDetails $cardDetail, int $last): void
    {
        if ($cardDetail === null) {
            $cardDetail = $this->createEntity();
        }

        $cardDetail->setLastCardNumber($last);

        $this->entityManager->persist($cardDetail);
        $this->entityManager->flush();
    }

    public function saveByNextNumber(int $nextNumber): void
    {
        $cardDetail = $this->repository->findLastNumberByBoardId($this->boardId);

        if ($cardDetail === null) {
            $cardDetail = $this->createEntity();
        }

        $last = $nextNumber - 1;

        $this->saveLastNumber($cardDetail, $last);
    }

    public function createEntity(): BoardCardDetails
    {
        $cardDetail = new BoardCardDetails();
        $cardDetail->setBoardId($this->boardId);

        return $cardDetail;
    }
}