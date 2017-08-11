<?php
/**
 * Joomla! MageBridge - Magento plugin for Acymailing
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */
		
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Newsletter Plugin for Acymailing
 */
class PlgMageBridgenewsletterAcymailing extends MageBridgePluginMagento
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

		$list_ids = $this->params->get('newsletter');

		if (empty($list_ids)) {
			$this->debug('onNewsletterSubscribe: No lists configured');
			return true;
		}
		
		if (!include_once(rtrim(JPATH_ADMINISTRATOR,'/').'/components/com_acymailing/helpers/helper.php')){
			$this->debug('onNewsletterSubscribe: Acymailing not found');
			return false;
		}

		// Get the subscriber ID (and generate the subscriber on the fly)
		$subid = $this->returnOrGenerateSubid($user);

		// Do not continue with invalid $subid
		if (empty($subid)) {
			$this->debug('onNewsletterSubscribe: No subid found');
			return false;
		}

		foreach($list_ids as $list_id)
		{
			$list_id = intval(trim($list_id));
			$this->subscribeNewsletter($subid, $list_id, $state);
		}

		return true;
	}

	/**
	 * @param $user
	 *
	 * @return bool|int
	 */
	private function returnOrGenerateSubid($user)
	{
		// Get the subscriber class
		$subscriberClass = $this->getSubscriberClass();

		if (empty($subscriberClass)) {
			$this->debug('onNewsletterSubscribe: Acymailing not found');
			return false;
		}

		// See if the user exists in the database
		$subid = $subscriberClass->subid($user->email);

		if (empty($subid))
		{
			$acyUser = (object) null;
			$acyUser->email = $user->email;
			$acyUser->name = $user->name;
			$acyUser->userid = $user->id;

            if ($this->params->get('send_emails', 0) == 0)
            {
                $subscriberClass->sendConf = false;
                $subscriberClass->sendNotif = false;
                $subscriberClass->sendWelcome = false;
            }

		    $subscriberClass->checkVisitor = false;
			$subid = $subscriberClass->save($acyUser);
		}

		return (int) $subid;
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
	 * @param $subid
	 * @param $list_id
	 * @param $state
	 *
	 * @return bool
	 */
	private function subscribeNewsletter($subid, $list_id, $state)
	{
		// Get the subscriber class
		$subscriberClass = $this->getSubscriberClass();

		if (empty($subscriberClass)) {
			$this->debug('onNewsletterSubscribe: Acymailing not found');
			return false;
		}

		// Subscribe the subscriber to the newsletter
		$newSubscription = array();
		$newList = array();
		$newList['status'] = ($state == 0) ? 0 : 1;
		$newSubscription[intval($list_id)] = $newList;

        if ($this->params->get('send_emails', 0) == 0)
        {
            $subscriberClass->sendConf = false;
            $subscriberClass->sendNotif = false;
            $subscriberClass->sendWelcome = false;
        }

		$rt = (bool) $subscriberClass->saveSubscription($subid, $newSubscription);

        return $rt;
	}

	/**
	 * Method to check whether this plugin is enabled or not
	 *
	 * @param null
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->checkComponent('com_acymailing');
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
			'text_file' => 'plg_magebridgenewsletter_acymailing.debug.php'
		), JLog::ALL, array('plg_magebridgenewsletter_acymailing'));

		JLog::add($message, JLog::WARNING, 'plg_magebridgenewsletter_acymailing');

		return true;
	}
}

