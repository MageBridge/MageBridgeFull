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
 * MageBridge Product Plugin for Communicator
 *
 * @package MageBridge
 */
class plgMageBridgeProductCommunicator extends MageBridgePluginProduct
{
    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_communicator');
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

        // See if the user is already there
        $query = 'SELECT * FROM `#__communicator_subscribers` WHERE `email`='.$this->db->Quote($user->email).' LIMIT 1';
        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        // Add the customer email to the subscribers table
        if (empty($row)) {
            $now = JFactory::getDate()->toMySQL();
            $fields = array(
                'user_id' => (int)$user->id,
                'subscriber_name' => $this->db->Quote($user->name),
                'subscriber_email' => $this->db->Quote($user->email),
                'confirmed' => 1,
                'subscribe_date' => $this->db->Quote($now),
            );

            $query = 'INSERT INTO `#__communicator_subscribers` SET '.MageBridgeHelper::arrayToSql($fields);
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

        $query = 'DELETE FROM `#__communicator_subscribers` WHERE `subscriber_email`='.$this->db->Quote($user->email);
        $this->db->setQuery($query);
        $this->db->query();

        return true;
    }
}

