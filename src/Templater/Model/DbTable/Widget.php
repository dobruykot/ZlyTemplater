<?php

/**
 * Zly
 * 
 * @version $Id: Widget.php 1231 2011-04-17 17:49:48Z deeper $
 * @license New BSD
 */
namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Widget extends EntityRepository
{
    public function getWidgets()
    {
        $this->createQueryBuilder('wd')
             ->select('wd','lay')
             ->leftJoin('wd.layout lay');
        return $query->execute();
    }

    /**
     * Return widget by id with all widget points
     * @param int $wdId
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidgetWithWidgetPoints($wdId)
    {
        $query = $this->createQueryBuilder('wd')
                      ->select('wd','wp')
                      ->leftJoin('wd.points', 'wp')
                      ->andWhere('wd.id = :wdid')
                      ->setParameter('wdid', $wdId)
                      ->getQuery();
        
        return $query->getSingleResult();
    }
    
    /**
     * Return paginator for widget mapper
     * @return \Zly\Paginator\Adapter\Doctrine 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('wd')
                      ->select('wd','lay','theme')
                      ->leftJoin('wd.layout', 'lay')
                      ->leftJoin('lay.theme', 'theme')
                      ->getQuery();
        return new \Zly\Paginator\Adapter\Doctrine($query);
    }
}

