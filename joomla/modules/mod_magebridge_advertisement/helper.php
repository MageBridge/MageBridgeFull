<?php
/**
 * Joomla! module MageBridge: Advertisement block
 *
 * @author	Yireo (info@yireo.com)
 * @package   MageBridge
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link	  https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Helper-class for the module
 */

class ModMageBridgeAdvertisementHelper extends MageBridgeModuleHelper
{
	/**
	 * Method to get the API-arguments based upon the module parameters
	 *
	 * @access public
	 * @param JRegistry $params
	 * @return array
	 */
	static public function getArguments($params = null)
	{
		static $arguments = array();
		$id = md5(var_export($params, true));

		if (!isset($arguments[$id]))
		{
			$arguments[$id] = array('product_id' => $params->get('product_id', 0), 'custom_image_size' => $params->get('thumb_size', null),);
		}

		return $arguments[$id];
	}

	/**
	 * Method to be called as soon as MageBridge is loaded
	 *
	 * @access public
	 * @param JRegistry $params
	 * @return array
	 */
	static public function register($params = null)
	{
		$arguments = array(array('api', 'magebridge_product.info', self::getArguments($params)));
		return $arguments;
	}

	/**
	 * Fetch the content from the bridge
	 *
	 * @access public
	 * @param JRegistry $params
	 * @return string
	 */
	static public function build($params = null)
	{
		$product = parent::getCall('getAPI', 'magebridge_product.info', self::getArguments($params));

		if (!empty($product))
		{
			// Use the URL-key to build a URL
			if (!empty($product['url_store']))
			{
				$product['url'] = MageBridgeUrlHelper::route($product['url_store']);
			}
			elseif (!empty($product['url_path']))
			{
				$product['url'] = MageBridgeUrlHelper::route($product['url_path']);
			}
			elseif (empty($product['url']))
			{
				$product['url'] = MageBridgeUrlHelper::route($product['url_key']);
			}
			$product['addtocart_url'] = MageBridgeUrlHelper::route('checkout/cart/add/product/' . $product['product_id'] . '/');

			// Create labels
			$product['addtocart_label'] = JText::sprintf($params->get('addtocart', 'MOD_MAGEBRIDGE_PRODUCTS_ADDTOCART'), $product['label']);
			$product['addtocart_text'] = JText::sprintf($params->get('addtocart', 'MOD_MAGEBRIDGE_PRODUCTS_ADDTOCART'), $product['name']);
			$product['readmore_label'] = JText::sprintf($params->get('readmore', 'MOD_MAGEBRIDGE_PRODUCTS_READMORE'), $product['label']);
			$product['readmore_text'] = JText::sprintf($params->get('readmore', 'MOD_MAGEBRIDGE_PRODUCTS_READMORE'), $product['name']);
		}

		return $product;
	}
}
