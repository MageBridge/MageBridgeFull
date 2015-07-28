<?php
/**
 * MageBridge Product plugin - EmailScheduler
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
 * MageBridge Product Plugin to mail a Joomla! article when a customer buys a product
 *
 * @package MageBridge
 */
class plgMageBridgeProductEmailscheduler extends MageBridgePluginProduct
{
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

		// Check for the article ID
		if(!isset($actions['article_id'])) {
			return false;
		}

		// Make sure the article is not empty
		$article_id = (int)$actions['article_id'];
		if(!$article_id > 0) {
			return false;
		}

		// Set the article
		require_once JPATH_ADMINISTRATOR.'/components/com_emailscheduler/api.php';
		$email = new EmailScheduler();
		$email->setArticle($article_id);

		// Set the template
		$template_id = (isset($actions['template_id'])) ? (int)$actions['template_id'] : 0;
		$email->setTemplate($template_id);

		// Set the recipients
		$recipients = array('to' => $user->email);
		if(!empty($actions['bcc'])) $recipients['bcc'] = $actions['bcc'];
		$email->setRecipients($recipients);

		// Save this mail with a bit of delay
		$delay = (isset($actions['delay'])) ? (int)$actions['delay'] : 0;
		$email->setSendDate(time() + $delay);
		$email->save();

		return true;
	}
}
