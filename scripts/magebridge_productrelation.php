<?php
/**
 * Joomla! 2.5 script for testing a MageBridge Product Connect
 *
 * Usage:
 * Configure the $product_sku and $user_id below. There is no need for the user to have actually purchased
 * the SKU, or even no need for the SKU to exist in Magento.
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

$product_sku = 'test-membership';
$user_id = 43;

/*
 * Preliminary actions to get the Joomla! Framework up and running
 */

// Neccessary definitions
define( '_JEXEC', 1 );
define( 'JPATH_BASE', dirname(dirname(__FILE__)));
define( 'DS', DIRECTORY_SEPARATOR );

// Change the path to the JPATH_BASE
if(!is_file(JPATH_BASE.'/includes/framework.php')) {
    die('Incorrect Joomla! base-path');
}
chdir(JPATH_BASE);

// Include the framework
require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );

// Start the application
$app = JFactory::getApplication('site');
$app->initialise();
$db = JFactory::getDBO();

// Include the autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/*
 * Run a product-connector
 */
$user = JFactory::getUser();
$user->load($user_id);
if(!empty($user) && $user->id > 0) {
    echo "Running product-relation for SKU '".$product_sku."' on user ".$user->email."\n";
    MageBridgeConnectorProduct::getInstance()->runOnPurchase($product_sku, 1, $user, 'complete');
} else {
    echo "Failed to load user with ID '".$user_id."'\n";
}

echo "\n";
