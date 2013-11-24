<?php
/**
 * MageBridgeRedirect
 *
 * @author Yireo
 * @package MageBridge
 * @copyright Copyright 2013
 * @license Open Source License
 * @link http://www.yireo.com
 */

class Yireo_MageBridgeRedirect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Helper-method to return whether this module is enabled
     *
     * @access public
     * @param null
     * @return boolean
     */
    public function enabled()
    {
        $value = Mage::getStoreConfig('magebridge/redirect/enabled');
        return (bool)$value;
    }

    /*
     * Helper-method to check whether the current request is an API-request
     *
     * @access public
     * @param null
     * @return string
     */
    public function isApi()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if($this->getModuleName() == 'api') return true;
        return false;
    }

    /*
     * Helper-method to return the MageBridgeRoot URL
     *
     * @access public
     * @param null
     * @return string
     */
    public function getMageBridgeRoot()
    {
        $value = trim(Mage::getStoreConfig('magebridge/redirect/magebridge_root'));
        if(!empty($value) && preg_match('/\/$/', $value) == false) $value .= '/';
        return $value;
    }

}
