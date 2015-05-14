<?php
/**
 * Script to install MageBridge into Joomla! or Magento
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// User settings
define('mbSupportKey', 'zQ0ODJkYzdjNzU0YTMwYWJhOTcwNjVkM');
define('mbSupportDomain', 'livakurser.dk');
define('mbApiHost', 'www.forlagetliva.dk');
define('mbApiBasedir', '');
define('mbApiUsername', 'magebridge9103203');
define('mbApiPassword', 'Azp3zDfdf8Smm3cc00');
define('mbTemplatePatch', 0);
    
// Detect the Joomla! directory
define('DOCUMENT_ROOT', dirname(__FILE__).'/');

/*
 * Parent class
 */
class MageBridgeInstaller
{
    /*
     * Constructor
     */
    public function __construct()
    {
        // Check for constants
        if(mbSupportKey == '') {
            $this->feedback('ERROR: No supportkey defined');
            exit;
        }

        // PHP settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR | E_PARSE);
    }

    /*
     * Method to unzip a file
     */
    public function doUnzipFile($file, $folder)
    {
        $zipSupport = false;
    
        // Set umask(0022) manually
        umask(0022);

        // ZipArchive
        if(class_exists('ZipArchive')) {
            $zipSupport = true;
            $zip = new ZipArchive();
            if($zip->open($file) === true) {
                $zip->extractTo($folder);
                $zip->close();
            }
        }

        // Try commands instead
        if($zipSupport == false) {
            $cmd = "unzip $file -d $folder";
            if(function_exists('exec')) {
                $zipSupport = true;
                exec($cmd);
            } elseif(function_exists('system')) {
                $zipSupport = true;
                system($cmd);
            } elseif(function_exists('passthru')) {
                $zipSupport = true;
                passthru($cmd);
            }
        }

        if($zipSupport == false) {
            $this->feedback('ERROR: No ZIP-support found');
        }
        return;
    }

    /*
     * Method to download a specific file
     */
    public function downloadFile($url, $file)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if(!empty($data)) {
            $rt = file_put_contents($file, $data);
            if($rt == false) {
                $this->feedback("ERROR: Failed to write file: $file");
            } else {
                $this->feedback("File: $file");
            }
        } else {
            $this->feedback("ERROR: CURL-error: ".curl_error($ch));
        }
        curl_close($ch);
    }

    /*
     * Method to give feedback
     */
    public function feedback($message)
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
}

/*
 * Joomla! class
 */
class MageBridgeInstallerJoomla extends MageBridgeInstaller
{
    /*
     * Main method to run this installer
     */
    public function __construct()
    {
        // Necessary definitions
        define('_JEXEC', 1);
        define('JPATH_BASE', DOCUMENT_ROOT);
        define('DS', DIRECTORY_SEPARATOR );

        parent::__construct();
    }

    /*
     * Main method to run this installer
     */
    public function run()
    {
        $this->getHtaccess();
        $this->initJoomla();
        $this->modGlobalConfig();
        $this->installMageBridge();
        $this->configMageBridge();
        $this->updateMageBridge();
    }

    /*
     * Get the htaccess-files
     */
    protected function getHtaccess()
    {
        if(!file_exists(DOCUMENT_ROOT.'.htaccess.yireo')) {
            $this->feedback('Downloading Yireo-optimized Joomla! htaccess-file');
            if(file_exists(DOCUMENT_ROOT.'.htaccess')) {
                copy(DOCUMENT_ROOT.'.htaccess', DOCUMENT_ROOT.'.htaccess.orig');
            }

            $this->downloadFile('http://www.yireo.com/documents/htaccess_joomla.txt', DOCUMENT_ROOT.'.htaccess');
            $this->downloadFile('http://www.yireo.com/documents/htaccess_joomla.txt', DOCUMENT_ROOT.'.htaccess.yireo');

            if(!file_exists(DOCUMENT_ROOT.'.htaccess.yireo')) {
                die('No .htaccess.yireo after download');
            }
        }
    }

    /*
     * Initialize the Joomla! application
     */
    protected function initJoomla()
    {
        // Change the path to the JPATH_BASE
        if(!is_file(JPATH_BASE.'/includes/framework.php')) {
            die('Incorrect Joomla! base-path');
        }
        chdir(JPATH_BASE);

        // Include the framework
        require_once ( JPATH_BASE .'/includes/defines.php' );
        require_once ( JPATH_BASE .'/includes/framework.php' );
        jimport('joomla.environment.request');
        jimport('joomla.database.database');

        // Start the application
        $mainframe =& JFactory::getApplication('site');
        $mainframe->initialise();
        if(method_exists('JFactory', 'getDbo')) {
            $db = JFactory::getDbo();
        } else {
            $db = JFactory::getDBO();
        }

        // Add Joomla! variables to this object
        $this->db = $db;
        $this->app = $mainframe;
    }

    /*
     * Modify the Global Configuration
     */
    protected function modGlobalConfig()
    {
        $this->feedback('Replacing the Global Configuration');

        $configuration = JPATH_BASE.'/configuration.php';
        $contents = file_get_contents($configuration);
        if(empty($contents)) {
            $this->feedback('ERROR: Empty file');
        }

        $contents = $this->configReplace($contents, 'dbtype', 'mysql', 'mysqli');
        $contents = $this->configReplace($contents, 'sef', '0', '1');
        $contents = $this->configReplace($contents, 'sef_rewrite', '0', '1');
        $contents = $this->configReplace($contents, 'lifetime', '15', '180');
        $contents = $this->configReplace($contents, 'gzip', '0', '1');

        try {
            if(function_exists('chmod')) chmod($configuration, 0644);
            file_put_contents($configuration, $contents);
        } catch(Exception $e) {
            $this->feedback('ERROR: Failed to write Global Configuration');
        }
    }

    /* 
     * Install the MageBridge component
     */
    protected function installMageBridge()
    {
        if(!is_dir(JPATH_BASE.'/components/com_magebridge')) {
            $this->feedback('Downloading MageBridge component');

            $file = 'com_magebridge_j25.zip';
            $url = 'http://api.yireo.com/key,'.mbSupportKey.'/domain,'.mbSupportDomain.'/resource,download/request,'.$file;
            $this->downloadFile($url, JPATH_BASE.'/tmp/com_magebridge.zip');

            jimport('joomla.application.component.model');
            jimport('joomla.installer.installer');
            jimport('joomla.installer.helper');

            $this->feedback('Unpacking MageBridge component');
            $package = JInstallerHelper::unpack(JPATH_BASE.'/tmp/com_magebridge.zip');

            $this->feedback('Installing MageBridge component');
            $installer = JInstaller::getInstance();
            $installer->install($package['dir']);
            @JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
        }

        if(!is_dir(JPATH_BASE.'/components/com_magebridge')) {
            die('No components/com_magebridge after installing');
        }
    }

    /* 
     * Configure the MageBridge component
     */
    protected function configMageBridge()
    {
        // Configuring MageBridge values
        $this->feedback('Configuring MageBridge component');
        $this->mbConfigure('supportkey', mbSupportKey);
        $this->mbConfigure('host', mbApiHost);
        $this->mbConfigure('basedir', mbApiBasedir);
        $this->mbConfigure('api_user', mbApiUsername);
        $this->mbConfigure('api_key', mbApiPassword);
        $this->mbConfigure('website', 1);
        if(mbTemplatePatch == 1) {
            $this->mbConfigure('disable_css_all', 1);
            $this->mbConfigure('disable_default_css', 0);
        }
    }

    /* 
     * Update all MageBridge extensions
     */
    protected function updateMageBridge()
    {
        // Skip this step if the MageBridge core-plugins seem already installed
        if(!is_file(JPATH_BASE.'/plugins/system/magebridge.php')) {

            $this->feedback('Upgrading all MageBridge extensions');

            // Include the needed MageBridge classes
            require_once ( JPATH_SITE.'/components/com_magebridge/helpers/loader.php' );
            require_once ( JPATH_ADMINISTRATOR.'/components/com_magebridge/models/update.php' );
            require_once ( JPATH_ADMINISTRATOR.'/components/com_magebridge/helpers/install.php' );

            // Set the hostname to enable license checks
            $_SERVER['HTTP_HOST'] = mbSupportDomain;

            // Initialize the updater
            $update = new MagebridgeModelUpdate();
            $packages = MageBridgeUpdateHelper::getPackageList();
            $files = array();
            
            // Add current packages to the update list
            foreach($packages as $package) {
                if($package['core'] == 1) {
                    $files[] = $package['name'];
                }
            }
            unset($files[0]);

            // Perform the actual update
            $update->updateAll($files);
        }
    }

    /*
     * Replace a configuration entry
     */
    protected function configReplace($contents, $name, $oldvalue, $newvalue) 
    {
        return preg_replace("/$name([\ ]+)\=\ \'$oldvalue\'/", "$name = '$newvalue'", $contents);
    }

    /*
     * Check whether the current Joomla! version is 2.5
     */
    protected function isJoomla25()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        if(!version_compare($version->RELEASE, '2.5', 'eq')) {
            return false;
        }

        return true;
    }

    /*
     * Function to add values to the MageBridge Configuration
     */
    protected function mbConfigure($name, $value) 
    {
        $this->db->setQuery('SELECT * FROM #__magebridge_config WHERE `name`='.$this->db->Quote($name));
        $rows = $this->db->loadObjectList();
        if(empty($rows)) {
            $query = 'INSERT INTO #__magebridge_config SET `name`='.$this->db->Quote($name).', `value`='.$this->db->Quote($value);
        } else {
            $query = 'UPDATE #__magebridge_config SET `value`='.$this->db->Quote($value).' WHERE `name`='.$this->db->Quote($name);
        }
        $this->db->setQuery($query);
        $this->db->query();
    }
}

/*
 * Magento class
 */
class MageBridgeInstallerMagento extends MageBridgeInstaller
{
    /*
     * Main method to run this installer
     */
    public function __construct()
    {
        // Necessary definitions

        parent::__construct();
    }

    /*
     * Main method to run this installer
     */
    public function run()
    {
        $this->getHtaccess();
        $this->fixMagentoIndexFile();
        $this->initMagento();
        $this->apiUserMagento();
        $this->systemConfigMagento();
        $this->installMageBridge();
        $this->cleanMagento();
    }

    /*
     * Get the htaccess-files
     */
    protected function getHtaccess()
    {
        if(!file_exists(DOCUMENT_ROOT.'.htaccess.yireo')) {
            $this->feedback('Downloading Yireo-optimized Magento htaccess-file');
            if(file_exists(DOCUMENT_ROOT.'.htaccess')) {
                copy(DOCUMENT_ROOT.'.htaccess', DOCUMENT_ROOT.'.htaccess.orig');
            }
            $this->downloadFile('http://www.yireo.com/documents/htaccess_magento.txt', DOCUMENT_ROOT.'.htaccess');
            $this->downloadFile('http://www.yireo.com/documents/htaccess_magento.txt', DOCUMENT_ROOT.'.htaccess.yireo');
        }
    }

    /*
     * Add umask(0022) to the main index.php file
     */
    protected function fixMagentoIndexFile()
    {
        $index = file_get_contents(DOCUMENT_ROOT.'index.php');
        if(strstr($index, 'umask(0);')) {
            $this->feedback('Fixing umask() in Magento index.php');
            $index = str_replace('umask(0);', 'umask(0022);', $index);
            file_put_contents(DOCUMENT_ROOT.'index.php', $index);
        }
    }

    /*
     * Initialize the Magento application
     */
    protected function initMagento()
    {
        // Start the Magento application
        $this->feedback('Starting Magento application');
        require_once 'app/Mage.php';
        Mage::app();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /*
     * Install MageBridge
     */
    protected function installMageBridge()
    {
        // Download the Magento Patch file
        $this->feedback('Downloading MageBridge package for Magento');
        $url = 'http://api.yireo.com/key,'.mbSupportKey.'/domain,'.mbSupportDomain.'/resource,download/request,MageBridge_Magento_patch.zip';
        $this->downloadFile($url, DOCUMENT_ROOT.'patch.zip');
    
        // Extract the ZIP-file
        $this->feedback('Unzipping MageBridge package for Magento');
        $this->doUnzipFile(DOCUMENT_ROOT.'patch.zip', DOCUMENT_ROOT);

        // Cleanup
        unlink(DOCUMENT_ROOT.'patch.zip');
    }

    /*
     * Configure the API-user and API-role
     */
    protected function apiUserMagento()
    {
        // Dummy settings
        $email = 'magebridge@example.com';
        $firstname = 'magebridge';
        $lastname = 'magebridge';

        // Create an API-user
        $this->feedback('Creating API user');
        $user = Mage::getModel('api/user')->load(1);
        $user->setData('firstname', $firstname);
        $user->setData('lastname', $lastname);
        $user->setData('email', $email);
        $user->setData('username', mbApiUsername);
        $user->setData('api_key', mbApiPassword);
        $user->save();
        $user_id = $user->getUserId();

        // Create an API-role
        $this->feedback('Creating API role');
        $role = Mage::getModel('api/role')->load(1);
        $role->setData('tree_level', 1);
        $role->setData('role_type', 'G');
        $role->setData('role_name', 'MageBridge API');
        $role->save();
        $parent_id = $role->getRoleId();

        // Create a relation between the new role and new user
        $this->feedback('Configuring API user for API role');
        $role = Mage::getModel('api/role')->load(2);
        $role->setData('parent_id', (int)$parent_id);
        $role->setData('tree_level', 1);
        $role->setData('role_type', 'U');
        $role->setData('user_id', (int)$user_id);
        $role->setData('role_name', mbApiUsername);
        $role->save();
    }

    /*
     * Configure the Magento application
     */
    protected function systemConfigMagento()
    {
        // Modify system settings
        $this->feedback('Modifying Magento configuration');
        Mage::getConfig()->saveConfig('catalog/seo/product_url_suffix', '');
        Mage::getConfig()->saveConfig('catalog/seo/category_url_suffix', '');
        Mage::getConfig()->saveConfig('web/url/redirect_to_base', 0);
        if(mbSupportKey != '') {
            $this->feedback('Setting MageBridge support-key');
            Mage::getConfig()->saveConfig('magebridge/settings/license_key', mbSupportKey);
        }
    }

    /*
     * Clean the Magento application
     */
    protected function cleanMagento()
    {
        // Reindex everything
        $this->feedback('Rebuilding Magento indices');
        $collection = Mage::getResourceModel('index/process_collection');
        foreach($collection as $process) {
            $process->reindexEverything();
        }

        // Clean the cache
        $this->feedback('Cleaning Magento cache');
        Mage::app()->cleanCache();
    }
}

// Create the right installer instance
if(file_exists(DOCUMENT_ROOT.'configuration.php') && is_dir(DOCUMENT_ROOT.'administrator')) {
    $installer = new MageBridgeInstallerJoomla();
} elseif(file_exists(DOCUMENT_ROOT.'app/etc/local.xml')) {
    $installer = new MageBridgeInstallerMagento();
} else {
    die('Unknown application');
}

// Run the installer
$installer->run();
// Done
