<?php
/**
 * MageBridge
 *
 * @author Yireo
 * @package MageBridge
 * @copyright Copyright 2015
 * @license Yireo EULA (www.yireo.com)
 * @link http://www.yireo.com
 */

class Yireo_MageBridge_Model_Update extends Mage_Core_Model_Abstract
{
    private $_current_version = null;
    private $_new_version = null;
    private $_remote_url = 'http://api.yireo.com/';

    public function getApiLink($arguments = array())
    {
        $arguments = array_merge($this->getApiArguments(), $arguments);

        foreach($arguments as $name => $value) {
            if($name == 'request') {
                $arguments[$name] = "$value";
            } else {
                $arguments[$name] = "$name,$value";
            }
        }

        return $this->_remote_url . implode('/', $arguments);
    }

    public function getApiArguments()
    {
        return array(
            'license' => $this->getLicenseKey(),
            'domain' => $_SERVER['HTTP_HOST'],
        );
    }

    public function upgradeNeeded()
    {
        if(version_compare($this->getNewVersion(), $this->getCurrentVersion(), '>')) {
            return true;
        }
        return true;
    }

    public function doUpgrade()
    {
        if(!class_exists('ZipArchive')) {
            return 'ERROR: PHP-extension zip (class ZipArchive) is not installed';
        }

        $tmpdir = Mage::getConfig()->getOptions()->getTmpDir();
        if(!is_writable($tmpdir)) {
            return 'ERROR: '.$tmpdir.' is not writable';
        }

        $download_url = $this->getApiLink(array('resource' => 'download', 'request' => 'MageBridge_Magento_patch.zip'));
        $data = $this->_getRemote($download_url);
        if(empty($data)) {
            return 'ERROR: Downloaded update-file is empty';
        }

        $tmpfile = $tmpdir.DS.'MageBridge_Magento_patch.zip';
        file_put_contents($tmpfile, $data);
        ini_set('error_reporting', 0);

        $zip = new ZipArchive();
        if($zip->open($tmpfile) === true) {
            $root = Mage::getBaseDir();
            $zip->extractTo($root);
            $zip->close();
        } else {
            return 'ERROR: Failed to extract the upgrade-archive';
        }

        // Reset the configuration cache
        Mage::getConfig()->removeCache();
        return null;
    }

    public function getCurrentVersion()
    {
        if(empty($this->_current_version)) {
            $config = Mage::app()->getConfig()->getModuleConfig('Yireo_MageBridge');
            $this->_current_version = (string)$config->version;
        }
        return $this->_current_version;
    }

    public function getNewVersion()
    {
        if($this->checkLicenseKey() == false) {
            return 'ERROR: Invalid license key';
        }

        if(empty($this->_new_version)) {
            $arguments = array('resource' => 'versions', 'request' => 'downloads/magebridge');
            $url = $this->getApiLink($arguments);
            $this->_data = trim($this->_getRemote($url));
            if(preg_match('/^Restricted access/', $this->_data)) {
                return 'ERROR: Restricted access. Is your licensing correct?';
            } elseif(empty($this->_data)) { 
                return 'ERROR: Empty reply. Is CURL enabled?';
            }

            try {
                $doc = new SimpleXMLElement($this->_data);
            } catch(Exception $e) {
                return 'ERROR: Update check failed. Is your licensing correct?';
            }
            $this->_new_version = (string)$doc->magento;
        }
        return $this->_new_version;
    }

    public function checkLicenseKey()
    {
        $license = Mage::getStoreConfig('magebridge/settings/license_key');
        if($license == 'TRIAL' || preg_match('/^([a-zA-Z0-9]{32})$/', $license)) {
            return true;
        }
        return false;
    }

    public function getLicenseKey()
    {
        return Mage::getStoreConfig('magebridge/settings/license_key');
    }

    private function _getRemote($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        return $data;
    }
}
