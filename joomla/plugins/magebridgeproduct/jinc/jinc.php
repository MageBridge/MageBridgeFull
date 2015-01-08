<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product Plugin for JINC
 *
 * @package MageBridge
 */
class plgMageBridgeProductJinc extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'jinc_group';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_jinc');
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

        // Check for the jinc_group
        if(!isset($actions['jinc_group'])) {
            return false;
        }

        // Make sure it is not empty
        $jinc_group = (int)$actions['jinc_group'];
        if(!$jinc_group > 0) {
            return false;
        }

        $db = JFactory::getDBO();

        // See if the user is already there
        $query = 'SELECT mem_id FROM `#__jinc_membership` WHERE `mem_user_id`='.$user->id.' AND `mem_grp_id`='.(int)$jinc_group;
        $db->setQuery($query);
        $membership_id = $db->loadResult();

        // Add the customer email to the membership list
        if (empty($membership_id)) {
            $query = 'INSERT INTO `#__jinc_membership` SET `mem_user_id`='.$user->id.' AND `mem_grp_id`='.(int)$jinc_group;
            $db->setQuery($query);
            $db->query();
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
    public function onMageBridgeProductReverse($actions = null, $user = null, $sku = null)
    {
        // Make sure this event is allowed
        if($this->isEnabled() == false) {
            return false;
        }

        // Check for the usergroup ID
        if(!isset($actions['jinc_group'])) {
            return false;
        }

        // Make sure it is not empty
        $jinc_group = (int)$actions['jinc_group'];
        if(!$jinc_group > 0) {
            return false;
        }

        $db = JFactory::getDBO();
        $query = 'DELETE FROM `#__jinc_membership` WHERE `mem_user_id`='.(int)$user->id.' AND `mem_grp_id`='.(int)$jinc_group;
        $db->setQuery($query);
        $db->query();

        return true;
    }
}

