<?php

/**
 * Zly
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
namespace ZlyTemplater\Model\Mapper;

/**
 * @\Doctrine\ORM\Mapping\Entity(repositoryClass="ZlyTemplater\Model\DbTable\WidgetPoint")
 * @\Doctrine\ORM\Mapping\Table(name="templater_widget_points")
 */
class WidgetPoint
{
    /**
     * @\Doctrine\ORM\Mapping\Id
     * @\Doctrine\ORM\Mapping\Column(type="integer")
     * @\Doctrine\ORM\Mapping\GeneratedValue
     */
    protected $id;
    /** @\Doctrine\ORM\Mapping\Column(length=35) */
    protected $map_id;
    /** @\Doctrine\ORM\Mapping\Column(type="integer", nullable=true) */
    protected $widget_id;
    /**
     * @\Doctrine\ORM\Mapping\ManyToOne(targetEntity="ZlyTemplater\Model\Mapper\Widget")
     * @\Doctrine\ORM\Mapping\JoinColumn(name="widget_id", referencedColumnName="id", unique=false)
     */
    protected $widget;
    
    public function getId()     {
        return $this->id;
    }

    public function getMapId()
    {
        return $this->map_id;
    }

    public function setMapId($map_id)
    {
        $this->map_id = $map_id;
    }

    public function getWidgetId()
    {
        return $this->widget_id;
    }

    public function setWidgetId($widget_id)
    {
        $this->widget_id = $widget_id;
    }

    public function getWidget()
    {
        return $this->widget;
    }

    public function setWidget($widget)
    {
        $this->widget = $widget;
    }


}

