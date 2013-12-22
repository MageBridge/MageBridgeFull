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
 * MageBridge Product Plugin for Akeeba Subscriptions
 *
 * @package MageBridge
 */
class plgMageBridgeProductAkeebasubs extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'akeebasubs_level';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_akeebasubs');
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

        // Check for the usergroup ID
        if(!isset($actions['akeebasubs_level'])) {
            return false;
        }

        // Make sure it is not empty
        $level_id = (int)$actions['akeebasubs_level'];
        if(!$level_id > 0) {
            return false;
        }

        // See if the user is already there
        $query = 'SELECT * FROM `#__akeebasubs_subscriptions` WHERE `user_id`='.(int)$user->id.' AND `akeebasubs_level_id`='.(int)$level_id.' LIMIT 1';
        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        if (empty($row)) {
            $values = array(
                'user_id' => (int)$user->id,
                'akeebasubs_level_id' => (int)$level_id,
                'enabled' => 1,
                //'publish_up' => '',
                //'publish_down' => '',
                'processor' => 'none',
                'processor_key' => 'magento',
                'state' => 'X',
            );

            $query = 'INSERT INTO `#__akeebasubs_subscriptions` SET '.MageBridgeHelper::arrayToSql($values);
            $this->db->setQuery($query);
            $this->db->query();
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
        if(!isset($actions['akeebasubs_level'])) {
            return false;
        }

        // Make sure it is not empty
        $level_id = (int)$actions['akeebasubs_level'];
        if(!$level_id > 0) {
            return false;
        }

        $query = 'DELETE FROM `#__akeebasubs_subscriptions` WHERE `user_id`='.(int)$user->id.' AND `akeebasubs_level_id`='.(int)$level_id;
        $this->db->setQuery($query);
        $this->db->query();

        return true;
    }
}

