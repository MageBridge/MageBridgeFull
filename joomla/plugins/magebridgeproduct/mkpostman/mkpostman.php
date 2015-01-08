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
 * MageBridge Product Plugin for MkPostman
 *
 * @package MageBridge
 */
class plgMageBridgeProductMkpostman extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'mkpostman_list';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_mkpostman');
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

        // Check for the mkpostman_list
        if(!isset($actions['mkpostman_list'])) {
            return false;
        }

        // Make sure it is not empty
        $mkpostman_list = (int)$actions['mkpostman_list'];
        if(!$mkpostman_list > 0) {
            return false;
        }

        $db = JFactory::getDBO();

        // See if the user is already there
        $query = 'SELECT * FROM `#__mkpostman_subscribers` WHERE `email`='.$db->Quote($user->email).' LIMIT 1';
        $db->setQuery($query);
        $row = $db->loadObject();

        // Add the customer email to the subscribers table
        if (empty($row)) {
            $now = JFactory::getDate()->toMySQL();
            $fields = array(
                'user_id' => (int)$user->id,
                'name' => $db->Quote($user->name),
                'email' => $db->Quote($user->email),
                'status' => 1,
                'registration_date' => $db->Quote($now),
                'confirmation_date' => $db->Quote($now),
            );

            $query = 'INSERT INTO `#__mkpostman_subscribers` SET '.MageBridgeHelper::arrayToSql($fields);
            $db->setQuery($query);
            $db->query();

            // See if the user is already there
            $query = 'SELECT * FROM `#__mkpostman_subscribers` WHERE `email`='.$db->Quote($user->email).' LIMIT 1';
            $db->setQuery($query);
            $row = $db->loadObject();
        }

        // Continue to add the subscriber to the actual list
        if (!empty($row->id)) {
            $subscriber_id = $row->id;
            $query = 'SELECT * FROM `#__mkpostman_subscribers_lists` WHERE `subscriber_id`='.(int)$subscriber_id.' AND `list_id`='.(int)$mkpostman_list;
            $db->setQuery($query);
            $row = $db->loadObject();

            if (empty($row)) {
                $query = 'INSERT INTO `#__mkpostman_subscribers_lists` SET `subscriber_id`='.(int)$subscriber_id.', `list_id`='.(int)$mkpostman_list;
                $db->setQuery($query);
                $db->query();
            }
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
        if(!isset($actions['mkpostman_list'])) {
            return false;
        }

        // Make sure it is not empty
        $mkpostman_list = (int)$actions['mkpostman_list'];
        if(!$mkpostman_list > 0) {
            return false;
        }

        $db = JFactory::getDBO();

        // See if the user is already there
        $query = 'SELECT * FROM `#__mkpostman_subscribers` WHERE `email`='.$db->Quote($user->email).' LIMIT 1';
        $db->setQuery($query);
        $row = $db->loadObject();

        // If the user is there, we can use its ID
        if (!empty($row->id)) {
            $query = 'DELETE FROM `#__mkpostman_subscribers_lists` WHERE `subscriber_id`='.(int)$row->id.' AND `list_id`='.(int)$mkpostman_list;
            $db->setQuery($query);
            $db->query();
        }

        return true;
    }
}

