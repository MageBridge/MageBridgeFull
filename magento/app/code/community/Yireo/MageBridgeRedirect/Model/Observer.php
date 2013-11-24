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

/*
 * Observer class
 */
class Yireo_MageBridgeRedirect_Model_Observer
{
    /**
     * Event "controller_action_predispatch"
     */
    public function controllerActionPredispatch($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        $currentUrl = Mage::app()->getRequest()->getOriginalPathInfo();

        // Check if this is a bridge-request
        if(Mage::helper('magebridge')->isBridge() == true) {
            return $this;
        }

        // Check whether redirection is enabled
        if(Mage::helper('magebridgeredirect')->enabled() == false) {
            return $this;
        }

        // Check if this is an API-request
        if(Mage::helper('magebridgeredirect')->isApi() == true) {
            return $this;
        }
        
        // Fetch the MageBridge Root
        $magebridgeRootUrl = Mage::helper('magebridgeredirect')->getMageBridgeRoot();
        if(empty($magebridgeRootUrl)) {
            return $this;
        }

        // Parse request URI
        $currentUrl = str_replace('/index.php/', '/', $currentUrl);
        if(preg_match('/\/$/', $magebridgeRootUrl)) $currentUrl = preg_replace('/^\//', '', $currentUrl);

        // Construct the new URL
        $newUrl = $magebridgeRootUrl.$currentUrl;

        // Redirect
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$newUrl);exit;
    }
}
