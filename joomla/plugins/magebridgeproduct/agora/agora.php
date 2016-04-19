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
 * MageBridge Product Plugin for Agora
 *
 * @package MageBridge
 */
class plgMageBridgeProductAgora extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'agora_group';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_agora');
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
		if(!isset($actions['agora_group'])) {
			return false;
		}

		// Make sure it is not empty
		$agora_group = (int)$actions['agora_group'];
		if(!$agora_group > 0) {
			return false;
		}

		// Get the Agora user
		$query = "SELECT * FROM `#__agora_users` WHERE `jos_id`=".(int)$user->id;
		$this->db->setQuery($query);
		$row = $this->db->loadObject();

		// If the Agora user does not exist yet, create it
		if (empty($row)) {
			$query = "INSERT INTO `#__agora_users` SET `jos_id`=".(int)$user->id.", `username`=".$this->db->Quote($user->username).", `email`=".$this->db->Quote($user->email);
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT * FROM `#__agora_users` WHERE `jos_id`=".(int)$user->id;
			$this->db->setQuery($query);
			$row = $this->db->loadObject();
		}

		// Check whether the Agora user is linked already to the Agora group
		$userid = $row->id;
		if ($userid > 0) {
			$query = "SELECT * FROM `#__agora_user_group` WHERE `user_id`=".(int)$userid." AND `group_id`=".(int)$agora_group;
			$this->db->setQuery($query);
			$row = $this->db->loadObject();

			if (empty($row)) {
				$query = "INSERT INTO `#__agora_user_group` SET `user_id`=".(int)$userid.", `group_id`=".(int)$agora_group;
				$this->db->setQuery($query);
				$this->db->query();
			}
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
	public function onMageBridgeProductReverse($actions = null, $user = null)
	{
		// Make sure this event is allowed
		if($this->isEnabled() == false) {
			return false;
		}

		// Check for the usergroup ID
		if(!isset($actions['agora_group'])) {
			return false;
		}

		// Make sure it is not empty
		$agora_group = (int)$actions['agora_group'];
		if(!$agora_group > 0) {
			return false;
		}

		// Delete this user from the group
		$query = "DELETE FROM FROM `#__agora_user_group` WHERE `group_id`=".(int)$agora_group." AND `user_id` IN (SELECT id FROM `#__agora_users WHERE `jos_id`=".(int)$user->id.")";
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}
}

