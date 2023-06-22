<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SentryIssue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

final class SentryIssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SentryIssue::class);
    }

    /**
     * @return list<SentryIssue>
     */
    public function findNewIssues(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('issue')
            ->from(SentryIssue::class, 'issue')
            ->andWhere(
                $qb->expr()->eq('issue.isCreated', ':state')
            );

        $qb->setParameter('state', false);

        return $qb->getQuery()->getResult();
    }
}