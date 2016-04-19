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
 * MageBridge Product Plugin for RsFiles
 *
 * @package MageBridge
 */
class plgMageBridgeProductRsFiles extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'rsfiles_group';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_rsfiles');
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

		// Check for the rsfiles_group
		if(!isset($actions['rsfiles_group'])) {
			return false;
		}

		// Make sure it is not empty
		$rsfiles_group = (int)$actions['rsfiles_group'];
		if(!$rsfiles_group > 0) {
			return false;
		}

		// Get the RSFiles group
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__rsfiles_group_details` WHERE `IdGroup`=".(int)$rsfiles_group;
		$db->setQuery($query);
		$row = $db->loadObject();

		if (empty($row)) {
			$query = "INSERT INTO `#__rsfiles_group_details` SET `IdGroup`=".(int)$rsfiles_group.", `IdUsers`=".(int)$user->id;

		} else {

			// Construct the new users-list
			if (empty($row->IdUsers)) {
				$users = $user->id;
			} else {
				$user_ids = explode(',', $row->IdUsers);
				$user_ids[] = $user->id;
				$users = implode(',', $user_ids);
			}

			$query = "UPDATE `#__rsfiles_group_details` SET `IdUsers`=".$users." WHERE `IdGroup`=".(int)$rsfiles_group;
		}

		$db->setQuery($query);
		$db->query();

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
		if(!isset($actions['rsfiles_group'])) {
			return false;
		}

		// Make sure it is not empty
		$rsfiles_group = (int)$actions['rsfiles_group'];
		if(!$rsfiles_group > 0) {
			return false;
		}

		// Delete this user from the group
		$db = JFactory::getDBO();
		$query = "DELETE FROM FROM `#__flexiaccess_members` WHERE `group_id`=".(int)$rsfiles_group." AND `member_id`=".(int)$user->id;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}

