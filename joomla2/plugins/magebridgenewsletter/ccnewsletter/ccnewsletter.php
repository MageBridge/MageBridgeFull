<?php
/**
 * Joomla! MageBridge - Magento plugin for ccNewsletter
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
 * MageBridge Newsletter Plugin for ccNewsletter
 */
class plgMageBridgeNewsletterCcnewsletter extends MageBridgePluginMagento
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

        $db = JFactory::getDBO();

        // See if the user is already there
        $query = 'SELECT * FROM `#__ccnewsletter_subscribers` WHERE `email`='.$db->Quote($user->email);
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        // Subscribe action
        if($state == 1) {

            // Add the customer email to the subscribers list
            if (empty($rows)) {
                $query = 'INSERT INTO `#__ccnewsletter_subscribers` SET `name`='.$db->Quote($user->name).', `email`='.$db->Quote($user->email).', `sdate`=NOW()';
                $db->setQuery($query);
                $db->query();
            }

        // Unsubscribe action
        } else {

            // Delete the customer email from the list
            if(!empty($rows)) {
                $query = 'DELETE FROM `#__ccnewsletter_subscribers` WHERE `email`='.$db->Quote($user->email);
                $db->setQuery($query);
                $db->query();
            }
        }

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
        return $this->checkComponent('com_ccnewsletter');
    }
}

