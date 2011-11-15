<?php

/**
 *	Zly
 *
 * @abstract   contains Templater_ToolsController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: ToolsController.php 763 2010-12-14 12:21:26Z deeper $
 */

namespace ZlyTemplater\Controller;

use Zend\Mvc\Controller\ActionController;

class ToolsController extends ActionController
{
    /**
     * Display flash system messages
     *
     * @Qualifier Templater_Form_FlashMessage
     */
    public function displayFlashMessagesAction()
    {
        $messages = $this->flashMessenger(array())->getMessages();
        return array('messages' => $messages);
    }
}