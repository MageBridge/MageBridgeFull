<?php
/**
 * Joomla! module MageBridge: Advertisement block
 *
 * @author	Yireo (info@yireo.com)
 * @package   MageBridge
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link	  http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Read the parameters
$layout = $params->get('layout', 'default');

// Call the helper
require_once(dirname(__FILE__) . '/helper.php');
$product = modMageBridgeAdvertisementHelper::build($params);

// Add CSS and JavaScript 
$templateHelper = new MageBridgeTemplateHelper();

if ($layout == 'slideshow')
{
	if ($params->get('load_default_css', 1) == 1)
	{
		$templateHelper->load('css', 'mod-advertisement-default.css');
	}
}

// Include the layout-file
require(JModuleHelper::getLayoutPath('mod_magebridge_advertisement', $layout));
