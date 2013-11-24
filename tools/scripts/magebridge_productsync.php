<?php
/**
 * Joomla! 1.5 script for re-syncing MageBridge Product Connectors
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2010
 * @license GNU Public License
 * @link http://www.yireo.com
 */

/*
 * Preliminary actions to get the Joomla! Framework up and running
 */

// Neccessary definitions
define( '_JEXEC', 1 );
define( 'JPATH_BASE', dirname(dirname(__FILE__)));
define( 'DS', DIRECTORY_SEPARATOR );

// Change the path to the JPATH_BASE
if(!is_file(JPATH_BASE.DS.'includes'.DS.'framework.php')) {
    die('Incorrect Joomla! base-path');
}
chdir(JPATH_BASE);

// Include the framework
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

// Start the application
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();
$db = JFactory::getDBO();

/*
 * Fetch all current Magento orders
 */

// Include the MageBridge loader and get common classes
include_once JPATH_SITE.DS.'components'.DS.'com_magebridge'.DS.'helpers'.DS.'loader.php';
$register = MageBridgeModelRegister::getInstance();
$bridge = MageBridgeModelBridge::getInstance();

// Fetch a list of products from Magento
$arguments = array();
$register->clean();
$id = $register->add('api', 'magebridge_order.getOrderItems', $arguments);
$bridge->build();
$orderItems = $register->getDataById($id);

// Create a listing of all customers per specific product-order
$productOrders = array();
foreach($orderItems as $orderItem) {

    $productSku = $orderItem['sku'];
    $customerEmail = $orderItem['customer_email'];

    if(!isset($productOrders[$productSku])) $productOrders[$productSku] = array();
    $productOrders[$productSku][] = $customerEmail;
}

/*
 * Perform the sync per user per ordered product
 */

// Fetch the configured products
foreach($productOrders as $sku => $emails) {
    foreach($emails as $email) {
        $user = MageBridgeModelUser::loadByEmail($email);
        if(!empty($user)) {
            MageBridgeConnectorProduct::getInstance()->onPurchase($sku, $user);
        }
    }
}

echo "================================================================\n";
echo "\n";
