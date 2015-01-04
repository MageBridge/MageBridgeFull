<?php
/**
 * Script to install MageBridge into Joomla!
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
define('mbApiHost', '');
define('mbApiBasedir', '');
define('mbApiUsername', '');
define('mbApiPassword', '');
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
        // PHP settings
        ini_set('display_errors', 0);
        error_reporting(E_ERROR | E_PARSE);
    }

    /*
     * Method to unzip a file
     */
    public function doUnzipFile($file, $folder)
    {
        $zip = new ZipArchive();
        if($zip->open($file) === true) {
            $zip->extractTo($folder);
            $zip->close();
        }
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
            file_put_contents($file, $data);
        } else {
            print_r(curl_error($ch));
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
        $this->feedback('Downloading Yireo-optimized Joomla! htaccess-file');
        if(!file_exists(DOCUMENT_ROOT.'.htaccess.yireo')) {
            if(file_exists(DOCUMENT_ROOT.'.htaccess')) {
                copy(DOCUMENT_ROOT.'.htaccess', DOCUMENT_ROOT.'.htaccess.orig');
            }
            $this->downloadFile('http://www.yireo.com/documents/htaccess_joomla.txt', DOCUMENT_ROOT.'.htaccess');
            $this->downloadFile('http://www.yireo.com/documents/htaccess_joomla.txt', DOCUMENT_ROOT.'.htaccess.yireo');
        }
    }

    /*
     * Initialize the Joomla! application
     */
    protected function initJoomla()
    {
        // Change the path to the JPATH_BASE
        if(!is_file(JPATH_BASE.DS.'includes'.DS.'framework.php')) {
            die('Incorrect Joomla! base-path');
        }
        chdir(JPATH_BASE);

        // Include the framework
        require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
        require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
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

        $configuration = JPATH_BASE.DS.'configuration.php';
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
        if(!is_dir(JPATH_BASE.DS.'components'.DS.'com_magebridge')) {
            $this->feedback('Downloading MageBridge component');

            $file = ($this->isJoomla15()) ? 'com_magebridge_j15.zip' : 'com_magebridge_j25.zip';
            $url = 'http://api.yireo.com/key,'.mbSupportKey.'/domain,'.mbSupportDomain.'/resource,download/request,'.$file;
            $this->downloadFile($url, JPATH_BASE.DS.'tmp'.DS.'com_magebridge.zip');

            jimport('joomla.application.component.model');
            jimport('joomla.installer.installer');
            jimport('joomla.installer.helper');

            $this->feedback('Unpacking MageBridge component');
            $package = JInstallerHelper::unpack(JPATH_BASE.DS.'tmp'.DS.'com_magebridge.zip');

            $this->feedback('Installing MageBridge component');
            $installer = JInstaller::getInstance();
            $installer->install($package['dir']);
            @JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
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
        if(!is_file(JPATH_BASE.DS.'plugins'.DS.'system'.DS.'magebridge.php')) {

            $this->feedback('Upgrading all MageBridge extensions');

            // Include the needed MageBridge classes
            require_once ( JPATH_SITE.DS.'components'.DS.'com_magebridge'.DS.'helpers'.DS.'loader.php' );
            require_once ( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge'.DS.'models'.DS.'update.php' );
            require_once ( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge'.DS.'helpers'.DS.'install.php' );

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
    protected function configReplace($contents, $name, $oldvalue, $newvalue) {
        return preg_replace("/$name([\ ]+)\=\ \'$oldvalue\'/", "$name = '$newvalue'", $contents);
    }

    /*
     * Check whether the current Joomla! version is 1.5
     */
    protected function isJoomla15()
    {
        JLoader::import( 'joomla.version' );
        $version = new JVersion();
        if(!version_compare($version->RELEASE, '1.5', 'eq')) {
            return false;
        }

        return true;
    }

    /*
     * Function to add values to the MageBridge Configuration
     */
    protected function mbConfigure($name, $value) {
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

// Create the right installer instance
if(file_exists(DOCUMENT_ROOT.'configuration.php') && is_dir(DOCUMENT_ROOT.'administrator')) {
    $installer = new MageBridgeInstallerJoomla();
} else {
    $installer = new MageBridgeInstallerMagento();
}

// Run the installer
$installer->run();
// Done
