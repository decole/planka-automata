<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NotifyEventLog;
use App\Entity\NotifyUser;
use App\Feature\DeadLineNotify\Enum\NotifyTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class NotifyUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotifyUser::class);
    }

    public function isNotified(NotifyUser $user, int $cardId, NotifyTypeEnum $type): bool
    {
        $date = new \DateTimeImmutable();

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('notify')
            ->from(NotifyEventLog::class, 'notify')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('notify.userId', ':userId'),
                    $qb->expr()->eq('notify.cardId', ':cardId'),
                    $qb->expr()->eq('notify.type', ':type'),
                    $qb->expr()->between('notify.createdAt', ':x', ':y')
                )
            );

        $qb->setParameter('userId', $user->getId());
        $qb->setParameter('cardId', $cardId);
        $qb->setParameter('type', $type->value);
        $qb->setParameter('x', $date->setTime(0, 0));
        $qb->setParameter('y', $date->setTime(23, 59, 59));

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}