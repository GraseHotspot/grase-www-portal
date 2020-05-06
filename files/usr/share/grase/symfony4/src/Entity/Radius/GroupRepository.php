<?php

namespace App\Entity\Radius;

use Doctrine\ORM\EntityRepository;

/**
 * GroupRepository
 * Easily get a group
 */
class GroupRepository extends EntityRepository
{
    /**
     * Search for groups matching search term
     *
     * @param $search string
     *
     * @return Group[]
     */
    public function searchByGroupname($search)
    {
        if (empty($search)) {
            return [];
        }

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g')
            ->where('g.name LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        return $query->getQuery()->getResult();
    }
}
