<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2011
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to make sure this file is run from the command-line

// System definitions
define( '_JEXEC', 1 );
define( 'JPATH_BASE', dirname(__FILE__));
define( 'DS', DIRECTORY_SEPARATOR );

// Include the Joomla! framework
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );

// Initialize Joomla!
$mainframe =& JFactory::getApplication('administrator');
$mainframe->initialise();

// Include the needed MageBridge classes
require_once ( JPATH_SITE.DS.'components'.DS.'com_magebridge'.DS.'helpers'.DS.'loader.php' );
require_once ( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge'.DS.'models'.DS.'update.php' );

// Set the hostname to enable license checks
$_SERVER['HTTP_HOST'] = MagebridgeModelConfig::load('host');

// Initialize the updater
$update = new MagebridgeModelUpdate();
$packages = MageBridgeUpdateHelper::getPackageList();
$files = array();

// Add current packages to the update list
foreach($packages as $package) {
    if(MageBridgeUpdateHelper::getCurrentVersion($package) == true) {
        $files[] = $package['name'];
    }
}

// Perform the actual update
@$update->updateAll($files);

// End
