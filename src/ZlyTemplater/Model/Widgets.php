<?php

/**
 * Zly
 *
 * @version    $Id: Widgets.php 1056 2011-01-19 14:38:17Z deeper $
 */
namespace ZlyTemplater\Model;

class Widgets extends \Zly\Doctrine\Model
{

    /**
     * Widgets repository
     * @var \ZlyTemplater\Model\DbTable\Widget
     */
    protected $_repository;
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->_repository = $this->em->getRepository('\ZlyTemplater\Model\Mapper\Widget');
    }
    
    /**
     * Return list of all widgets
     *
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->_repository->findAll();
    }

    /**
     * Return widget entity
     *
     * @param int $id
     * @param boolean $forEdit
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidget($id, $forEdit = false)
    {
        if (empty($id) && $forEdit)
            $widget = new Mapper\Widget();
        else
            $widget = $this->_repository->getWidgetWithWidgetPoints($id);

        if (empty($widget) && $forEdit)
            $widget = new Mapper\Widget();

        return $widget;
    }

    /**
     * Save widget type
     * @param array $values
     * @return boolean
     */
    public function saveWidget(Mapper\Widget $widget, $values)
    {
        $widget->fromArray($values);     

        if($widget->getId()) {
            $this->em->getRepository('\ZlyTemplater\Model\Mapper\WidgetPoint')
                ->deleteUnusedPoints($widget->getId(), $values['widget_points']);
        }
        $layout = $this->em
                       ->getRepository('\ZlyTemplater\Model\Mapper\Layout')
                       ->find($widget->getLayoutId());
        $widget->setLayout($layout);
        $this->em->persist($widget);

        if(!empty($values['widget_points'])) {
            foreach($values['widget_points'] as $key=>$mapId) {
                
                if($widget->getId())
                    $point = $this->em->getRepository('\ZlyTemplater\Model\Mapper\WidgetPoint')
                            ->findOneBy(array('map_id' => $mapId, 'widget_id'=>$widget->getId()));

                if(empty($point)) {
                    $point = new Mapper\WidgetPoint();
                    $point->setMapId($mapId);
                    $point->setWidget($widget);
                    $this->em->persist($point);
                }
            }
        }
        
        return $this->em->flush();
    }

    /**
     * Return paginator for widgets list
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @return \Zend\Paginator\Paginator 
     */
    public function getWidgetsPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->em->getRepository('ZlyTemplater\Model\Mapper\Widget');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }
    
     /**
     * Delete Widget
     * @param int $id
     * @return boolean
     */
    public function deleteWidget($id)
    {
        $widget = new Templater_Model_Mapper_Widget();
        $widget->assignIdentifier($id);
        return $widget->delete();
    }
    
}