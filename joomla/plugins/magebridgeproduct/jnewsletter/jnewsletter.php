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
 * MageBridge Product Plugin for jNewsletter
 *
 * @package MageBridge
 */
class plgMageBridgeProductJnewsletter extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'jnews_list';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_jnewsletter');
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

		// Check for the jnews_list
		if(!isset($actions['jnews_list'])) {
			return false;
		}

		// Make sure it is not empty
		$jnews_list = (int)$actions['jnews_list'];
		if(!$jnews_list > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// See if the user is already there
		$query = 'SELECT * FROM `#__jnews_listssubscribers` WHERE `list_id`='.(int)$jnews_list.' AND `subscriber_id`='.(int)$user->id;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// Add the customer email to the subscribers list
		if (empty($rows)) {
			// @todo: Use unsubdate feature of jNews for automatic reversal
			$query = 'INSERT INTO `#__jnews_listssubscribers` SET `list_id`='.(int)$jnews_list.', `subscriber_id`='.(int)$user->id.', `subdate`=NOW()';
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
		if(!isset($actions['jnews_list'])) {
			return false;
		}

		// Make sure it is not empty
		$jnews_list = (int)$actions['jnews_list'];
		if(!$jnews_list > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// See if the user is already there
		$query = 'DELETE FROM `#__jnews_listssubscribers` WHERE `list_id`='.(int)$jnews_list.' AND `subscriber_id`='.(int)$user->id;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}

