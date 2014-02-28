<?php
/**
 * MageBridge Store plugin - Days
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
 * MageBridge Store Plugin to dynamically load a Magento store-scope based on a Joomla! days
 *
 * @package MageBridge
 */
class plgMageBridgeStoreDays extends MageBridgePluginStore
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

        // Make sure to check upon the $actions array to see if it contains what we need
        if(empty($actions['days_from'])) {
            return false;
        }

        // Check if the condition applies
        $from = explode('-', $actions['days_from']);
        $to = explode('-', $actions['days_to']);
        $from_stamp = mktime(0, 0, 0, $from[1], $from[2], $from[0]);
        $to_stamp = mktime(0, 0, 0, $to[1], $to[2], $to[0]);
        if (time() > $from_stamp && time() < $to_stamp) {
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

