<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Layout.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace ZlyTemplater\Form;

use \Zend\Form\Element as Element;

class Layout extends \Zend\Form\Form
{
    protected $locator;
    
    /**
     * Form initialization
     */
    public function init()
    {
        $this->loadDefaultDecorators();
        $this->setLegend('New layout');
        $this->addDecorator('fieldset');
        $this->setMethod('POST');
        $element = new Element\Text('title');
        $element->setLabel('Title:')
                ->setRequired(true);
        $this->addElement($element);

        $themeModel = new \ZlyTemplater\Model\Themes();
        $themes = $themeModel->getThemesPaginator(1, 10000);

        $themesList = array();
        foreach ($themes as $theme) {
            $themesList[$theme->getId()] = $theme->getTitle();
        }

        $element = new Element\Select('theme_id');
        $element->setLabel('Theme:')
                ->addMultiOptions($themesList)
                ->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('name');
        $element->setLabel('Layout file:')
                ->setRequired(true);
        $this->addElement($element);

        $element = new Element\Checkbox('published');
        $element->setLabel('Published:');
        $this->addElement($element);

        if($this->locator->instanceManager()->hasAlias('sysmap-service')) {
            $navigator = $this->locator->get('sysmap-service')->getMapFormElement();

            if($navigator instanceof \Zly\Form\Element\Tree) {
                $navigator->setName('map_id');
                $navigator->setMultiple(true);
                $navigator->setRequired();
                $this->addElement($navigator);
            }
        }
        
        $element = new Element\Hidden('module');
        $element->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->setValue($this->_defaultValue);
        $this->addElement($element);

        $element = new Element\Hidden('controller');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Element\Hidden('action');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Element\Submit('submit');
        $element->setLabel('Save')
                ->setIgnore(true);
        $this->addElement($element);
    }

    public function  populate(array $values)
    {
        if(!empty($values['id']))
            $this->setLegend('Edit layout');

        if(!empty($values['points'])) {
            $points = $values['points'];
            unset($values['points']);
            foreach($points as $point)
                $values['map_id'][] = $point->getMapId();
        }

        return parent::populate($values);
    }
    
    public function setLocator($locator)
    {
        $this->locator = $locator;
    }

}