<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product Plugin for FLEXI Access
 *
 * @package MageBridge
 */
class plgMageBridgeProductFlexiaccess extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'flexiaccess_group';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_flexiaccess');
	}

	/**
	 * Event "onMageBridgeProductPurchase"
	 * 
	 * @access public
	 * @param array $actions
	 * @param object $user Joomla! user object
	 * @param tinyint $status Status of the current order
	 * @param string $sku Magento SKU
	 */
	public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null, $sku = null)
	{
		// Make sure this event is allowed
		if($this->isEnabled() == false) {
			return false;
		}

		// Check for the usergroup ID
		if(!isset($actions['flexiaccess_group'])) {
			return false;
		}

		// Make sure it is not empty
		$flexiaccess_group = (int)$actions['flexiaccess_group'];
		if(!$flexiaccess_group > 0) {
			return false;
		}

		// Get the FLEXIaccess group
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__flexiaccess_members` WHERE `group_id`=".(int)$flexiaccess_group." AND `member_id`=".(int)$user->id;
		$db->setQuery($query);
		$row = $db->loadObject();

		if (empty($row)) {
			$query = "INSERT INTO `#__flexiaccess_members` SET `group_id`=".(int)$flexiaccess_group.", `member_id`=".(int)$user->id;
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}

	/**
	 * Event "onMageBridgeProductReverse"
	 * 
	 * @param array $actions
	 * @param JUser $user
	 * @param string $sku Magento SKU
	 * @return bool
	 */
	public function onMageBridgeProductReverse($actions = null, $user = null, $sku = null)
	{
		// Make sure this event is allowed
		if($this->isEnabled() == false) {
			return false;
		}

		// Check for the flexiaccess_group ID
		if(!isset($actions['flexiaccess_group'])) {
			return false;
		}

		// Make sure it is not empty
		$flexiaccess_group = (int)$actions['flexiaccess_group'];
		if(!$flexiaccess_group > 0) {
			return false;
		}

		// Delete this user from the group
		$db = JFactory::getDBO();
		$query = "DELETE FROM FROM `#__flexiaccess_members` WHERE `group_id`=".(int)$flexiaccess_group." AND `member_id`=".(int)$user->id;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}