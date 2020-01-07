<?php


namespace App\Entity\Radius;


use Doctrine\ORM\EntityRepository;

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