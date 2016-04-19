<?php
/**
 * Joomla! component MageBridge
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
 * MageBridge Product Plugin for JomSocial Group
 *
 * @package MageBridge
 */
class plgMageBridgeProductJomsocial_group extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'jomsocial_group';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_community');
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

		// Check for the jomsocial_group
		if(!isset($actions['jomsocial_group'])) {
			return false;
		}

		// Make sure it is not empty
		$jomsocial_group = (int)$actions['jomsocial_group'];
		if(!$jomsocial_group > 0) {
			return false;
		}

		// Get the current group-configuration
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM `#__community_groups_members` WHERE `groupid`=".(int)$jomsocial_group." AND `memberid`=".(int)$user->id);
		$rows = $db->loadObjectList();

		if (empty($rows)) {
			$query = "INSERT INTO `#__community_groups_members` SET `groupid`=".(int)$jomsocial_group.", `memberid`=".(int)$user->id.", approved=1, permissions=0";
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

		// Check for the usergroup ID
		if(!isset($actions['jomsocial_group'])) {
			return false;
		}

		// Make sure it is not empty
		$jomsocial_group = (int)$actions['jomsocial_group'];
		if(!$jomsocial_group > 0) {
			return false;
		}

		// Get the current group-configuration
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM `#__community_groups_members` WHERE `groupid`=".(int)$jomsocial_group." AND `memberid`=".(int)$user->id);
		$rows = $db->loadObjectList();

		if (empty($rows)) {
			$query = "INSERT INTO `#__community_groups_members` SET `groupid`=".(int)$jomsocial_group.", `memberid`=".(int)$user->id.", approved=1, permissions=0";
			$db->setQuery($query);
			$db->query();
		} else {
			$query = "UPDATE `#__community_groups_members` SET approved=1, permissions=0 WHERE `groupid`=".(int)$jomsocial_group." AND `memberid`=".(int)$user->id;
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}
}

