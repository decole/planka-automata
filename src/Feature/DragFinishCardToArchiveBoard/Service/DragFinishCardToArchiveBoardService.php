<?php

declare(strict_types=1);

namespace App\Feature\DragFinishCardToArchiveBoard\Service;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use DateTimeImmutable;
use Planka\Bridge\PlankaClient;
use Planka\Bridge\Views\Dto\Card\CardDto;
use Psr\Log\LoggerInterface;

final class DragFinishCardToArchiveBoardService
{
    public function __construct(
        private readonly bool $isEnable,
        private readonly string $boardId,
        private readonly string $archiveBoardId,
        private readonly string $archiveBoardListId,
        private readonly string $targetBoardListId,
        private readonly int $daysBeforeTransfer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(PlankaApiIntegrator $integrator): void
    {
        if (!$this->isEnable) {
            return;
        }

        $targetDay = (new DateTimeImmutable())->modify("-{$this->daysBeforeTransfer} days");

        $client = $integrator->getClient();

        $dto = $client->board->get($this->boardId);

        foreach ($dto->included->cards as $card) {
            if ($card->listId === $this->targetBoardListId && $this->isNeedTransfer($card, $targetDay)) {
                $this->dragToArchive($card, $client);
            }
        }
    }

    private function dragToArchive(CardDto $card, PlankaClient $client): void
    {
        $this->logger->info('Drag finished card to archive', [
            'cardId' => $card->id,
            'oldBoardId' => $card->boardId,
            'oldListId' => $card->listId,
            'newBoardId' => $this->archiveBoardId,
            'newBoardListId' => $this->archiveBoardListId,
        ]);

        $card->boardId = $this->archiveBoardId;
        $card->listId = $this->archiveBoardListId;

        $client->card->update($card);

        sleep(1);
    }

    private function isNeedTransfer(CardDto $card, DateTimeImmutable $targetDay): bool
    {
        return $card->updatedAt === null || $targetDay->getTimestamp() >= $card->updatedAt->getTimestamp();
    }
}