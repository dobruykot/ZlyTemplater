<?php

/**
 * Zly
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class WidgetPoint extends EntityRepository
{
    /**
     * Remove from DB widget points which not in new points set
     * @param int $layId
     * @param array $newPoints
     * @return boolean
     */
    public function deleteUnusedPoints($wdId, array $newPoints)
    {
        $qb = $this->createQueryBuilder('wp');
        $pointsPart = $qb->expr()->in('wp.map_id', ':points');
        $query = $qb 
                     ->leftJoin('wp.widget', 'wd')
                     ->andWhere('wd.id = :wdId')
                     ->andWhere($pointsPart)
                     ->setParameter('wdId', $wdId)
                     ->setParameter('points', $newPoints)
                     ->getQuery();
 
        $points = $query->execute();
        foreach($points as $point)
            $this->getEntityManager()->remove($point);
        return $this->getEntityManager()->flush();
    }
}

