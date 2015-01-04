<?php
/*
 * MageBridge installer script for Magento v0.3
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// User settings
define('mbSupportKey', '');
define('mbSupportDomain', '');
define('mbApiUsername', '');
define('mbApiPassword', '');

/* 
 * System values
 */
$email = 'magebridge@example.com';
$firstname = 'magebridge';
$lastname = 'magebridge';
$root = dirname(__FILE__).'/';

/*
 * There's no need to modify anything else
 */

// Preliminary checks
if(!file_exists($root.'index.php') || !file_exists($root.'app/Mage.php')) {
    feedback('ERROR: Not a Magento instance');
    feedback('Exit');
    exit;
}

// Set umask(0022) manually
umask(0022);

// Add umask(0022) to the main index.php file
$index = file_get_contents($root.'index.php');
if(strstr($index, 'umask(0);')) {
    feedback('Fixing umask() in Magento index.php');
    $index = str_replace('umask(0);', 'umask(0022);', $index);
    file_put_contents($root.'index.php', $index);
}

// Skip this step if the supportkey is empty
if(mbSupportKey == '') {
    feedback('ERROR: No supportkey defined');

} elseif(!class_exists('ZipArchive')) {
    feedback('ERROR: No ZIP-support found');

} else {

    // Download the Magento Patch file
    feedback('Downloading MageBridge package for Magento');
    $url = 'http://api.yireo.com/key,'.mbSupportKey.'/domain,'.mbSupportDomain.'/resource,download/request,MageBridge_Magento_patch.zip';
    downloadFile($url, $root.'patch.zip');

    // Extract the ZIP-file
    feedback('Unzipping MageBridge package for Magento');
    doUnzipFile($root.'patch.zip', $root);

    // Cleanup
    unlink($root.'patch.zip');
}

// Download the htaccess-files
feedback('Downloading Yireo-optimized Magento htaccess-file');
if(file_exists($root.'.htaccess')) copy($root.'.htaccess', $root.'.htaccess.orig');
downloadFile('http://www.yireo.com/documents/htaccess_magento.txt', $root.'.htaccess');
downloadFile('http://www.yireo.com/documents/htaccess_magento.txt', $root.'.htaccess.yireo');

// Start the Magento application
feedback('Starting Magento application');
require_once 'app/Mage.php';
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

// Create an API-user
feedback('Creating API user');
$user = Mage::getModel('api/user')->load(1);
$user->setData('firstname', $firstname);
$user->setData('lastname', $lastname);
$user->setData('email', $email);
$user->setData('username', mbApiUsername);
$user->setData('api_key', mbApiPassword);
$user->save();
$user_id = $user->getUserId();

// Create an API-role
feedback('Creating API role');
$role = Mage::getModel('api/role')->load(1);
$role->setData('tree_level', 1);
$role->setData('role_type', 'G');
$role->setData('role_name', 'MageBridge API');
$role->save();
$parent_id = $role->getRoleId();

// Create a relation between the new role and new user
feedback('Configuring API user for API role');
$role = Mage::getModel('api/role')->load(2);
$role->setData('parent_id', (int)$parent_id);
$role->setData('tree_level', 1);
$role->setData('role_type', 'U');
$role->setData('user_id', (int)$user_id);
$role->setData('role_name', mbApiUsername);
$role->save();

// Modify system settings
feedback('Modifying Magento configuration');
Mage::getConfig()->saveConfig('catalog/seo/product_url_suffix', '');
Mage::getConfig()->saveConfig('catalog/seo/category_url_suffix', '');
Mage::getConfig()->saveConfig('web/url/redirect_to_base', 0);
if(mbSupportKey != '') {
    feedback('Setting MageBridge support-key');
    Mage::getConfig()->saveConfig('magebridge/settings/license_key', mbSupportKey);
}

// Reindex everything
feedback('Rebuilding Magento indices');
$collection = Mage::getResourceModel('index/process_collection');
foreach($collection as $process) {
    $process->reindexEverything();
}

// Clean the cache
feedback('Cleaning Magento cache');
Mage::app()->cleanCache();

// Function to unzip a file
function doUnzipFile($file, $root)
{
    $zip = new ZipArchive();
    if($zip->open($file) === true) {
        $zip->extractTo($root);
        $zip->close();
    }
}

// Function to download a specific file
function downloadFile($url, $file)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    if(!empty($data)) {
        file_put_contents($file, $data);
    } else {
        print_r(curl_error($ch));
    }
    curl_close($ch);
}

function feedback($message)
{
    $isCLI = ( php_sapi_name() == 'cli' );
    if($isCLI) {
        echo $message."\n";
    } else {
        echo $message."<br/>";
    }
    @ob_end_flush(); 
    @ob_flush(); 
    @flush(); 
    @ob_start(); 
}

feedback('Done');
// End
