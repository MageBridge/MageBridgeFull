<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (https://www.yireo.com/)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link https://www.yireo.com/
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the helper
require_once (JPATH_SITE.'/components/com_magebridge/helpers/helper.php');

/* Abstract models */
if(MageBridgeHelper::isJoomla35()) {
    class MagebridgeModelAbstract extends JModelLegacy {}
    class MagebridgeControllerAbstract extends JControllerLegacy {}
    class MagebridgeViewAbstract extends JViewLegacy {}
} else {
    jimport('joomla.application.component.model');
    jimport('joomla.application.component.controller');
    jimport('joomla.application.component.view');
    class MagebridgeModelAbstract extends JModel {}
    class MagebridgeControllerAbstract extends JController {}
    class MagebridgeViewAbstract extends JView {}
}

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');
$controller = new MageBridgeController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();

