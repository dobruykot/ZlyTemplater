<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 */
namespace ZlyTemplater\Form;

use \Zend\Form\Element as Element;

class Theme extends \Zend\Form\Form
{
    protected $model;

    public function init()
    {
        $this->setMethod('POST');
        $element = new Element\Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Select('name');
        $element->setLabel('Directory:');
        $element->setRequired(true);
        $element->addMultiOptions($this->model->getThemesDirectoriesFromFS());
        $this->addElement($element);

        $element = new Element\Radio('active');
        $element->setSeparator('&nbsp;');
        $element->setLabel('Active:');
        $element->setValue(false);
        $element->setMultiOptions(array('1' => 'Yes', '0' => 'No'));
        $element->setRequired(true);
        $this->addElement($element);
        
        $element = new Element\Text('ordering');
        $element->setLabel('Ordering:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Submit('submit');
        $element->setLabel('Save');
        $element->setIgnore(true);
        $this->addElement($element);
    }

    public function setModel($model)
    {
        $this->model = $model;
    }
}