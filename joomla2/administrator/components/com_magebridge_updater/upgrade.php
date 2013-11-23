<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to make sure this file is run from the command-line
if (isset($_SERVER['HTTP_HOST'])) {
    die('Illegal access');
} 
$_SERVER['HTTP_HOST'] = null;

// System definitions
define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));
defined('_JEXEC') or die();

// Include the Joomla! framework
require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );

// Initialize Joomla!
$mainframe = JFactory::getApplication('administrator');
$mainframe->initialise();

// Include the needed MageBridge classes
require_once ( JPATH_SITE.'/components/com_magebridge/helpers/loader.php' );
require_once ( JPATH_ADMINISTRATOR.'/components/com_magebridge/models/update.php' );

// Set the hostname to enable supportkey checks
$_SERVER['HTTP_HOST'] = MagebridgeModelConfig::load('host');

// Initialize the updater
$update = new MagebridgeModelUpdate();
$packages = MageBridgeUpdateHelper::getPackageList();
$files = array();

// Add current packages to the update list
foreach ($packages as $package) {
    if (MageBridgeUpdateHelper::getCurrentVersion($package) == true) {
        $files[] = $package['name'];
    }
}

// Perform the actual update
@$update->updateAll($files);

// End
