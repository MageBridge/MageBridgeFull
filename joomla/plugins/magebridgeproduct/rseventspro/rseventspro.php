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
 * MageBridge Product Plugin for RsEvents Pro
 *
 * @package MageBridge
 */
class plgMageBridgeProductRseventspro extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'rseventspro_event';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_rseventspro');
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

		// Check for the rseventspro_event
		if(!isset($actions['rseventspro_event'])) {
			return false;
		}

		// Make sure it is not empty
		$rseventspro_event = (int)$actions['rseventspro_event'];
		if(!$rseventspro_event > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// Check for a valid user
		$query = 'SELECT `id` FROM `#__rseventspro_users` WHERE `ide`='.(int)$rseventspro_event.' AND `idu`='.(int)$user->id.' LIMIT 1';
		$db->setQuery($query);
		$user_event_id = $db->loadResult();

		// Create the user if needed
		if(empty($user_id)) {
			$query_values = array();
			$query_values[] = '`idu`='.(int)$user->id;
			$query_values[] = '`ide`='.(int)$rseventspro_event;
			$query_values[] = '`name`='.(int)$user->name;
			$query_values[] = '`email`='.(int)$user->email;
			$query_values[] = '`date`='.date('Y-m-d H:i:s');
			$query_values[] = '`state`=1';
			$query = 'INSERT INTO `#__rseventspro_users` SET '.implode(',', $query_values);
			$db->setQuery($query);
			$db->query();
			$user_event_id = $db->insertId();
		}

		// See if the user is already subscribed
		$query = 'SELECT * FROM `#__rseventspro_user_tickets` WHERE `ide`='.(int)$user_event_id;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// Add the customer email to the subscribers list
		if (empty($rows)) {

			$values = array(
				'ids' => (int)$user_event_id,
				'quantity' => 1,
			);

			$query_values = array();
			foreach ($values as $name => $value) {
				$query_values[] = "`$name`=".$db->Quote($value);
			}

			$query = 'INSERT INTO `#__rseventspro_user_tickets` SET '.implode(',', $query_values);
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
		if(!isset($actions['rseventspro_event'])) {
			return false;
		}

		// Make sure it is not empty
		$rseventspro_event = (int)$actions['rseventspro_event'];
		if(!$rseventspro_event > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// Check for a valid user
		$query = 'SELECT `id` FROM `#__rseventspro_users` WHERE `ide`='.(int)$rseventspro_event.' AND `idu`='.(int)$user->id.' LIMIT 1';
		$db->setQuery($query);
		$user_event_id = $db->loadResult();

		// Delete the user-record
		$query = 'DELETE FROM `#__rseventspro_users` WHERE `ide`='.(int)$rseventspro_event.' AND `idu`='.(int)$user->id;
		$db->setQuery($query);
		$db->query();

		// Delete the ticket-record
		$query = 'DELETE FROM `#__rseventspro_user_tickets` WHERE `id`='.(int)$user_event_id;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}

