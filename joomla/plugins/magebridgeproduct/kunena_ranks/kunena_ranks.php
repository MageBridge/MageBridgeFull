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
 * MageBridge Product Plugin for Kunena Ranks
 *
 * @package MageBridge
 */
class plgMageBridgeProductKunena_ranks extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'kunena_rank';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_kunena');
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

		// Check for the kunena_rank
		if(!isset($actions['kunena_rank'])) {
			return false;
		}

		// Make sure it is not empty
		$kunena_rank = (int)$actions['kunena_rank'];
		if(!$kunena_rank > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// See if the user is already there
		$query = 'SELECT id FROM `#__kunena_users` WHERE `userid`='.(int)$user->id.' LIMIT 1';
		$db->setQuery($query);
		$user_id = $db->loadResult();

		// Add the customer email to the subscribers list
		if ($user_id > 0) {
			$query = 'UPDATE `#__kunena_users` SET `rank`='.(int)$kunena_rank.' WHERE `userid`='.(int)$user->id;
		} else {
			$query = 'INSERT INTO `#__kunena_users` SET `userid`='.(int)$user->id.', `rank`='.(int)$kunena_rank;
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
		if(!isset($actions['kunena_rank'])) {
			return false;
		}

		// Make sure it is not empty
		$kunena_rank = (int)$actions['kunena_rank'];
		if(!$kunena_rank > 0) {
			return false;
		}

		$db = JFactory::getDBO();
		$query = 'UPDATE `#__kunena_users` SET `rank`=0 WHERE `userid`='.(int)$user->id;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}

