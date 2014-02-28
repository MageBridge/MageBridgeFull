<?php
/**
 * MageBridge Store plugin - Usergroup
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Store Plugin to dynamically load a Magento store-scope based on a Joomla! usergroup
 *
 * @package MageBridge
 */
class plgMageBridgeStoreUsergroup extends MageBridgePluginStore
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Store Plugins
     */
    protected $connector_field = 'usergroup_id';

    /**
     * Event "onMageBridgeValidate"
     * 
     * @access public
     * @param array $actions
     * @return bool
     */
    public function onMageBridgeValidate($actions = null)
    {
        // Make sure this plugin is enabled
        if ($this->isEnabled() == false) {
            return false;
        }

        // Make sure to check upon the $actions array to see if it contains what we need
        if(empty($actions['usergroup_id'])) {
            return false;
        }

        // Check if the current user is of the right group
        $user = JFactory::getUser();
        if (is_array($user->groups) && in_array($actions['usergroup_id'], $user->groups)) {
            return true;
        }

        // Return false by default
        return false;
    }

    /*
     * Method to check whether this plugin is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }
}

