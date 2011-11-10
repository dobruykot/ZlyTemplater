<?php

/**
 * Zly
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Layout.php 269 2010-10-05 13:38:46Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Templater\Model\DbTable\Layout")
 * @Doctrine\ORM\Mapping\Table(name="templater_layouts")
 */
class Layout
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
    /** @Doctrine\ORM\Mapping\Column(type="integer") */
    protected $theme_id;
    /** @Doctrine\ORM\Mapping\Column(length=1000, nullable=true) */
    protected $params;
    /** @Doctrine\ORM\Mapping\Column(type="boolean") */
    protected $published;
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Templater\Model\Mapper\Theme")
     * @Doctrine\ORM\Mapping\JoinColumn(name="theme_id", referencedColumnName="id", unique=false)
     */
    protected $theme;
    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Templater\Model\Mapper\Widget", mappedBy="layout", cascade={"remove", "persist"})
     */
    protected $widgets;    
    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Templater\Model\Mapper\LayoutPoint", mappedBy="layout", cascade={"remove", "persist"})
     */
    protected $points;

    protected $path;

    protected $vars;

    public function getId()     {
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

    public function getThemeId()
    {
        return $this->theme_id;
    }

    public function setThemeId($theme_id)
    {
        $this->theme_id = $theme_id;
    }

    public function getParams()
    {
        return unserialize($this->params);
    }

    public function setParams($params)
    {
        $this->params = serialize($params);
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
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

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setVar($name, $value)
    {
        $this->vars[$name][] = $value;
    }

    public function getVars($name = null)
    {
        if(!empty($name) && !isset($this->vars[$name]))
            return false;

        if(!empty($name))
            return $this->vars[$name];
        else
            return $this->vars;
    }
}

