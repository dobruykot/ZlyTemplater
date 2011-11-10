<?php

/**
 * Zly
 * 
 * @version $Id: Widget.php 269 2010-10-05 13:38:46Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Templater\Model\DbTable\Widget")
 * @Doctrine\ORM\Mapping\Table(name="templater_widgets")
 */
class Widget
{
    /**
     * @Doctrine\ORM\Mapping\Id 
     * @Doctrine\ORM\Mapping\Column(type="integer")
     * @Doctrine\ORM\Mapping\GeneratedValue
     */
    protected $id;
    /** @Doctrine\ORM\Mapping\Column(length=255) */
    protected $name;
    /** @Doctrine\ORM\Mapping\Column(type="boolean") */
    protected $published;
    /** @Doctrine\ORM\Mapping\Column(type="integer", nullable=true) */
    protected $layout_id;    
    /** @Doctrine\ORM\Mapping\Column(type="integer", nullable=true) */
    protected $ordering;  
    /** @Doctrine\ORM\Mapping\Column(length=35) */
    protected $map_id;
    /** @Doctrine\ORM\Mapping\Column(length=255) */
    protected $placeholder;
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Templater\Model\Mapper\Layout")
     * @Doctrine\ORM\Mapping\JoinColumn(name="layout_id", referencedColumnName="id", unique=false)
     */
    protected $layout;
    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Templater\Model\Mapper\WidgetPoint", mappedBy="widget", cascade={"remove", "persist"})
     */
    protected $points; 
    
    public function getId()     
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMapId()
    {
        return $this->map_id;
    }

    public function setMapId($map_id)
    {
        $this->map_id = $map_id;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function getLayoutId()
    {
        return $this->layout_id;
    }

    public function setLayoutId($layout_id)
    {
        $this->layout_id = $layout_id;
    }

    public function getOrdering()
    {
        return $this->ordering;
    }

    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($widget_points)
    {
        $this->points = $widget_points;
    }

    public function toArray()
    {
        $array = array();
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');

        $vars = get_class_vars(get_class($this));
        foreach (array_keys($vars) as $var) {
            $array[$var] = $this->{'get' . $filter->filter($var)}();
        }
        return $array;
    }

    public function fromArray($data)
    {
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');

        $vars = get_class_vars(get_class($this));
        foreach (array_keys($vars) as $var) {
            if (isset($data[$var]))
                $this->{'set' . $filter->filter($var)}($data[$var]);
        }

        return $this;
    }
}

