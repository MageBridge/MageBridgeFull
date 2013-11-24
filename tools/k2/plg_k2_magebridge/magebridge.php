<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die ('Restricted access');

/**
 * MageBridge K2 Plugin
 */

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'k2plugin.php');

// Initiate class to hold plugin events
class plgK2MageBridge extends K2Plugin 
{
	/* 
     * Plugin name
     */
	public $pluginName = 'magebridge';

	/* 
     * Plugin name
     */
	public $pluginNameHumanReadable = 'MageBridge K2 Plugin';

	public function onK2PrepareContent( &$item, &$params, $limitstart) 
    {
		// Get the K2 plugin params 
		//$plugin = &JPluginHelper::getPlugin('k2', $this->pluginName);
		//$pluginParams = new JParameter($plugin->params);

        // Fetch the main bridge-variables
        $bridge = MageBridgeModelBridge::getInstance();
        $register = MageBridgeModelRegister::getInstance();

		// Get the output of the K2 plugin fields
		$plugins = new K2Parameter($item->plugins, '', $this->pluginName);
		
        // Read the Magento product ID
		$product_id = $plugins->get('product_id');
        if(empty($product_id)) {
            return false;
        }

        // Fetch additional product-data from the bridge
        $register_id = $register->add('api', 'catalog_product.info', $product_id);
        $bridge->build();
        $data = $register->getDataById($register_id);

        // Extra the product-data
        $product_name = (isset($data['name'])) ? $data['name'] : null;
        $product_sku = (isset($data['sku'])) ? $data['sku'] : null;
        $product_type = (isset($data['type'])) ? $data['type'] : null;
        $product_short_description = (isset($data['short_description'])) ? $data['short_description'] : null;
        $product_description = (isset($data['description'])) ? $data['description'] : null;
        // @todo: Image?

        // Add the description
        if(strstr($item->text, '{description}')) {
            $item->text = str_replace('{description}', $product_description, $item->text);
        } else {
            $item->text .= $product_description; // @todo: Create parameter for this
        }

        // Add the cart-URL
        $cartUrl = JRoute::_('index.php?option=com_magebridge&view=root&request=checkout/cart/add/product/'.$product_id);
		$cart = '<button class="button" onclick="location.href=\''.$cartUrl.'\';"><span>'.JText::_('Add to cart').'</span></button>';
        if(strstr($item->text, '{addtocart}')) {
            $item->text = str_replace('{addtocart}', $cart, $item->text);
        } else {
            $item->text .= $cart; // @todo: Create parameter for this
        }
	}

	public function onK2AfterDisplay( &$item, &$params, $limitstart) 
    {
		return '';
	}

	public function onK2BeforeDisplay( &$item, &$params, $limitstart) 
    {
		return '';
	}

	public function onK2AfterDisplayTitle( &$item, &$params, $limitstart)
    {
		return '';
	}

	public function onK2BeforeDisplayContent( &$item, &$params, $limitstart) 
    {
		return '';
	}

	// Event to display (in the frontend) 
	public function onK2AfterDisplayContent( &$item, &$params, $limitstart) 
    {
		return '';
	}
}
