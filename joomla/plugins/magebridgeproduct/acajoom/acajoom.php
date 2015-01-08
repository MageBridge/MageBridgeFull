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
 * MageBridge Product Plugin for Acajoom
 *
 * @package MageBridge
 */
class plgMageBridgeProductAcajoom extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'acajoom_list';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_acajoom');
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
        if(!isset($actions['acajoom_list'])) {
            return false;
        }

        // Make sure it is not empty
        $acajoom_list = (int)$actions['acajoom_list'];
        if(!$acajoom_list > 0) {
            return false;
        }

        // See if the user is already there
        $query = 'SELECT `id` FROM `#__acajoom_subscribers` WHERE `email`='.$this->db->Quote($user->email).' LIMIT 1';
        $this->db->setQuery($query);
        $subscriber_id = $this->db->loadResult();

        // Add the customer email to the subscribers list
        if (empty($subscriber_id)) {

            $values = array(
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'confirmed' => 1,
            );

            $query = 'INSERT INTO `#__acajoom_subscribers` SET '.MageBridgeHelper::arrayToSql($values).', `sdate`=NOW()';
            $this->db->setQuery($query);
            $this->db->query();
            $subscriber_id = $this->db->insertid();
        }

        if ($subscriber_id > 0) {

            // See if the user is already there
            $query = 'SELECT * FROM `#__acajoom_queue` WHERE `subscriber_id`='.(int)$subscriber_id.' AND `list_id`='.(int)$acajoom_list.' LIMIT 1';
            $this->db->setQuery($query);
            $row = $this->db->loadObject();

            if (empty($row)) {

                $values = array(
                    'subscriber_id' => (int)$subscriber_id,
                    'list_id' => (int)$acajoom_list,
                    'type' => 1,
                );

                $query = 'INSERT INTO `#__acajoom_queue` SET '.MageBridgeHelper::arrayToSql($values);
                $this->db->setQuery($query);
                $this->db->query();
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
        if(!isset($actions['acajoom_list'])) {
            return false;
        }

        // Make sure it is not empty
        $acajoom_list = (int)$actions['acajoom_list'];
        if(!$acajoom_list > 0) {
            return false;
        }

        // See if the user is there
        $query = 'SELECT id FROM `#__acajoom_subscribers` WHERE `email`='.$this->db->Quote($user->email).' LIMIT 1';
        $this->db->setQuery($query);
        $subscriber_id = $this->db->loadResult();

        if ($subscriber_id > 0) {
            $query = 'DELETE FROM `#__acajoom_queue` WHERE `subscriber_id`='.(int)$subscriber_id.' AND `list_id`='.(int)$acajoom_list;
            $this->db->setQuery($query);
            $this->db->query();
        }

        return true;
    }
}

