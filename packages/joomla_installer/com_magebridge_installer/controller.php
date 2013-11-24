<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (https://www.yireo.com/)
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Include the parent controller
jimport( 'joomla.application.component.controller' );

// Include other libraries
require_once( JPATH_SITE.'/components/com_magebridge/models/config.php' );

/**
 * MageBridge Controller
 *
 * @package MageBridge
 */
class MageBridgeController extends MagebridgeControllerAbstract
{
    /**
     * Constructor
     * @access private
     * @package MageBridge
     */
    public function __construct()
    {
        parent::__construct();

        // If no task has been set, try the default
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'update');
        }
    }

    public function apply()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $post = JRequest::get('post');
        $model = $this->getModel('config');
        $model->store($post);

        $link = JRoute::_('index.php?option=com_magebridge');
        $this->setRedirect($link);
    }

    public function update()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $post = JRequest::get('post');
        $model = $this->getModel('config');
        $model->store($post);
        $supportkey = $post['supportkey'];

        $model = $this->getModel('update');
        $model->setSupportKey($supportkey);
        $model->updateAll();

        $link = JRoute::_('index.php?option=com_magebridge');
        $this->setRedirect($link);
    }
}
