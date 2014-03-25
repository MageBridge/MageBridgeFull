<?php
/**
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

// Require all the neccessary libraries
require_once JPATH_ADMINISTRATOR.'/components/com_magebridge_importer/helpers/helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_magebridge_importer/libraries/loader.php';
require_once JPATH_SITE.'/components/com_magebridge/libraries/factory.php';
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

// Detect access
$user = JFactory::getUser();
if ($user->authorise('core.edit', 'com_content') == false) {
    JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
}

// Require the controller
require_once JPATH_COMPONENT.'/controller.php';
$controller = new MageBridgeImporterController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
