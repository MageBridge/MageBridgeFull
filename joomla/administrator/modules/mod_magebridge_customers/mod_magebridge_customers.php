<?php
/**
 * Joomla! module Magento Bridge: Latest Customers
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */
		
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';
require_once 'helper.php';

// Read the parameters
$count = $params->get('count', 5);

// Include the MageBridge bridge
$customers = modMageBridgeCustomersHelper::build();

// Display the template
require(JModuleHelper::getLayoutPath('mod_magebridge_customers'));
