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

class Yireo_MageBridge_Block_Menu extends Mage_Core_Block_Template
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magebridge/menu.phtml');
    }

    /*
     * Helper method to get data from the Magento configuration
     */
    public function getMenuItems()
    {

        $items = array(
            array(
                'action' => 'index',
                'title' => 'Settings',
            ),
            array(
                'action' => 'updates',
                'title' => 'Installation',
            ),
            array(
                'action' => 'license',
                'title' => 'License Agreement',
            ),
        );

        $url = Mage::getModel('adminhtml/url');
        $current_action = $this->getRequest()->getActionName();

        foreach($items as $index => $item) {

            if($item['action'] == $current_action) {
                $item['class'] = 'active';
            } else {
                $item['class'] = 'inactive';
            }
        
            $item['url'] = $url->getUrl('magebridge/index/'.$item['action']);

            $items[$index] = $item;
        }

        return $items;
    }
}
