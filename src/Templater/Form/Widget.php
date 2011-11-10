<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Widget.php 1065 2011-01-20 10:04:22Z deeper $
 */
namespace Templater\Form;

use \Zend\Form\Element as Element;

class Widget extends \Zend\Form\Form
{
    /**
     * @var \Templater\Model\Themes 
     */
    protected $model;
    
    protected $locator;
    /**
     * Form initialization
     */
    public function init()
    {
        $this->setMethod('POST');

        $themes = $this->model->getlist();

        $themesList = array();
        foreach ($themes as $theme) {
            $layoutsList = array();

            foreach ($theme->getLayouts() as $layout)
                $layoutsList[$layout->getId()] = $layout->getTitle();

            $themesList[$theme->getTitle()] = $layoutsList;
        }

        $element = new Element\Select('layout_id');
        $element->setLabel('Theme layout:')
                ->addMultiOptions($themesList)
                ->addDecorator('fieldset')
                ->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('name');
        $element->setLabel('Widget name:')
                ->setRequired(true);
        $this->addElement($element);


        $element = new Element\Text('placeholder');
        $element->setLabel('Position:')
                ->setRequired(true);
        $this->addElement($element);

        $this->addDisplayGroup(array('placeholder','name'), 'place', array('order' => 2, 'legend' => 'Display options:'));

         if($this->locator->instanceManager()->hasAlias('sysmap-service')) {
            $sysmapElement = $this->locator->get('sysmap-service')->getMapFormElement();

            if(!empty($sysmapElement)) {
                $actionNavigator = clone $sysmapElement;

                if ($actionNavigator instanceof \Zly\Form\Element\Tree) {
                    $actionNavigator->setName('map_id');
                    $actionNavigator->setRequired();
                    $actionNavigator->setLabel('Widget content provider action:');
                    $actionNavigator->addDisableCondition('level', new \Zend\Validator\LessThan(3));
                    $this->addElement($actionNavigator);
                }

                $displayNavigator = clone $sysmapElement;

                if ($displayNavigator instanceof \Zly\Form\Element\Tree) {
                    $displayNavigator->setName('widget_points');
                    $displayNavigator->setLabel('Widget display pages:');
                    $displayNavigator->setRequired();
                    $displayNavigator->setMultiple(true);
                    $this->addElement($displayNavigator);
                }
            }
         }
        $element = new Element\Text('ordering');
        $element->addValidator(new \Zend\Validator\Int())
                ->setRequired(true)
                ->setLabel('Ordering:')
                ->setAttrib('style', 'width:50px;');
        $this->addElement($element);

        $element = new Element\Checkbox('published');
        $element->setLabel('Published:');
        $this->addElement($element);

        $this->addDisplayGroup(array('ordering', 'published'), 'other',
                array('legend' => 'Publishing options:'));


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
        $element->setLabel('Save');

        $element->setIgnore(true);
        $this->addElement($element);

        foreach ($this->getDisplayGroups() as $group) {
            $group->setDecorators(array('description', 'FormElements', 'fieldset'));
        }

        foreach ($this->getSubForms() as $group) {
            $group->setDecorators(array('description', 'FormElements', 'fieldset'));
        }
    }

    public function populate(array $values)
    {
        if (!empty($values['id']))
            $this->setLegend('Edit Widget');

        if(!empty($values['points'])) {
            $points = $values['points'];
            foreach($points as $point)
                $values['widget_points'][] = $point->getMapId();
        }

        return parent::populate($values);
    }
    
    public function setModel($model) 
    {
        $this->model = $model;
    }

    public function setLocator($locator) 
    {
        $this->locator = $locator;
    }

}