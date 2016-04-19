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
 * MageBridge Product Plugin for JomSocial User Points
 *
 * @package MageBridge
 */
class plgMageBridgeProductJomsocial_userpoints extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'jomsocial_points';

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

		// Check for the jomsocial_points
		if(!isset($actions['jomsocial_points'])) {
			return false;
		}

		// Make sure it is not empty
		$jomsocial_points = (int)$actions['jomsocial_points'];
		if(!$jomsocial_points > 0) {
			return false;
		}

		// @todo: Is this correct? Shouldn't we calculate the difference with existing points?

		// Update the points
		$db = JFactory::getDBO();
		$query = "UPDATE `#__community_users` SET `points`=".(int)$jomsocial_points." WHERE `userid`=".(int)$user->id;
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
		if(!isset($actions['jomsocial_points'])) {
			return false;
		}

		// Make sure it is not empty
		$jomsocial_points = (int)$actions['jomsocial_points'];
		if(!$jomsocial_points > 0) {
			return false;
		}

		// @todo: Update the points

		return true;
	}
}

