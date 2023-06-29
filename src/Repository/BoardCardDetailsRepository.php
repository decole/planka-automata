<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BoardCardDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class BoardCardDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardCardDetails::class);
    }

    public function findLastNumberByBoardId(int $boardId): ?BoardCardDetails
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('number')
            ->from(BoardCardDetails::class, 'number')
            ->andWhere(
                $qb->expr()->eq('number.boardId', ':boardId')
            );

        $qb->setParameter('boardId', $boardId);

        return $qb->getQuery()->getOneOrNullResult();
    }
}