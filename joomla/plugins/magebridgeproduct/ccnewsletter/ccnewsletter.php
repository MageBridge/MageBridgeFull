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
 * MageBridge Product Plugin for ccNewsletter
 *
 * @package MageBridge
 */
class plgMageBridgeProductCcnewsletter extends MageBridgePluginProduct
{
	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_ccnewsletter');
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

		// See if the user is already there
		$query = 'SELECT * FROM `#__ccnewsletter_subscribers` WHERE `email`='.$this->db->Quote($user->email);
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();

		// Add the customer email to the subscribers list
		if (empty($rows)) {
			$query = 'INSERT INTO `#__ccnewsletter_subscribers` SET `name`='.$this->db->Quote($user->name).', `email`='.$this->db->Quote($user->email).', `sdate`=NOW()';
			$this->db->setQuery($query);
			$this->db->query();
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

		// See if the user is already there
		$query = 'DELETE FROM `#__ccnewsletter_subscribers` WHERE `email`='.$this->db->Quote($user->email);
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}
}

