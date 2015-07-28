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
 * MageBridge Product Plugin for Ohanah
 *
 * @package MageBridge
 */
class plgMageBridgeProductOhanah extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'ohanah_event';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_ohanah');
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

		// Check for the ohanah_event
		if(!isset($actions['ohanah_event'])) {
			return false;
		}

		// Make sure it is not empty
		$ohanah_event = (int)$actions['ohanah_event'];
		if(!$ohanah_event > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// See if the user is already subscribed
		$query = 'SELECT * FROM `#__ohanah_registrations` WHERE `email`='.$user->email.' AND `ohanah_event_id`='.(int)$ohanah_event;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// Add the customer email to the subscribers list
		if (empty($rows)) {

			$values = array(
				'ohanah_event_id' => (int)$ohanah_event,
				'name' => $user->name,
				'email' => $user->email,
				'number_of_tickets' => 1,
				'paid' => 1,
				'checked_in' => 0,
			);

			$query_values = array();
			foreach ($values as $name => $value) {
				$query_values[] = "`$name`=".$db->Quote($value);
			}

			$query = 'INSERT INTO `#__ohanah_registrations` SET '.implode(',', $query_values);
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
		if(!isset($actions['ohanah_event'])) {
			return false;
		}

		// Make sure it is not empty
		$ohanah_event = (int)$actions['ohanah_event'];
		if(!$ohanah_event > 0) {
			return false;
		}

		$db = JFactory::getDBO();

		// See if the user is already there
		$query = 'DELETE FROM `#__ohanah_registrations` WHERE `email`='.$db->Quote($user->email).' AND `ohanah_event_id`='.(int)$ohanah_event;
		$db->setQuery($query);
		$db->query();

		return true;
	}
}

