<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (http://www.yireo.com/)
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Include Joomla! libraries
jimport( 'joomla.application.component.model' );
jimport( 'joomla.installer.installer' );
jimport('joomla.installer.helper');

// Include MageBridge libraries
require_once JPATH_COMPONENT.'/helpers/update.php';

/*
 * MageBridge Update model
 */
class MagebridgeModelUpdate extends MagebridgeModelAbstract
{
    public function updateAll()
    {
        $packages = MageBridgeUpdateHelper::getPackageList();
        $count = 0;
        foreach($packages as $package) {
            if($this->update($package['name']) == false) {
                return false;
            }
            $count++;
        }
        JError::raiseNotice('MB', JText::sprintf('Updated %d extensions successfully', $count));
        return true;
    }

    public function update($extension_name = null)
    {
        $supportkey = $this->getSupportKey();
        
        if(strlen($supportkey) < 20) {
            JError::raiseNotice('MB', JText::_('Invalid support-key'));
            return false;
        }

        if($extension_name == null) {
            JError::raiseWarning('MB', JText::_('No extension specified'));
            return false;
        }

        $packages = MageBridgeUpdateHelper::getPackageList();
        foreach($packages as $package) {
            if($package['name'] == $extension_name) {
                $extension = $package;
                break;
            }
        }

        if($extension == null) {
            JError::raiseWarning('MB', JText::_('Unknown extension'));
            return false;
        }

        // Construct the update URL
        $extension_uri = $extension['name'];
        $extension_uri .= (MageBridgeHelper::isJoomla15()) ? '_j15' : '_j25';
        $extension_url = $this->getUrl($extension_uri);

        if(ini_get('allow_url_fopen') == 1) {
            $package_file = JInstallerHelper::downloadPackage($extension_url, $extension['name'].'.zip');
        } else {
            $package_file = MageBridgeUpdateHelper::downloadPackage($extension_url, $extension['name'].'.zip');
        }

        if($package_file == false) {
            JError::raiseWarning('MB', JText::sprintf('Failed to download update %s', $extension_url));
            return false;
        }

        $app = JFactory::getApplication();
        $tmp_path = $app->getCfg('tmp_path');
        $package_path = $tmp_path.'/'.$package_file;
        if(!is_file($package_path)) {
            JError::raiseWarning('MB', JText::sprintf('File %s does not exist', $package_path));
            return false;
        }

        if(filesize($package_path) < 1024) {

            $contents = file_get_contents($package_path);
            if(preg_match('/^Restricted/', $contents)) {
                JError::raiseWarning('MB', JText::sprintf('Not allowed to access updates.'));
                JError::raiseWarning('MB', JText::sprintf('Is the support-key "%s" correctly configured?', $this->getSupportKey()));
                JInstallerHelper::cleanupInstall($package_path, $package['extractdir']);
                return false;
            }

            JError::raiseWarning('MB', JText::sprintf('File %s is not a valid ZIP-file', $package_path));
            JInstallerHelper::cleanupInstall($package_path, $package['extractdir']);
            return false;
        }

        $package = JInstallerHelper::unpack($package_path);
        if($package == false) {
            JError::raiseWarning('MB', JText::sprintf('Unable to find update for %s on local filesystem', $extension['name']));
            JInstallerHelper::cleanupInstall($package_path, $package['extractdir']);
            return false;
        }

        // Check for .svn directories
        if(is_dir(JPATH_COMPONENT.'/.svn')) {
            JError::raiseWarning('MB', JText::_('Development environments should be updated through SVN, not the Joomla! installer'));
            JInstallerHelper::cleanupInstall($package_path, $package['extractdir']);
            return true;
        }

        $installer = JInstaller::getInstance();
        if($installer->install($package['dir']) == false) {
            JError::raiseWarning('MB', JText::sprintf('Failed to install %s', $extension['name']));
            return false;
        }

        if(!is_file($package['packagefile'])) {
            $app = JFactory::getApplication();
            $package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
        return true;
    }

    public function getUrl($extension_name)
    {
        $url = 'http://api.yireo.com/';
        $domain = preg_replace( '/\:(.*)/', '', $_SERVER['HTTP_HOST'] );
        $arguments = array(
            'key' => $this->_supportkey,
            'domain' => $domain,
            'resource' => 'download',
            'request' => $extension_name.'.zip',
        );
        foreach($arguments as $name => $value) {
            $arguments[$name] = "$name,$value";
        }
        return $url . implode('/', $arguments);
    }

    public function getSupportKey()
    {
        return $this->_supportkey;
    }

    public function setSupportKey($supportkey)
    {
        $this->_supportkey = $supportkey;
    }
}
