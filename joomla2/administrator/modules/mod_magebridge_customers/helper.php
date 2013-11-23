<?php
/**
 * Joomla! module Magento Bridge: Customers
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Helper-class for the module
 */
class modMageBridgeCustomersHelper extends MageBridgeModuleHelper
{
    /*
     * Method to be called once the MageBridge is loaded
     */
    static public function register($params = null)
    {
        return array(
            array('api', 'magebridge_customer.list'),
        );
    }

    /*
     * Fetch the content from the bridge
     */
    static public function build()
    {
        return parent::getCall('getAPI', 'magebridge_customer.list');
    }
}
