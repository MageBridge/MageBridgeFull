<?php
/**
 * Joomla! module MageBridge: Remote Block
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
class modMageBridgeRemoteHelper
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
        // Initialize the register 
        $register = array();
        if ($params->get('load_css', 1) == 1 || $params->get('load_js', 1) == 1) {
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
        $blockName = modMageBridgeRemoteHelper::blockName($params);

        // Include the MageBridge bridge
        $bridge = MageBridgeModelBridge::getInstance();

        // Load CSS if needed
        if ($params->get('load_css', 1) == 1) {
            $bridge->setHeaders('css');
        }

        // Load JavaScript if needed
        if ($params->get('load_js', 1) == 1) {
            $bridge->setHeaders('js');
        }

        // Load the Ajax script
        $url = $bridge->getMagentoUrl().'/magebridge/block/view/name/'.$blockName;
        $script = MageBridgeAjaxHelper::getScript($blockName, 'magebridge-'.$blockName, $url);
        $document = JFactory::getDocument();
        $document->addCustomTag( '<script type="text/javascript">'.$script.'</script>');
    }

    /*
     * Helper-method to fetch the block name from the parameters
     * 
     * @access public
     * @param JParameter $params
     * @return string
     */
    public static function blockName($params)
    {
        $block = trim($params->get('custom'));
        if (empty($block)) {
            $block = $params->get('block', $block);
        }
        return $block;
    }
}
