<?php
/**
 * MageBridge Store plugin - Domain
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
 * MageBridge Store Plugin to dynamically load a Magento store-scope based on a Joomla! domain
 *
 * @package MageBridge
 */
class plgMageBridgeStoreDomain extends MageBridgePluginStore
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Store Plugins
     */
    protected $connector_field = 'domain_name';

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
        if(empty($actions['domain_name'])) {
            return false;
        }

        // Check for the domain-name
        $domain_name = $actions['domain_name'];
        if (!empty($domain_name) && $domain_name == $_SERVER['HTTP_HOST']) {
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

