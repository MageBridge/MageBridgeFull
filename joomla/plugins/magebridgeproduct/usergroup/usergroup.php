<?php
/**
 * MageBridge Product plugin - Usergroup
 *
 * @author    Yireo (info@yireo.com)
 * @package   MageBridge
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link      https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product Plugin to assign a customer to a Joomla! Usergroup when a product is purchased
 *
 * @package MageBridge
 */
class PlgMageBridgeProductUsergroup extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'usergroup_id';

	/**
	 * Event "onMageBridgeProductPurchase"
	 *
	 * @param array  $actions
	 * @param object $user   Joomla! user object
	 * @param int    $status Status of the current order
	 * @param string $sku    Magento SKU
	 *
	 * @return bool
	 */
	public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null, $sku = null)
	{
		// Make sure this event is allowed
		if ($this->allowPluginRun($actions) == false)
		{
			return false;
		}

		$userGroupId = (int) $actions['usergroup_id'];

		// Add the user
		if ($this->isUserListed($user, $userGroupId) === false)
		{
			$this->insertNewMapping($user, $userGroupId);
		}

		return true;
	}

	/**
	 * Event "onMageBridgeProductReverse"
	 *
	 * @param array  $actions
	 * @param JUser  $user
	 * @param string $sku Magento SKU
	 *
	 * @return bool
	 */
	public function onMageBridgeProductReverse($actions = null, $user = null, $sku = null)
	{
		// Make sure this event is allowed
		if ($this->allowPluginRun($actions) == false)
		{
			return false;
		}

		$userGroupId = (int) $actions['usergroup_id'];
		$this->removeMapping($user, $userGroupId);

		return true;
	}

	/**
	 * @param $user
	 * @param $userGroupId
	 */
	protected function removeMapping($user, $userGroupId)
	{
		$query = $this->db->getQuery(true);

		$conditions = array(
			$this->db->quoteName('user_id') . ' = ' . (int) $user->id,
			$this->db->quoteName('group_id') . ' = ' . (int) $userGroupId
		);

		$query->delete($this->db->quoteName('#__user_usergroup_map'));
		$query->where($conditions);

		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * @param $user
	 * @param $userGroupId
	 */
	protected function insertNewMapping($user, $userGroupId)
	{
		$entry           = (object) null;
		$entry->user_id  = (int) $user->id;
		$entry->group_id = (int) $userGroupId;
		$this->db->insertObject('#__user_usergroup_map', $entry);
	}

	/**
	 * @param $actions
	 *
	 * @return bool
	 */
	protected function allowPluginRun($actions)
	{
		// Make sure this event is allowed
		if ($this->isEnabled() === false)
		{
			return false;
		}

		// Check for the usergroup ID
		if (!isset($actions['usergroup_id']))
		{
			return false;
		}

		$userGroupId = (int) $actions['usergroup_id'];

		if (empty($userGroupId))
		{
			return false;
		}

		return true;
	}

	/**
	 * @param $user
	 * @param $usergroupId
	 *
	 * @return bool
	 */
	protected function isUserListed($user, $usergroupId)
	{
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('user_id'));
		$query->from($this->db->quoteName('#__user_usergroup_map'));
		$query->where($this->db->quoteName('user_id') . ' = ' . (int) $user->id);
		$query->where($this->db->quoteName('group_id') . ' = ' . (int) $usergroupId);
		$query->setLimit(1, 0);

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		// Add the user
		if (empty($result))
		{
			return false;
		}

		return true;
	}
}

