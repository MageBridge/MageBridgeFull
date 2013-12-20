<?php
/**
 * MageBridge Product plugin - Usergroup
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
 * MageBridge Product Plugin to assign a customer to a Joomla! Usergroup when a product is purchased
 *
 * @package MageBridge
 */
class plgMageBridgeProductUsergroup extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'usergroup_id';

    /*
     * Method to execute when the product is bought
     * 
     * @param array $actions
     * @param JUser $user
     * @param int $status
     * @return bool
     */
    public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null)
    {
        // Check for the usergroup ID
        if(!isset($actions['usergroup_id'])) {
            return false;
        }

        // Make sure it is not empty
        $usergroup_id = (int)$actions['usergroup_id'];
        if(!$usergroup_id > 0) {
            return false;
        }

        // See if the user is already listed
        $query = 'SELECT user_id FROM `#__user_usergroup_map` WHERE `user_id`='.(int)$user->id.' AND `group_id`='.(int)$usergroup_id.' LIMIT 1';
        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        // Add the user
        if (empty($result)) {
            $query = 'INSERT INTO `#__user_usergroup_map` SET `user_id`='.(int)$user->id.', `group_id`='.(int)$usergroup_id;
            $this->db->setQuery($query);
            $this->db->query();
        }

        return true;
    }

    /*
     * Method to execute when this connector is reversed
     * 
     * @param array $actions
     * @param JUser $user
     * @return bool
     */
    public function onMageBridgeProductReverse($actions = null, $user = null)
    {
        // Check for the usergroup ID
        if(!isset($actions['usergroup_id'])) {
            return false;
        }

        // Make sure it is not empty
        $usergroup_id = (int)$actions['usergroup_id'];
        if(!$usergroup_id > 0) {
            return false;
        }

        $query = 'DELETE FROM `#__user_usergroup_map` WHERE `user_id`='.(int)$user->id.' AND `group_id`='.(int)$usergroup_id;
        $this->db->setQuery($query);
        $this->db->query();

        return true;
    }
}

