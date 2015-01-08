<?php
/**
 * Joomla! module MageBridge: Products block
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Helper-class for the module
 */
class modMagebridgeRokScrollerHelper extends MageBridgeModuleHelper
{
    /*
     * Method to get the API-arguments based upon the module parameters
     * 
     * @access public
     * @param JParameter $params
     * @return array
     */
    static public function getArguments($params = null)
    {
        static $arguments = array();
        $id = md5(var_export($params, true));
        if (!isset($arguments[$id])) {
            $arguments[$id] = array(
                'ordering' => $params->get('ordering', ''),
                'count' => $params->get('count', 5),
                'category_id' => $params->get('category_id', 0),
            );
        }

        return $arguments[$id];
    }

    /*
     * Method to be called as soon as MageBridge is loaded
     *
     * @access public
     * @param JParameter $params
     * @return array
     */
    public function register($params = null)
    {
        // Initialize the register 
        $register = array(
            array( 'api', 'magebridge_product.list', modMagebridgeRokScrollerHelper::getArguments($params)),
        );
        return $register;
    }

    /*
     * Fetch the content from the bridge
     * 
     * @access public
     * @param JParameter $params
     * @return string
     */
    public function build($params = null)
    {
        $products = parent::build('getAPI', 'magebridge_product.list', modMagebridgeRokScrollerHelper::getArguments($params));

        if (!empty($products)) {
            foreach ($products as $index => $product) {

                // Use the URL-key to build a URL
                $product['url'] = MageBridgeUrlHelper::route($product['url_key']);

                // Remove the current product from the list
                if (JURI::getInstance()->toString( array('path')) == $product['url']) {
                    unset($products[$index]);
                    continue;
                }

                $products[$index] = $product;
            }
        }

        return $products;
    }
}
