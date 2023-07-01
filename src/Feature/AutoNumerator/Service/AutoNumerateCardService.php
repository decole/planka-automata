<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Service;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use Planka\Bridge\PlankaClient;
use Planka\Bridge\Views\Dto\Board\BoardDto;
use Planka\Bridge\Views\Dto\Card\CardDto;
use Psr\Log\LoggerInterface;

final class AutoNumerateCardService
{
    private const TEMPLATE_CARD_NAME = '%s %s';

    public function __construct(
        private readonly NextNumberCardService $maxNumberService,
        private readonly string                $numerateLabelId,
        private readonly string                $bugfixLabelId,
        private readonly string                $cardPrefix,
        private readonly string                $boardId,
        private readonly bool                  $isEnable,
        private readonly LoggerInterface       $logger
    ) {
    }

    public function handle(PlankaApiIntegrator $integrator): void
    {
        if (!$this->isEnable) {
            return;
        }

        $client = $integrator->getClient();

        $board = $client->board->get($this->boardId);

        $this->numerateCards($board, $client);

        $board = $client->board->get($this->boardId);

        $this->labelingCards($board, $client);
    }

    private function numerateCards(BoardDto $board, PlankaClient $client): void
    {
        $maxNumber = $this->getMaxNumber($board);

        foreach ($this->createCardMap($board) as $item) {
            if ($item['number'] === 0) {
                ++$maxNumber;

                $this->numerateCard($item['card'], $maxNumber, $client);
                $this->maxNumberService->saveMaxNumber($maxNumber, (int)$board->item->id);
            }
        }
    }

    private function labelingCards(BoardDto $board, PlankaClient $client): void
    {
        $cardLabels = [];

        foreach ($board->included->cardLabels as $cardLabel) {
            $cardLabels[$cardLabel->cardId][$cardLabel->labelId] = true;
        }

        foreach ($board->included->cards as $card) {
            $number = PrefixTemplateResolver::retrieveNumber($card->name, $this->cardPrefix);

            if ($number > 0 && !array_key_exists($this->numerateLabelId, $cardLabels[$card->id])) {
                $client->cardLabel->add($card->id, $this->numerateLabelId);

                $this->logger->debug('add specific numerate label by card', [
                    'cardId' => $card->id,
                ]);
            }
        }
    }

    private function getMaxNumber(BoardDto $board): int
    {
        $maxNumber = $this->maxNumberService->getMaxNumberByBoardDto($board, $this->cardPrefix);
        $savedNumber = $this->maxNumberService->getSavedNumber($board->item->id);

        if ($savedNumber > $maxNumber) {
            $maxNumber = $savedNumber;
        }

        return $maxNumber;
    }

    /**
     * @param BoardDto $board
     * @return array<string, array{number: int, card: CardDto}>
     */
    private function createCardMap(BoardDto $board): array
    {
        $cardMap = [];

        foreach ($board->included->cardLabels as $cardLabel) {
            if ($cardLabel->labelId === $this->numerateLabelId || $cardLabel->labelId === $this->bugfixLabelId) {
                foreach ($board->included->cards as $card) {
                    if ($card->id === $cardLabel->cardId) {
                        $cardMap[$card->id] = [
                            'number' => PrefixTemplateResolver::retrieveNumber($card->name, $this->cardPrefix),
                            'card' => $card,
                        ];
                    }
                }
            }
        }

        return $cardMap;
    }

    private function numerateCard(CardDto $card, int $number, PlankaClient $client): void
    {
        $prefix = PrefixTemplateResolver::getPrefixWithNumber($this->cardPrefix, $number);

        $oldName = $card->name;
        $card->name = sprintf(self::TEMPLATE_CARD_NAME, $prefix, $card->name);

        $client->card->update($card);

        $this->logger->debug('updated card', [
            'cardId' => $card->id,
            'oldName' => $oldName,
            'newName' => $card->name,
        ]);
    }
}