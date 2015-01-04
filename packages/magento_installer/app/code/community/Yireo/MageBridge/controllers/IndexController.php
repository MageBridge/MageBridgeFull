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

/**
 * MageBridge admin controller
 */
class Yireo_MageBridge_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Common method
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/magebridge')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('CMS'), Mage::helper('adminhtml')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('MageBridge'), Mage::helper('adminhtml')->__('MageBridge'))
        ;
        return $this;
    }

    /**
     * Settings page
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('magebridge/settings'))
            ->renderLayout();
    }

    /**
     * License page
     */
    public function licenseAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('magebridge/license'))
            ->renderLayout();
    }

    /**
     * Updates page (which calls for AJAX)
     */
    public function updatesAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('magebridge/updates'))
            ->renderLayout();
    }

    /**
     * Perform an update through AJAX
     */
    public function doupdateAction()
    {
        $update = Mage::getSingleton('magebridge/update');
        if($update->upgradeNeeded() == true) {
            $status = $update->doUpgrade();
        } else {
            $status = 'No upgrade needed';
        }

        $response = new Varien_Object();
        $response->setError(0);
        $response->setMessage($status);
        $this->getResponse()->setBody($response->toJson());
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $data['license_key'] = trim($data['license_key']);
            Mage::getConfig()->saveConfig('magebridge/settings/license_key', $data['license_key']);
            Mage::getConfig()->removeCache();
            Mage::getModel('core/session')->addSuccess('Settings saved');
            
        }

        $url = Mage::getModel('adminhtml/url')->getUrl('magebridge/index/index');
        $this->getResponse()->setRedirect($url);
    }
}
