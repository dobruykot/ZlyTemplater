<?php

/**
 * Zly
 * 
 * @version $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Templater\Model\DbTable\Theme")
 * @Doctrine\ORM\Mapping\Table(name="templater_themes")
 */
class Theme
{
    /**
     * @Doctrine\ORM\Mapping\Id 
     * @Doctrine\ORM\Mapping\Column(type="integer")
     * @Doctrine\ORM\Mapping\GeneratedValue
     */
    protected $id;
    /** @Doctrine\ORM\Mapping\Column(length=255) */
    protected $title;
    /** @Doctrine\ORM\Mapping\Column(length=255) */
    protected $name;
    /** @Doctrine\ORM\Mapping\Column(type="boolean") */
    protected $current;    
    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Templater\Model\Mapper\Layout", mappedBy="theme", cascade={"remove"})
     */
    protected $layouts;
    
    public function getId()     
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current)
    {
        $this->current = $current;
    }

    public function getLayouts()
    {
        return $this->layouts;
    }

    public function setLayouts($layouts)
    {
        $this->layouts = $layouts;
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

