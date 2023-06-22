<?php

declare(strict_types=1);

namespace App\Feature\SentryWebhook\Service;

use App\Entity\SentryIssue;
use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use App\Feature\AutoNumerator\Service\NextNumberCardService;
use App\Feature\AutoNumerator\Service\PrefixTemplateResolver;
use App\Repository\BoardCardDetailsRepository;
use App\Repository\SentryIssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Planka\Bridge\PlankaClient;
use Psr\Log\LoggerInterface;

final class CreateBugfixCardService
{
    private const TEMPLATE = "[%s] \n# %s \n\n %s \n level: %s";

    public function __construct(
        private readonly bool $isEnable,
        private readonly string $cardPrefix,
        private readonly string $numerateLabelId,
        private readonly string $bugfixLabelId,
        private readonly SentryIssueRepository $repository,
        private readonly BoardCardDetailsRepository $cardNumberRepository,
        private readonly NextNumberCardService $nextNumberCardService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }
    public function handle(PlankaApiIntegrator $integrator): void
    {
        if (!$this->isEnable) {
            return;
        }

        $client = $integrator->getClient();

        foreach ($this->repository->findNewIssues() as $issue) {
            $cardDetail = $this->cardNumberRepository->findLastNumberByBoardId($issue->getBoardId());

            if ($cardDetail !== null && $cardDetail->getLastCardNumber() !== null) {
                $next = $cardDetail->getLastCardNumber() + 1;

                $this->createCardWithNumber($issue, $next, $client);

                $cardDetail->setLastCardNumber($next);

                $this->nextNumberCardService->save($cardDetail);

                continue;
            }

            $this->createCard($issue->getIssue()['message'], $issue, $client);
        }
    }

    private function createCardWithNumber(SentryIssue $issue, int $number, PlankaClient $client): void
    {
        $name = sprintf(
            '%s %s',
            PrefixTemplateResolver::getPrefixWithNumber($this->cardPrefix, $number),
            $issue->getIssue()['message']
        );

        $issue->setCardNumber($number);

        $this->createCard($name, $issue, $client);
    }

    private function createCard(string $name, SentryIssue $issue, PlankaClient $client): void
    {
        $card = $client->card->create((string)$issue->getListId(), $name, $issue->getId());
        $card->description = sprintf(
            self::TEMPLATE,
            $issue->getIssue()['id'],
            $issue->getIssue()['message'],
            $issue->getIssue()['url'],
            $issue->getIssue()['level']
        );

        $client->card->update($card);

        $client->cardLabel->add($card->id, $this->numerateLabelId);
        $client->cardLabel->add($card->id, $this->bugfixLabelId);

        $issue->setIsCreated(true);
        $issue->setCardId((int)$card->id);

        $this->entityManager->persist($issue);
        $this->entityManager->flush();

        $this->logger->info('Create bugfix card', [
            'issueId' => $issue->getId(),
            'name' => $name,
            'boardId' => $issue->getBoardId(),
            'listId' => $issue->getListId(),
        ]);
    }
}