<?php

/**
 * Zly
 *
 * @version    $Id: Layouts.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace ZlyTemplater\Model;

class Layouts extends \Zly\Doctrine\Model
{

    protected $repoName = '\ZlyTemplater\Model\Mapper\Layout';
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    /**
     * Return all layouts collection
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->em->getRepository($this->repoName)->findAll();
    }

    /**
     * Return single layout by id
     * @param int $id
     * @return Templater_Model_Mapper_Layout
     */
    public function getLayout($id, $forEdit = false)
    {
        if(empty($id))
            return new Mapper\Layout();

        $layout = $this->em->getRepository($this->repoName)->getLayoutWithLayoutPoints($id);

        if(empty($layout))
            return new Mapper\Layout();
        else
            return $layout;
    }
    
    /**
     * Return layout which registered like default
     * @return  \ZlyTemplater\Model\Mapper\Layout
     */
    public function getDefaultLayout()
    {
        return $this->em->getRepository($this->repoName)
                    ->getDefaultLayout();
    }
    
    /**
     * Return layout which assigned to provided systemap nodes
     * @return \ZlyTemplater\Model\Mapper\Layout 
     */
    public function getCurrentLayout($identifiers)
    {
        return $this->em->getRepository($this->repoName)
                    ->getCurrentLayout($identifiers);
    }

    /**
     * Return layouts list pager
     *
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getLayoutsPaginator($pageNumber = 1, $itemCountPerPage = 20, array $where = array())
    {
        $paginator = $this->em->getRepository($this->repoName)->getPaginator();
        return $paginator;
    }

    /**
     * Return list of layouts found in Theme directory
     * and save it if not found in DB
     *
     * @param Templater_Model_Mapper_Theme $theme
     * @return array
     */
    public function importFromTheme(Mapper\Theme $theme, $import = false)
    {
        $options = $this->config;
        
        $path = realpath(
            $options->themes->directory . DIRECTORY_SEPARATOR .
            $theme->getName(). DIRECTORY_SEPARATOR .
            $options->layout->directory);
        if(empty($path))
            return false;

        $layouts = $this->getLayoutsFiles($path);
        
        $sysmapService = $this->locator->get('sysmap-service');
        $rootNode = $sysmapService->getRootIdentifier();
        
        if($import)
            foreach (array_keys($layouts) as $name) {
                $exist = $this->em->getRepository($this->repoName)
                        ->findOneBy(array('theme_id'=>$theme->getId(), 'name'=>$name));
               
                if (empty($exist)) {
                    $layout = new Mapper\Layout();
                    $layout->setName($name);
                    $layout->setTheme($theme);
                    $layout->setTitle(ucfirst($name));
                    $layout->setPublished(true);
                    $this->em->persist($layout);
                    
                    if ($name == $options['layout']['default']) {

                        $layPoint = new Mapper\LayoutPoint();
                        $layPoint->setMapId($rootNode->getResourceId());
                        $layPoint->setLayout($layout);
                        $this->em->persist($layPoint);
                    }
                }
            }
        
        $this->em->flush();
        return $layouts;
    }

    /**
     * Return list of files which found in tempalte directory
     * @param string $path
     * @return array
     */
    public function getLayoutsFiles($path)
    {
        $result = array();
        $dirIterator = new \DirectoryIterator($path);
        foreach ($dirIterator as $dir) {
            if (!$dir->isDir() && $dir->isFile()
                && strripos($dir->getBasename(), '.') !== 0) {
                $result[$dir->getBasename('.phtml')] = $dir->getBasename();
            }
        }
        return $result;
    }

    /**
     * Save layout
     * @param Templater_Model_Mapper_Layout $layout
     * @param array $values
     * @return boolean
     */
    public function saveLayout(Mapper\Layout $layout, $values)
    {    
        $layout->fromArray($values);
        
        $id = $layout->getId();
        
        if(!empty($id)) {
            $this->em->getRepository('\ZlyTemplater\Model\Mapper\LayoutPoint')
                ->deleteUnusedPoints($layout->getId(), $values['map_id']);
        } else {
            $theme = $this->em->getRepository('\ZlyTemplater\Model\Mapper\Theme')
                          ->find($values['theme_id']);
            $layout->setTheme($theme);
        }
        
        $this->em->persist($layout);
        
        if(!empty($values['map_id'])) {
            foreach($values['map_id'] as $key=>$mapId) {
                $repo = $this->em->getRepository('\ZlyTemplater\Model\Mapper\LayoutPoint');
                $layPoint = $repo->findOneBy(array('map_id'=>$mapId, 'layout_id'=>$layout->getId()));
                
                if(empty($layPoint)) {
                    $layPoint = new Mapper\LayoutPoint();
                    $layPoint->setMapId($mapId);
                    $layPoint->setLayout($layout);
                    $this->em->persist($layPoint);
                } 
            }

        }

        return $this->em->flush();
    }

    /**
     * Delete layout
     * @param Mapper\Layout $layout
     * @return boolean
     */
    public function deleteLayout(Mapper\Layout $layout, \Zend\Controller\Request\AbstractRequest $request)
    {
        $currentLayout = $this->em->getRepository($this->repoName)
                ->getCurrentLayout($request);
        if($currentLayout->getId() == $layout->getId())
            throw new \Zend\Layout\Exception('You can\'t delete current layout');
        $this->em->remove($layout);
        return $this->em->flush();
    }

    public function getLayoutWithWidgetsbyNameAndRequest($layoutName, $mapIds = array()) 
    {
        return $this->em->getRepository($this->repoName)->getLayoutWithWidgetsbyNameAndRequest($layoutName, $mapIds);
    }
    
    public function setConfig($config)
    {
        $this->config = $config;
    }
    
    public function setLocator($locator)
    {
        $this->locator = $locator;
    }
}