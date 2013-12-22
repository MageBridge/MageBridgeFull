<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product Plugin for Alphauserpoints
 *
 * @package MageBridge
 */
class plgMageBridgeProductAlphauserpoints extends MageBridgePluginProduct
{
    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_alphauserpoints');
    }

    /*
     * Event "onMageBridgeProductPurchase"
     * 
     * @access public
     * @param array $actions
     * @param object $user Joomla! user object
     * @param tinyint $status Status of the current order
     * @param string $sku Magento SKU
     */
    public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null, $sku = null)
    {
        // Make sure this event is allowed
        if($this->isEnabled() == false) {
            return false;
        }

        // Check for the form-values
        if(!isset($actions['aup_rule']) || !isset($actions['aup_points'])) {
            return false;
        }

        // Make sure it is not empty
        $aup_points = (int)$actions['aup_points'];
        $aup_rule = (int)$actions['aup_rule'];
        if(empty($aup_points) || empty($aup_rule)) {
            return false;
        }

        // Apply the points
        $aup = JPATH_SITE.'/components/com_alphauserpoints/helper.php';
        if (file_exists($aup)) {
            require_once($aup);

            $aup_id = AlphaUserPointsHelper::getAnyUserReferreID($user->id);
            if ($aup_id) AlphaUserPointsHelper::newpoints($aup_rule, $aup_id, $sku, null, $aup_points);
        }

        return true;
    }

    /*
     * Event "onMageBridgeProductReverse"
     * 
     * @param array $actions
     * @param JUser $user
     * @param string $sku Magento SKU
     * @return bool
     */
    public function onMageBridgeProductReverse($actions = null, $user = null)
    {
        // Make sure this event is allowed
        if($this->isEnabled() == false) {
            return false;
        }

        // @todo: Extract the points?

        return true;
    }
}

