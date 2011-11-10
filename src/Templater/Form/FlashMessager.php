<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: FlashMessager.php 269 2010-10-05 13:38:46Z deeper $
 */

namespace Templater\Form;

class FlashMessager extends \Zend\Form\Form
{
	public function  init()
	{
		$this->setDecorators(array('Description', 'FormElements','Errors'));

		$element = new \Zend\Form\Element\TextBox('type_id');
		$element->setLabel('Type:');
		$element->setRequired(true);
		$this->addElement($element);

		$element = new \Zend\Form\Element\RadioButton('current');
		$element->setSeparator('&nbsp;');
		$element->setLabel('Current:');
		$element->setMultiOptions(array('1'=>'Yes','0'=>'No'));
		$element->setRequired(true);
		$this->addElement($element);

		$this->setDescription('Flash messages parameters:');
	}
}