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
 * MageBridge Product Plugin for jNews
 *
 * @package MageBridge
 */
class plgMageBridgeProductJnews extends MageBridgePluginProduct
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
		return $this->checkComponent('com_jnews');
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

		// See if the user is already listed in the subscribers-table
		$query = 'SELECT * FROM `#__jnews_subscribers` WHERE `user_id`='.(int)$user->id.' LIMIT 1';
		$db->setQuery($query);
		$row = $db->loadObject();

		// Add the customer to the subscribers-table
		$subscriber_id = 0;
		if (empty($rows)) {
			$fields = array(
				'`user_id`='.(int)$user->id,
				'`name`='.$db->Quote($user->name),
				'`email`='.$db->Quote($user->email),
				'`receive_html`=1',
				'`confirmed`=1',
				'`subscribe_date`='.time(),
			);
			$query = 'INSERT INTO `#__jnews_subscribers` SET '.implode(', ', $fields);
			$db->setQuery($query);
			$db->query();

			// See if the user is already listed in the subscribers-table
			$query = 'SELECT * FROM `#__jnews_subscribers` WHERE `user_id`='.(int)$user->id.' LIMIT 1';
			$db->setQuery($query);
			$row = $db->loadObject();
			if (!empty($row->id)) {
				$subscriber_id = $row->id;
			}

		} else {
			$subscriber_id = $row->id;
		}

		if ($subscriber_id > 0) {

			// See if this subscriber is already
			$query = 'SELECT * FROM `#__jnews_listssubscribers` WHERE `list_id`='.(int)$jnews_list.' AND `subscriber_id`='.(int)$subscriber_id.' LIMIT 1';
			$db->setQuery($query);
			$row = $db->loadObject();

			// Add the customer email to the subscribers list
			if (empty($row)) {
				// @todo: Use unsubdate feature of jNews for automatic reversal
				$fields = array(
					'`list_id`='.(int)$jnews_list,
					'`subscriber_id`='.(int)$subscriber_id,
					'`subdate`=NOW()',
				);
				$query = 'INSERT INTO `#__jnews_listssubscribers` SET '.implode(', ', $fields);
				$db->setQuery($query);
				$db->query();
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

