<?php

namespace App\Entity\Radius;

use Doctrine\ORM\EntityRepository;

/**
 * RadacctRepository
 * Allows quick and easy finding of all Active Sessions (and other criteria as we add the filters)
 */
class RadacctRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function findAllActiveSessions()
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('ra')
            ->from(Radacct::class, 'ra')
            ->where('ra.acctstoptime IS NULL')
            ->orderBy('ra.radacctid', 'DESC')
            ;

        return $query->getQuery()->getResult();
    }
}
