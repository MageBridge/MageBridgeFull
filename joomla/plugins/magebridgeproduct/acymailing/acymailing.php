<?php
/**
 * Joomla! component MageBridge
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
 * MageBridge Product Plugin for Acymailing
 *
 * @package MageBridge
 */
class PlgMageBridgeProductAcymailing extends MageBridgePluginProduct
{
	/**
	 * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
	 */
	protected $connector_field = 'acymailing_list';

	/**
	 * Method to check whether this connector is enabled or not
	 *
	 * @param null
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if ($this->checkComponent('com_acymailing') === false)
		{
			return false;
		}

		if (!include_once(JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Event "onMageBridgeProductPurchase"
	 *
	 * @param array  $actions
	 * @param JUser  $user   Joomla! user object
	 * @param int    $status Status of the current order
	 * @param string $sku    Magento SKU
	 *
	 * @return bool
	 */
	public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null, $sku = null)
	{
		if ($this->allowPluginRun($actions) === false)
		{
			$this->debug('Purchase: Plugin not allowed');

			return false;
		}

        $actionType = (!empty($actions['acymailing_type'])) ? $actions['acymailing_type'] : 1;

		// Make sure it is not empty
		$listIds = $this->getListIdsFromMixed($actions['acymailing_list']);

		if (empty($listIds))
		{
			$this->debug('Reverse: Empty list ID', $actions);

			return false;
		}

		// See if the user exists in the database
		$acyUser                       = $this->buildAcyUserObject($user);
		$subscriberClass               = $this->getSubscriberClass();
		$subscriberClass->checkVisitor = false;
		$subId                         = $subscriberClass->save($acyUser);

		if (empty($subId))
		{
			return false;
		}

		foreach ($listIds as $listId)
		{
			$newSubscription          = [];
			$newList                  = [];
			$newList['status']        = $actionType;
			$newSubscription[$listId] = $newList;

			$this->debug('Purchase: New subscription', $newSubscription);

			$subscriberClass->saveSubscription($subId, $newSubscription);
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
	public function onMageBridgeProductReverse($actions = null, $user = null)
	{
		if ($this->allowPluginRun($actions) === false)
		{
			$this->debug('Reverse: Plugin not allowed');

			return false;
		}

        if (isset($actions['acymailing_reverse']) && $actions['acymailing_reverse'] == 0)
        {
            return false;
        }

		// Make sure it is not empty
		$listIds = $this->getListIdsFromMixed($actions['acymailing_list']);

		if (empty($listIds))
		{
			$this->debug('Reverse: Empty list ID', $actions);

			return false;
		}

		$subscriberClass = $this->getSubscriberClass();
		$subId           = $subscriberClass->get($user->id);

		foreach ($listIds as $listId)
		{
			$newSubscription          = [];
			$newList                  = [];
			$newList['status']        = 0;
			$newSubscription[$listId] = $newList;

			if ($this->params->get('send_emails', 0) === 0)
			{
				$subscriberClass->sendConf    = false;
				$subscriberClass->sendNotif   = false;
				$subscriberClass->sendWelcome = false;
			}

			$this->debug('Reverse: New subscription', $newSubscription);

			$subscriberClass->saveSubscription($subId, $newSubscription);
		}

		return true;
	}

	/**
	 * @param JUser $user
	 *
	 * @return object
	 */
	protected function buildAcyUserObject($user)
	{
		$acyUser         = (object) null;
		$acyUser->email  = $user->email;
		$acyUser->name   = $user->name;
		$acyUser->userid = $user->id;

		return $acyUser;
	}

	/**
	 * @return subscriberClass
	 */
	protected function getSubscriberClass()
	{
		if (function_exists('acymailing_get'))
		{
			return acymailing_get('class.subscriber');
		}

		return acymailing::get('class.subscriber');
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
		if (!isset($actions['acymailing_list']))
		{
			return false;
		}
	}

	/**
	 * Get a list of IDs
	 *
	 * @param mixed $param
	 *
	 * @return array
	 */
	protected function getListIdsFromMixed($param)
	{
		$listIds = array();

		if (!is_array($param))
		{
			$listId = (int) $param;

			if (!$listId > 0)
			{
				return $listIds;
			}

			$listIds[] = (int) $listId;

			return $listIds;
		}

		foreach ($param as $listId)
		{
			if (!(int) $listId > 0)
			{
				continue;
			}

			$listIds[] = (int) $listId;
		}

		return $listIds;
	}

	/**
	 * @return bool
	 */
	protected function allowDebug()
	{
		return (bool) $this->params->get('debug', 0);
	}

	/**
	 * @param string $message
	 * @param mixed $variable
	 *
	 * @return bool
	 */
	protected function debug($message, $variable = null)
	{
		if ($this->allowDebug() === false)
		{
			return false;
		}

		if (!empty($variable))
		{
			$message .= ' = ' . var_export($variable, true);
		}

		JLog::addLogger(array(
			'text_file' => 'plg_magebridgeproduct_acymailing.debug.php'
		), JLog::ALL, array('plg_magebridgeproduct_acymailing'));

		JLog::add($message, JLog::WARNING, 'plg_magebridgeproduct_acymailing');

		return true;
	}
}


