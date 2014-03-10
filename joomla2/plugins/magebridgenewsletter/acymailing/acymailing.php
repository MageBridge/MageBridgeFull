<?php
/**
 * Joomla! MageBridge - Magento plugin for Acymailing
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Newsletter Plugin for Acymailing
 */
class plgMageBridgeNewsletterAcymailing extends MageBridgePluginMagento
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

        $list_id = $this->getParams()->get('newsletter');
        if (empty($list_id)) {
            return true;
        }
        
        if (!include_once(rtrim(JPATH_ADMINISTRATOR,'/').'/components/com_acymailing/helpers/helper.php')){
            return false;
        }

        // See if the user exists in the database
        $acyUser = null;
        $acyUser->email = $user->email;
        $acyUser->name = $user->name;
        $acyUser->userid = $user->id;

        $subscriberClass = acymailing::get('class.subscriber');
        $subscriberClass->checkVisitor = false;
        $subid = $subscriberClass->save($acyUser);
        if (empty($subid)) return false;

        $newSubscription = array();
        $newList = array();
        $newList['status'] = ($state == 0) ? 0 : 1;
        $newSubscription[intval($list_id)] = $newList;

        $subscriberClass->saveSubscription($subid, $newSubscription);

        return true;
    }

    /*
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
     * Load the parameters
     * 
     * @access private
     * @param null
     * @return JParameter
     */
    private function getParams()
    {
        return $this->params;
    }
}

