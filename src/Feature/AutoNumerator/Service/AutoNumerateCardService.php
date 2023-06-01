<?php

declare(strict_types=1);

namespace App\Feature\AutoNumerator\Service;

use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use App\Feature\AutoNumerator\Dto\PrefixNumberContext;
use Planka\Bridge\PlankaClient;
use Planka\Bridge\Views\Dto\Board\BoardDto;
use Planka\Bridge\Views\Dto\Card\CardDto;
use Psr\Log\LoggerInterface;

final class AutoNumerateCardService
{
    private const TEMPLATE_CARD_NAME = '%s %s';

    public function __construct(
        private readonly NextNumberCardService $maxNumberService,
        private readonly string $numerateLabelId,
        private readonly string $bugfixLabelId,
        private readonly string $cardPrefix,
        private readonly string $boardId,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(PlankaApiIntegrator $integrator): void
    {
        $client = $integrator->getClient();

        $context = $this->createContext($client->board->get($this->boardId));

        $nextNumber = $this->maxNumberService->getNextNumber($context);

        $this->numerate($context, $nextNumber, $client);

        $this->labeling($context, $client);

        $this->loggingDoublePrefixCards($context->getDoubleNumberedCards());

        $this->maxNumberService->saveByNextNumber($nextNumber);
    }

    private function createContext(BoardDto $board): PrefixNumberContext
    {
        $context = new PrefixNumberContext($this->cardPrefix);

        // add labeled cards
        foreach ($board->included->cardLabels as $cardLabel) {
            if ($cardLabel->labelId === $this->numerateLabelId ||
                $cardLabel->labelId === $this->bugfixLabelId
            ) {
                foreach ($board->included->cards as $card) {
                    if ($card->id === $cardLabel->cardId) {
                        $context->addCard($card);
                    }
                }
            }
        }

        // add cards with numerical template without label
        $labeledCards = array_flip($context->getAllCards());

        foreach ($board->included->cards as $card) {
            if (array_key_exists($card->id, $labeledCards)) {
                continue;
            }

            $context->addUnlabeledCard($card);
        }

        return $context;
    }

    private function numerate(PrefixNumberContext $context, int &$nextNumber, PlankaClient $client): void
    {
        foreach ($context->getUnNumberedCards() as $card) {
            $prefix = PrefixTemplateResolver::getPrefixWithNumber($context->getPrefix(), $nextNumber);

            $oldName = $card->name;
            $card->name = sprintf(self::TEMPLATE_CARD_NAME, $prefix, $card->name);

            // find labeled cards without numerical template
            $client->card->update($card);

            $this->logger->debug('updated card', [
                'cardId' => $card->id,
                'oldName' => $oldName,
                'newName' => $card->name,
            ]);

            $nextNumber++;
        }
    }

    private function labeling(PrefixNumberContext $context, PlankaClient $client): void
    {
        foreach($context->getUnlabeledCards() as $card) {
            $client->cardLabel->add($card->id, $this->numerateLabelId);

            $this->logger->debug('add specific numerate label by card', [
                'cardId' => $card->id,
            ]);
        }
    }

    private function loggingDoublePrefixCards(array $cardList): void
    {
        foreach ($cardList as $number => $cards) {
            $this->logger->warning('Planka have doubled number prefix cards', [
                'number' => $number,
                'cards' => $this->getCardList($cards),
            ]);
        }
    }

    /**
     * @param array<int, CardDto> $cards
     * @return list<array{cardId: int, name: string}>
     */
    private function getCardList(array $cards): array
    {
        $result = [];

        foreach ($cards as $card) {
            $template = [
                'cardId' => (int)$card->id,
                'name' => $card->name,
            ];

            $result[] = $template;
        }

        return $result;
    }
}