<?php

declare(strict_types=1);

namespace App\Feature\SentryWebhook\Service;

use App\Entity\SentryIssue;
use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use App\Repository\SentryIssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Planka\Bridge\PlankaClient;
use Psr\Log\LoggerInterface;

final class CreateBugfixCardService
{
    private const TEMPLATE = "[%s] \n# %s \n\n %s \n level: %s";

    public function __construct(
        private readonly bool $isEnable,
        private readonly string $bugfixLabelId,
        private readonly SentryIssueRepository $repository,
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
            $this->createCard($issue->getIssue()['message'], $issue, $client);
        }
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