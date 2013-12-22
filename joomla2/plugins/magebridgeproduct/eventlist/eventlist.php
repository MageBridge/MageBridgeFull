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
 * MageBridge Product Plugin for Eventlist
 *
 * @package MageBridge
 */
class plgMageBridgeProductEventlist extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'eventlist_event_id';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_eventlist');
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
        if(!isset($actions['eventlist_event_id'])) {
            return false;
        }

        // Make sure it is not empty
        $eventlist_event_id = (int)$actions['eventlist_event_id'];
        if(!$eventlist_event_id > 0) {
            return false;
        }

        // See if the user is already there
        $query = 'SELECT id FROM `#__eventlist_register` WHERE `event`='.(int)$eventlist_event_id.' AND `uid`='.(int)$user->id.' LIMIT 1';
        $db->setQuery($query);
        $row = $db->loadObject();

        // Add the customer email to the subscribers list
        if (empty($row)) {

            $values = array(
                'event' => (int)$eventlist_event_id,
                'uid' => (int)$user->id,
                'uip' => '127.0.0.1',
            );

            $query = 'INSERT INTO `#__eventlist_register` SET '.MageBridgeHelper::arrayToSql($values).', `uregdate`=NOW()';
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
        if(!isset($actions['eventlist_event_id'])) {
            return false;
        }

        // Make sure it is not empty
        $eventlist_event_id = (int)$actions['eventlist_event_id'];
        if(!$eventlist_event_id > 0) {
            return false;
        }

        // Remove the user from the registration
        $query = 'DELETE FROM `#__eventlist_register` WHERE `event`='.(int)$eventlist_event_id.' AND `uid`='.(int)$user->id.' LIMIT 1';
        $db->setQuery($query);
        $db->query();

        return true;
    }
}

