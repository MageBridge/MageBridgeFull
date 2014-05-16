<?php
/**
 * Joomla! module MageBridge: Customer Menu
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Helper-class for the module
 */
class modMageBridgeCustomermenuHelper
{
    /*
     * Method to be called as soon as MageBridge is loaded
     * 
     * @access public
     * @param JParameter $params
     * @return array
     */
    static public function register($params = null)
    {
        // Don't register anything, if the user is not logged in
        $user = JFactory::getUser();
        if((bool)$user->guest == true) {
            return false;
        }

        // Get the block name
        $blockName = 'customer_account_navigation';

        // Initialize the register 
        $register = array();
        $register[] = array('block', $blockName);

        if ($params->get('load_css', 1) == 1) {
            $register[] = array('headers');
        }
        return $register;
    }

    /*
     * Build output for the AJAX-layout
     * 
     * @access public
     * @param JParameter $params
     * @return string
     */
    static public function ajaxbuild($params = null)
    {
        // Get the block name
        $blockName = 'customer_account_navigation';

        // Include the MageBridge bridge
        $bridge = MageBridgeModelBridge::getInstance();

        // Load CSS if needed
        if ($params->get('load_css', 1) == 1) {
            $bridge->setHeaders('css');
        }

        // Load the Ajax script
        $script = MageBridgeAjaxHelper::getScript($blockName, 'magebridge-'.$blockName);
        $document = JFactory::getDocument();
        $document->addCustomTag( '<script type="text/javascript">'.$script.'</script>');
    }

    /*
     * Fetch the content from the bridge
     * 
     * @access public
     * @param JParameter $params
     * @return string
     */
    static public function build($params = null)
    {
        // Get the block name
        $blockName = 'customer_account_navigation';

        // Include the MageBridge bridge
        $bridge = MageBridgeModelBridge::getInstance();

        // Load CSS if needed
        if ($params->get('load_css', 1) == 1) {
            $bridge->setHeaders('css');
        }

        // Disable for all pages except customer pages
        if (MageBridgeTemplateHelper::isCustomerPage() == false) {
            return null;
        }

        // Get the block
        MageBridgeModelDebug::getInstance()->notice('Bridge called for block "'.$blockName.'"');
        $block = $bridge->getBlock($blockName);

        // Return the output
        return $block;
    }
}
