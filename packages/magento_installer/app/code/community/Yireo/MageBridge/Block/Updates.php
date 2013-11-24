<?php
/**
 * MageBridge
 *
 * @author Yireo
 * @package MageBridge
 * @copyright Copyright 2011
 * @license Yireo EULA (www.yireo.com)
 * @link http://www.yireo.com
 */

class Yireo_MageBridge_Block_Updates extends Mage_Core_Block_Template
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magebridge/updates.phtml');
    }

    /*
     * Helper method to get data from the Magento configuration
     */
    public function getSetting($key = '')
    {
        static $data;
        if(empty($data)) {
            $data = array(
                'license_key' => Mage::helper('magebridge')->getLicenseKey(),
                'enabled' => Mage::helper('magebridge')->enabled(),
            );
        }

        if(isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }

    /*
     * Helper to return the header of this page
     */
    public function getHeader($title = null)
    {
        return 'MageBridge Installer - '.$this->__($title);
    }

    /*
     * Helper to return the menu
     */
    public function getMenu()
    {
        return $this->getLayout()->createBlock('magebridge/menu')->toHtml();
    }

    public function getUpdateUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('magebridge/index/doupdate');
    }

    public function getThisUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('magebridge/index/updates');
    }

    public function getCurrentVersion()
    {
        return Mage::getSingleton('magebridge/update')->getCurrentVersion();
    }

    public function getNewVersion()
    {
        $result = Mage::getSingleton('magebridge/update')->getNewVersion();
        return $result;
    }

    public function upgradeNeeded()
    {
        return Mage::getSingleton('magebridge/update')->upgradeNeeded();
    }
}
