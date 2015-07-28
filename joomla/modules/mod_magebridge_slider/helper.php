<?php
/**
 * Joomla! module MageBridge: Slider block
 *
 * @author	Yireo (info@yireo.com)
 * @package   MageBridge
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link	  http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Helper-class for the module
 */
class ModMageBridgeSliderHelper extends MageBridgeModuleHelper
{
	/**
	 * Method to get the API-arguments based upon the module parameters
	 *
	 * @access public
	 *
	 * @param JRegistry $params
	 *
	 * @return array
	 */
	static public function getArguments($params = null)
	{
		static $arguments = array();
		$id = md5(var_export($params, true));

		if (!isset($arguments[$id]))
		{
			$arguments[$id] = array(
				'ordering' => $params->get('ordering', ''),
				'count' => $params->get('count', 5),
				'category_id' => $params->get('category_id', 0),
				'custom_image_size' => $params->get('image_size', null),);
		}

		return $arguments[$id];
	}

	/**
	 * Method to be called as soon as MageBridge is loaded
	 *
	 * @access public
	 *
	 * @param JRegistry $params
	 *
	 * @return array
	 */
	static public function register($params = null)
	{
		return array(
			array('api', 'magebridge_product.list', modMageBridgeSliderHelper::getArguments($params)),);
	}

	/**
	 * Fetch the content from the bridge
	 *
	 * @access public
	 *
	 * @param JRegistry $params
	 *
	 * @return string
	 */
	static public function build($params = null)
	{
		$products = parent::getCall('getAPI', 'magebridge_product.list', modMageBridgeSliderHelper::getArguments($params));

		if (!empty($products))
		{
			foreach ($products as $index => $product)
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
				$product['addtocart_label'] = JText::sprintf($params->get('addtocart', 'MOD_MAGEBRIDGE_SLIDER_ADDTOCART'), $product['label']);
				$product['addtocart_text'] = JText::sprintf($params->get('addtocart', 'MOD_MAGEBRIDGE_SLIDER_ADDTOCART'), $product['name']);
				$product['readmore_label'] = JText::sprintf($params->get('readmore', 'MOD_MAGEBRIDGE_SLIDER_READMORE'), $product['label']);
				$product['readmore_text'] = JText::sprintf($params->get('readmore', 'MOD_MAGEBRIDGE_SLIDER_READMORE'), $product['name']);

				// Remove the current product from the list
				if (JURI::getInstance()->toString(array('path')) == $product['url'])
				{
					unset($products[$index]);
					continue;
				}

				$products[$index] = $product;
			}
		}

		return $products;
	}

	static public function addStylesheet($stylesheet)
	{
		$template = JFactory::getApplication()->getTemplate();

		if (file_exists(JPATH_SITE . '/templates/' . $template . '/css/mod_magebridge_slider/' . $stylesheet))
		{
			$path = '/templates/' . $template . '/css/mod_magebridge_slider/' . $stylesheet;
		}
		else
		{
			$path = '/media/mod_magebridge_slider/' . $stylesheet;
		}

		$document = JFactory::getDocument();
		$document->addStylesheet($path);
	}

	static public function addScript($script)
	{
		$template = JFactory::getApplication()->getTemplate();

		if (file_exists(JPATH_SITE . '/templates/' . $template . '/js/mod_magebridge_slider/' . $script))
		{
			$path = '/templates/' . $template . '/css/mod_magebridge_slider/' . $script;
		}
		else
		{
			$path = '/media/mod_magebridge_slider/' . $script;
		}

		$script = '/media/mod_magebridge_slider/' . $script;
		$document = JFactory::getDocument();
		$document->addScript($script);
	}
}
