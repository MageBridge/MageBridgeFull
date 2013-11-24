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

class Yireo_MageBridge_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function enabled()
    {
        return (bool)Mage::getStoreConfig('magebridge/settings/active');
    }

    public function getLicenseKey()
    {
        return Mage::getStoreConfig('magebridge/settings/license_key');
    }
}
