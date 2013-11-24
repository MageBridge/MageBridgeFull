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

class Yireo_MageBridge_Block_Settings extends Mage_Core_Block_Template
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magebridge/settings.phtml');
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
     * Helper method to get list of all the forwarded events and their current status
     */
    public function getEvents()
    {
        $events = Mage::getModel('magebridge/listener')->getEvents();
        $event_list = array();
        foreach($events as $event) {
            $event_list[] = array(
                'name' => $event,
                'value' => (int)Mage::getModel('magebridge/listener')->isEnabled($event),
            );
        }
        return $event_list;
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

    public function getSaveUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('magebridge/index/save');
    }
}
