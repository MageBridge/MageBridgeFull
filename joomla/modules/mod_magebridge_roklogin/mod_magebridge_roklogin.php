<?php
/**
 * Joomla! module MageBridge Login for RocketTheme templates
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */
		
// No direct access
defined('_JEXEC') or die('Restricted access');

// Decide whether to show a login-link or logout-link
$user =& JFactory::getUser();
$type = (!$user->get('guest')) ? 'logout_link' : 'login_link';

switch ($params->get($type)) {
	case 'current':
		$return_url = JFactory::getURI()->toString();
		break;

	case 'home':
		$default = JSite::getMenu()->getDefault();
		$return_url = JFactory::getURI($default->link)->toString();
		break;

	case 'mbhome':
		$return_url = MageBridgeUrlHelper::route('/');
		break;

	case 'mbaccount':
		$return_url = MageBridgeUrlHelper::route('customer/account');
		break;
}
$return_url = base64_encode($return_url);

// Set the greeting name
$user =& JFactory::getUser();
$name = ($params->get('name')) ? $user->name : $user->username;	

// Construct the account URLs
$account_url = ($params->get('account_link') == 2) ? MageBridgeUrlHelper::route('customer/account') : JRoute::_('index.php?option=com_user&view=user&layout=form');
$wishlist_url = MageBridgeUrlHelper::route('wishlist');
$checkout_url = MageBridgeUrlHelper::route('checkout');
$cart_url = MageBridgeUrlHelper::route('checkout/cart');

// Determine whether to show links
$show_account_links = false;
if ($params->get('account_link') || $params->get('wishlist_link') || $params->get('checkout_link') || $params->get('cart_link')) {
	$show_account_links = true;
}

// Include the template-helper
$magebridge = new MageBridgeTemplateHelper();

require(JModuleHelper::getLayoutPath('mod_magebridge_roklogin'));
