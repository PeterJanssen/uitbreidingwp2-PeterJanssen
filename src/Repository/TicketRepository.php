<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findAllDescendingByNumberOfVotes()
    {
        $queryBuilder = $this->createQueryBuilder('ticket');
        $queryBuilder->orderBy('ticket.numberOfVotes', 'DESC');

        return $queryBuilder->getQuery()->execute();
    }

    public function findTicketsTodayByAssetId($assetId)
    {
        $queryBuilder = $this->createQueryBuilder('ticket');
        $queryBuilder
            ->andWhere('ticket.creationDate LIKE :date')
            ->andWhere('IDENTITY(ticket.asset) = :assetId')
            ->setParameter('date', '%'.date('Y-m-d', time()). '%')
            ->setParameter('assetId', $assetId);

        return $queryBuilder->getQuery()->execute();
    }
}
