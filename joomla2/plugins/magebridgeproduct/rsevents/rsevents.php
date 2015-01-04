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
 * MageBridge Product Plugin for RsEvents
 *
 * @package MageBridge
 */
class plgMageBridgeProductRsevents extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'rsevents_event';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_rsevents');
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

        // Check for the rsevents_event
        if(!isset($actions['rsevents_event'])) {
            return false;
        }

        // Make sure it is not empty
        $rsevents_event = (int)$actions['rsevents_event'];
        if(!$rsevents_event > 0) {
            return false;
        }

        $db = JFactory::getDBO();

        // See if the user is already subscribed
        $query = 'SELECT * FROM `#__rsevents_subscriptions` WHERE `IdUser`='.(int)$user->id.' AND `IdEvent`='.(int)$rsevents_event;
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        // Add the customer email to the subscribers list
        if (empty($rows)) {

            $values = array(
                'IdEvent' => (int)$rsevents_event,
                'IdUser' => (int)$user->id,
                'FirstName' => $user->name,
                'LastName' => '-',
                'Email' => $user->email,
                'SubscriptionState' => 0,
                'SubscriptionTotalFee' => 0,
                'SubscriptionDate' => time(),
                'ValidationDate' => 0,
                'ConfirmationDate' => 0,
                'SubscriptionFormId' => 1,
            );

            $query_values = array();
            foreach ($values as $name => $value) {
                $query_values[] = "`$name`=".$db->Quote($value);
            }

            $query = 'INSERT INTO `#__rsevents_subscriptions` SET '.implode(',', $query_values);
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
        if(!isset($actions['rsevents_event'])) {
            return false;
        }

        // Make sure it is not empty
        $rsevents_event = (int)$actions['rsevents_event'];
        if(!$rsevents_event > 0) {
            return false;
        }

        $db = JFactory::getDBO();

        // See if the user is already there
        $query = 'DELETE FROM `#__rsevents_subscriptions` WHERE `Email`='.$db->Quote($user->email).' AND `IdEvent`='.(int)$rsevents_event;
        $db->setQuery($query);
        $db->query();

        return true;
    }
}

