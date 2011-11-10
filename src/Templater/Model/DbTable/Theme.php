<?php

/**
 * Zly
 * 
 * @version $Id: Theme.php 1224 2011-04-04 13:58:41Z deeper $
 * @license New BSD
 */
namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Theme extends EntityRepository
{

    /**
     * Return paginator for theme mapper
     * @return \Zly\Paginator\Adapter\Doctrine 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('theme')->getQuery();
        return new \Zly\Paginator\Adapter\Doctrine($query);
    }
    
    /**
     * Return current theme
     * @return Templater_Model_Mapper_Theme 
     */
    public function getCurrentTheme()
    {
       return $this->findOneBy(array('current'=>true));
    }

    /**
     * Return themes list with all layouts assigned to it
     * @param int $id - theme ID
     * @return Templater_Model_Mapper_Theme
     */
    public function getThemeWithLayouts($id)
    {
        return $this->createQueryBuilder('theme')
                    ->leftJoin('theme.Layouts', 'lay')
                    ->where('theme.id = ?', array($id))
                    ->getQuery()
                    ->getSingleResult();
    }
}

