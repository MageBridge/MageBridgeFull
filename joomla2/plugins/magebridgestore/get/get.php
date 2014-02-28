<?php
/**
 * MageBridge Store plugin - Get
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
 * MageBridge Store Plugin to dynamically load a Magento store-scope based on a Joomla! get
 *
 * @package MageBridge
 */
class plgMageBridgeStoreGet extends MageBridgePluginStore
{
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

