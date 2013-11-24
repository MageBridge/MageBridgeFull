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

class Yireo_MageBridge_Block_License extends Mage_Core_Block_Template
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('magebridge/license.phtml');
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
}
