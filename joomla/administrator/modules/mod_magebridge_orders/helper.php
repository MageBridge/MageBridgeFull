<?php
/**
 * Joomla! module Magento Bridge: Orders
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Helper-class for the module
 */
class modMageBridgeOrdersHelper extends MageBridgeModuleHelper
{
	/**
	 * Method to be called once the MageBridge is loaded
	 */
	static public function register($params = null)
	{
		return array(
			array('api', 'magebridge_order.list'),
		);
	}

	/**
	 * Fetch the content from the bridge
	 */
	static public function build()
	{
		return parent::getCall('getAPI', 'magebridge_order.list');
	}
}
