<?php

/**
 * Zly
 *
 * @abstract    contains Templater_AdminController class,
 *              extending Zend_Controller_Action
 * @author      Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version     $Id: AdminController.php 1183 2011-02-07 08:38:38Z deeper $
 */
namespace ZlyTemplater\Controller;

use Zend\Mvc\Controller\ActionController,
    ZlyTemplater\Model as Model;
/**
 * Themes administrator panel
 */
class AdminController extends ActionController
{

    /**
     * Display templater admin dashboard
     */
    public function indexAction()
    {
        
    }

    /**
     * THEMES SECTION
     */

    /**
     * Themes list action
     */
    public function themesAction()
    {
        $themesModel = $this->getLocator()->get('ZlyTemplater\Model\Themes');
        return array('themes' => $themesModel->getThemesPaginator(
            $this->getRequest()->getMetadata('page', 1),
            $this->getRequest()->getMetadata('perPage', 20)
        ));
    }

    /**
     * Edit Theme action
     * @return null
     */
    public function editThemeAction()
    {
        $themesModel = $this->getLocator()->get('ZlyTemplater\Model\Themes');
        $config = $this->getEvent();

        $theme = $themesModel->getTheme($this->getRequest()->getMetadata('id'), true);
        
        $form = $this->getLocator()->get('ZlyTemplater\Form\Theme', array('options'=>array('model'=>$themesModel)));
        $form->populate($theme->toArray());
        
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->post()->toArray())) {
            try{
                $themesModel->saveTheme($theme, $form->getValues());
            }catch(Exception $exception) {
                $this->flashMessenger(array())->addMessage($exception->getMessage());
            }
            
            $this->flashMessenger(array())->addMessage('Theme successful saved.');
            $this->redirect(array())->toRoute('default',array('action'=>'themes','controller'=>'templater-admin'));
        }
        return array('editThemeForm' => $form);
    }

    /**
     * Delete Theme action
     */
    public function deleteThemeAction()
    {
        $model = $this->getLocator()->get('ZlyTemplater\Model\Themes');
        try {
            $result = $model->deleteTheme($this->getRequest()->getMetadata('id'));
            if ($result)
            $this->flashMessenger(array())->addMessage('Theme successful deleted.');
        } catch(Exception $exception) {
            $this->flashMessenger(array())->addMessage($exception->getMessage());
        }
        $this->redirect(array())->toRoute('default',array('action'=>'themes','controller'=>'templater-admin'));
    }

    /**
     * LAYOUTS SECTIONS
     */

    /**
     * Layouts list action
     */
    public function layoutsAction()
    {
        $tplId = $this->getRequest()->getMetadata('tpl', null);
        $layoutsModel = $this->getLocator()->get('ZlyTemplater\Model\Layouts');
        $where = array();
        if (!empty($tplId))
            $where['theme_id'] = $tplId;
            return array('layouts' => $layoutsModel->getLayoutsPaginator(
                $this->getRequest()->getMetadata('page', 1),
                $this->getRequest()->getMetadata('perPage', 20),
                $where
        ));
    }

    /**
     * Edit Theme action
     * @return null
     */
    public function editLayoutAction()
    {
        $layoutModel = $this->getLocator()->get('ZlyTemplater\Model\Layouts');
        $layout = $layoutModel->getLayout($this->getRequest()->getMetadata('id'), true);
        $form = $this->getLocator()->get('ZlyTemplater\Form\Layout', array('options'=>array('locator'=>$this->getLocator())));
        $form->populate($layout->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->post()->toArray())) {
            $layoutModel->saveLayout($layout, $form->getValues());
            $this->flashMessenger(array())->addMessage('Layout successful saved.');
            $this->redirect(array())->toRoute('default',array('action'=>'layouts','controller'=>'templater-admin'));
        }
        return array('editLayoutForm' => $form);
    }

    /**
     * Delete widget action
     */
    public function deleteLayoutAction()
    {
        $model = $this->getLocator()->get('ZlyTemplater\Model\Layouts');
        try{
            $result = $model->deleteLayout($this->getRequest()->getParam('id'), $this->getRequest());
            if ($result)
                $this->flashMessenger(array())->addMessage('Layout successful deleted.');
        } catch(Exception $exception) {
            $this->flashMessenger(array())->addMessage($exception->getMessage());
        }
        
        $this->redirect(array())->toRoute('default',array('action'=>'layouts','controller'=>'templater-admin'));
    }

  
    /**
     * WIDGETS SECTION
     */

    /**
     * Widgets list action
     */
    public function widgetsAction()
    {
        $widgetsModel = $this->getLocator()->get('ZlyTemplater\Model\Widgets');
        return array('widgets' => $widgetsModel->getWidgetsPaginator(
            $this->getRequest()->getMetadata('page', 1),
            $this->getRequest()->getMetadata('perPage', 20)
        ));
    }

    /**
     * Edit widget action
     * @return null
     */
    public function editWidgetAction()
    {
        $widgetsModel = $this->getLocator()->get('ZlyTemplater\Model\Widgets');
        $themesMmodel =$this->getLocator()->get('ZlyTemplater\Model\Themes');
        $form = $this->getLocator()->get('ZlyTemplater\Form\Widget', array('options'=>array('model'=>$themesMmodel, 'locator'=>$this->getLocator())));
        $widget = $widgetsModel->getWidget($this->getRequest()->getMetadata('id'), true);
        $form->populate($widget->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->post()->toArray())) {
            $result = $widgetsModel->saveWidget($widget, $form->getValues());
            $this->flashMessenger(array())->addMessage('Widget successful saved.');
            $this->redirect(array())->toRoute('default',array('action'=>'widgets','controller'=>'templater-admin'));
        }
        return array('editWidgetForm' => $form);
    }

    /**
     * Delete widget action
     */
    public function deleteWidgetAction()
    {
        $widgetsModel = $this->getLocator()->get('ZlyTemplater\Model\Widgets');
        $result = $widgetsModel->deleteWidget($this->getRequest()->getMetadata('id'));
        if($result)
            $this->flashMessenger(array())->addMessage('Widget successful saved.');
        $this->redirect(array())->toRoute('default',array('action'=>'widgets','controller'=>'templater-admin'));
    }

}