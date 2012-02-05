<?php

/**
 * Zly
 *
 * @version    $Id: Themes.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace ZlyTemplater\Model;

class Themes extends \Zly\Doctrine\Model
{

    protected $config;
    protected $locator;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function setLocator($locator)
    {
        $this->locator = $locator;
    }
    
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Return collection of all tempaltes
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->em
                    ->getRepository('ZlyTemplater\Model\Mapper\Theme')
                    ->findAll();
    }

    /**
     * Return paginator for themes list
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @return \Zend\Paginator\Paginator 
     */
    public function getThemesPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->em->getRepository('ZlyTemplater\Model\Mapper\Theme');
        /** @var $paginator \Doctrine\ORM\Tools\Pagination\Paginator */
        $paginator = $repo->getPaginator();
        return $paginator;
    }

    /**
     * Return single theme
     * @param int $id
     * @param boolean $forUpdate
     * @return Templater_Model_Mapper_Theme
     */
    public function getTheme($id = null, $forUpdate = false)
    {
        if (!empty($id))
            $theme = $this->em
                          ->getRepository('\ZlyTemplater\Model\Mapper\Theme')
                          ->findOneBy(array('id'=>$id));
        else
            $theme = false;

        if (empty($theme) && $forUpdate)
            $theme = new Mapper\Theme();

        return $theme;
    }

    /**
     * Disable all active tempaltes
     * @return boolean
     */
    public function disableAllThemes()
    {
        $activeThemes = $this->em->getRepository('\ZlyTemplater\Model\Mapper\Theme')
                ->findBy(array('current'=>true));

        foreach ($activeThemes as $theme) {
            $theme->setCurrent(false);
            $this->em->persist($theme);
        }
        $this->em->flush();
        return true;
    }

    /**
     * Save theme object into DB
     * @param Templater_Model_Mapper_Theme $theme
     * @param array $values
     * @return boolean
     */
    public function saveTheme(Mapper\Theme $theme, array $values)
    {
        $theme->fromArray($values);
        $layoutsModel = $this->locator->get('ZlyTemplater\Model\Layouts');
        $current = false;
        if ($theme->getCurrent() == true) {
            $current = true;
            $theme->setCurrent(false);
        }
        $this->em->persist($theme);
        $result = $this->em->flush();

        if (!empty($values['import_layouts'])) {
            $layoutsModel->importFromTheme($theme, true);
        }

        if($current === true && $this->locator->instanceManager()->hasAlias('sysmap-service')) {
            
     
            $rootNode = $this->locator->get('sysmap-service')->getRootIdentifier();
            $front = false;
            foreach($theme->getLayouts() as $layout) {
                /* @var $layout \ZlyTemplater\Model\Mapper\Layout */
                $points = $layout->getPoints();
                if(!empty($points)) {
                    foreach($points as $point) {
                        if($point->getMapId() == $rootNode->getResourceId())
                            $front = true;
                    }
                }
            }
            
            if($front) {
                $this->disableAllThemes();
                $theme->setCurrent(true);
                $this->em->persist($theme);
                $result = $this->em->flush();
            } else {
                throw new \Exception('Theme can\'t be activated, because '.
                        'published default layouts not found for this theme');
            }
        }

        return $result;
    }

    /**
     * Generate list of all directories which placed in 'themes' directory
     * @return array
     */
    public function getThemesDirectoriesFromFS()
    {
        $dirIterator = new \DirectoryIterator($this->config->themes->directory);
        $result = array();
        foreach ($dirIterator as $dir) {
            if ($dir->isDir()
                && !$dir->isDot()
                && strripos($dir->getBasename(), '.') !== 0)
                $result[$dir->getBasename()] = $dir->getBasename();
        }
        return $result;
    }

    /**
     * Delete theme
     * @return boolean
     */
    public function deleteTheme($id)
    {
        $theme = $this->em
                      ->getRepository('\ZlyTemplater\Model\Mapper\Theme')->find($id);
        if(empty($theme))
            return false;
        if($theme->getCurrent() == true)
            throw new Zend_Exception('You can\'t delete active theme.');
        $this->em->remove($theme);
        return $this->em->flush();
    }
    
    public function initSchema()
    {
        $em = $this->em;
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);    
        $tool->createSchema($classes);
        return $this;
    }
    
    public function updateSchema()
    {
        $em = $this->em;
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->updateSchema($classes, true);
        return $this;
    }
    
    public function dropSchema()
    {
        $em = $this->em;
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);
        return $this;
    }
    
    protected function _getShemaClasses()
    {
        $em = $this->em;
        $classes = array(
          $em->getClassMetadata('ZlyTemplater\Model\Mapper\Layout'),
          $em->getClassMetadata('ZlyTemplater\Model\Mapper\LayoutPoint'),
          $em->getClassMetadata('ZlyTemplater\Model\Mapper\Theme'),
          $em->getClassMetadata('ZlyTemplater\Model\Mapper\Widget'),
          $em->getClassMetadata('ZlyTemplater\Model\Mapper\WidgetPoint'),
        );
        
        return $classes;
    }

}