<?php

/**
 * Zly
 * 
 * @version $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 * @license New BSD
 */
namespace ZlyTemplater\Model\Mapper;

/**
 * @\Doctrine\ORM\Mapping\Entity(repositoryClass="ZlyTemplater\Model\DbTable\Theme")
 * @\Doctrine\ORM\Mapping\Table(name="templater_themes")
 */
class Theme
{
    /**
     * @\Doctrine\ORM\Mapping\Id
     * @\Doctrine\ORM\Mapping\Column(type="integer")
     * @\Doctrine\ORM\Mapping\GeneratedValue
     */
    protected $id;
    /** @\Doctrine\ORM\Mapping\Column(length=255) */
    protected $title;
    /** @\Doctrine\ORM\Mapping\Column(length=255) */
    protected $name;
    /** @\Doctrine\ORM\Mapping\Column(type="boolean") */
    protected $active;    
    /** @\Doctrine\ORM\Mapping\Column(type="integer") */
    protected $ordering;    
    /**
     * @\Doctrine\ORM\Mapping\OneToMany(targetEntity="ZlyTemplater\Model\Mapper\Layout", mappedBy="theme", cascade={"remove"})
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

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getLayouts()
    {
        return $this->layouts;
    }

    public function setLayouts($layouts)
    {
        $this->layouts = $layouts;
    }
    
    public function getOrdering()
    {
        return $this->ordering;
    }

    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }
    
    public function toArray()
    {
        $array = array();
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');

        $vars = get_class_vars(get_class($this));
        foreach (array_keys($vars) as $var) {
            $method = 'get' . $filter->filter($var);
            if(method_exists($this, $method)) {
                $array[$var] = $this->{$method}();
            }
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

