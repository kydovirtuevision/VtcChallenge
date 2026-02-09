<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * Basic search: by text (title or content), status and category for a given owner
     */
    public function searchForUser($owner, ?string $q, ?string $status, ?string $category)
    {
        if (is_object($owner) && method_exists($owner, 'getId')) {
            $ownerId = $owner->getId();
        } else {
            $ownerId = $owner;
        }

        $qb = $this->createQueryBuilder('n')
            ->andWhere('IDENTITY(n.owner) = :ownerId')
            ->setParameter('ownerId', $ownerId);

        if ($q) {
            $qb->andWhere('n.title LIKE :q OR n.content LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }

        if ($status) {
            $qb->andWhere('n.status = :status')
               ->setParameter('status', $status);
        }

        if ($category) {
            $qb->andWhere('n.category = :category')
               ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }
}
