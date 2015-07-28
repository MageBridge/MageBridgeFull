<?php
/**
 * Joomla! MageBridge - Magento plugin for jNews
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Newsletter Plugin for jNews
 */
class plgMageBridgeNewsletterJnews extends MageBridgePluginMagento
{
	/**
	 * Event "onNewsletterSubscribe"
	 * 
	 * @access private
	 * @param null
	 * @return int
	 */
	public function onNewsletterSubscribe($user, $state)
	{
		if ($this->isEnabled() == false) {
			return false;
		}

		$list_id = $this->params->get('newsletter');
		if (empty($list_id)) {
			return true;
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
			$query = 'SELECT * FROM `#__jnews_listssubscribers` WHERE `list_id`='.(int)$list_id.' AND `subscriber_id`='.(int)$subscriber_id.' LIMIT 1';
			$db->setQuery($query);
			$row = $db->loadObject();

			// Add the customer email to the subscribers list
			if (empty($row)) {
				// @todo: Use unsubdate feature of jNews for automatic reversal
				$fields = array(
					'`list_id`='.(int)$list_id,
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
	 * Method to check whether this plugin is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_jnews');
	}
}

