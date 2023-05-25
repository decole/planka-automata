<?php

declare(strict_types=1);

namespace App\Module\AutoNumerator\Service;

use App\Module\ApiIntegration\Service\PlankaApiIntegrator;

final class AutoNumerateCardService
{
    // add .env template card param
    public function __construct(
        private readonly PlankaApiIntegrator $integrator,
        private readonly string $boardId
    ) {
    }

    public function handle(): void
    {
        $client = $this->integrator->getClient();

        $this->integrator->authenticate();
        // get cards in project and board
        $board = $client->board->get($this->boardId);


        // PLANKA_BUGFIX_LABEL_ID=794228157828826616
        // PLANKA_NUMERICAL_LABEL_ID=770114546592384237

        $cardMap = $board->included->cards;

        // find cards with spec label
        foreach ($board->included->cardLabels as $cardLabel) {
            if ($cardLabel->labelId === '770114546592384237') {
                foreach ($cardMap as $card) {
                    if ($card->id === $cardLabel->cardId) {
                        dump($cardLabel->cardId . '  ' . $card->name);
                    }
                }
            }
        }

        // update name by template
        // check last numeric card and save in DB last number
        // all found cards with numerical template should be labeled! check in if is not it
        // find labeled cards without numerical template and next numbering

        // observe labels: PLANKA_BUGFIX_LABEL_ID and PLANKA_NUMERICAL_LABEL_ID
        // PLANKA_NUMERICAL_TEMPLATE=UBS
    }
}