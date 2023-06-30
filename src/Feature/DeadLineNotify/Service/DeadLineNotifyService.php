<?php

declare(strict_types=1);

namespace App\Feature\DeadLineNotify\Service;

use App\Entity\NotifyEventLog;
use App\Entity\NotifyUser;
use App\Feature\ApiIntegration\Service\PlankaApiIntegrator;
use App\Feature\DeadLineNotify\Enum\NotifyTypeEnum;
use App\Feature\TelegramNotify\Service\TelegramNotifyService;
use App\Repository\NotifyUserRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Planka\Bridge\Views\Dto\Board\BoardDto;
use Planka\Bridge\Views\Dto\Card\CardDto;
use Psr\Log\LoggerInterface;
use Throwable;

final class DeadLineNotifyService
{
    public function __construct(
        private readonly bool $isEnable,
        private readonly string $timezone,
        private readonly string $message,
        private readonly string $host,
        private readonly NotifyUserRepository $notifyUserRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TelegramNotifyService $notifyService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(PlankaApiIntegrator $integrator): void
    {
        if (!$this->isEnable) {
            return;
        }

        try {
            $client = $integrator->getClient();

            [$boardList, $userNotify] = $this->getMap();

            foreach ($boardList as $boardId) {
                $board = $client->board->get((string)$boardId);

                $this->execute($board, $userNotify);
            }
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
    }


    /**
     * @return list<NotifyUser>
     */
    private function getUserList(): array
    {
        return $this->notifyUserRepository->findAll();
    }

    public function getMap(): array
    {
        $boardList = [];
        $userNotify = [];

        foreach ($this->getUserList() as $user) {
            $boardId = $user->getBoardId();

            $boardList[$boardId] = $boardId;

            if (array_key_exists($boardId, $userNotify)) {
                $userNotify[$boardId] = [...$userNotify[$boardId], $user];

                continue;
            }

            $userNotify[$boardId][] = $user;
        }

        /** @var array<string, NotifyUser> $userNotify */
        /** @var array<string, string> $boardList */
        return [$boardList, $userNotify];
    }

    /**
     * @param BoardDto $board
     * @param array<string, NotifyUser> $userNotify
     * @throws Exception
     */
    private function execute(BoardDto $board, array $userNotify): void
    {
        if (!array_key_exists($board->item->id, $userNotify)) {
            return;
        }

        $now = new \DateTimeImmutable(timezone: new DateTimeZone($this->timezone));

        if ($now < $now->setTime(8, 0)) {
            return;
        }

        foreach ($board->included->cards as $card) {
            if ($card->dueDate !== null) {
                $cardDeadLine = $card->dueDate->setTimezone(new DateTimeZone($this->timezone));

                if ($now->modify('+1 week') >= $cardDeadLine) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AT_WEEK);

                    continue;
                }

                if ($now->modify('+3 days') >= $cardDeadLine) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AT_THREE_DAYS);

                    continue;
                }

                if ($now->modify('+1 days') >= $cardDeadLine) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AT_TOMORROW);

                    continue;
                }

                if ($now->setTime(0, 0) === $cardDeadLine->setTime(0, 0)) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AT_TODAY);

                    continue;
                }

                if ($cardDeadLine >= $now) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AT_DEAD_LINE);

                    continue;
                }

                if ($now > $cardDeadLine) {
                    $this->notify($userNotify, $card, NotifyTypeEnum::AFTER_DEADLINE_BY_EVERYDAY);
                }
            }
        }
    }

    private function notify(array $userNotify, CardDto $card, NotifyTypeEnum $type): void
    {
        if ($userNotify[(int)$card->boardId] ?? false) {
            foreach ($userNotify[(int)$card->boardId] as $user) {
                if ($user->getTelegramUserId() === null) {
                    continue;
                }

                if ($this->notifyUserRepository->isNotified($user, (int)$card->id, $type)) {
                    continue;
                }

                $this->notifyService->send(
                    $user->getTelegramUserId(),
                    sprintf(
                        $this->message,
                        $card->name,
                        $card->dueDate->setTimezone(new DateTimeZone('Europe/Moscow'))->format(DATE_ATOM),
                        $this->getCardUrl($card)
                    )
                );

                $this->setNotify($user, $card, $type);
            }
        }
    }

    // <host>/cards/<cardId> -> https://planka.host.com/cards/887936266107094569
    private function getCardUrl(CardDto $card): string
    {
        return sprintf('%s/cards/%s', $this->host, $card->id);
    }

    private function setNotify(NotifyUser $user, CardDto $card, NotifyTypeEnum $type): void
    {
        $this->entityManager->persist(new NotifyEventLog(
            userId: $user->getId(),
            boardId: (int)$card->boardId,
            cardId: (int)$card->id,
            type: $type
        ));
        $this->entityManager->flush();
    }
}