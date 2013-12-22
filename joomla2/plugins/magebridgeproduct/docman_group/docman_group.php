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
 * MageBridge Product Plugin for DOCman Groups
 *
 * @package MageBridge
 */
class plgMageBridgeProductDocman_group extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'docman_group';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_docman');
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
        if(!isset($actions['docman_group'])) {
            return false;
        }

        // Make sure it is not empty
        $docman_group = (int)$actions['docman_group'];
        if(!$docman_group > 0) {
            return false;
        }

        // Get the DOCman group
        $query = "SELECT * FROM `#__docman_groups` WHERE `groups_id`=".(int)$docman_group;
        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        if (!empty($row)) {

            // Construct the new members-list
            if (empty($row->groups_members)) {
                $members = $user->id;
            } else {
                $user_ids = explode(',', $row->groups_members);
                $user_ids[] = $user->id;
                $members = implode(',', $user_ids);
            }

            // Update the new members-list within the database
            $members = $this->db->Quote($members);
            $query = "UPDATE `#__docman_groups` SET `groups_members`=".$members." WHERE `groups_id`=".(int)$docman_group;
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
        if(!isset($actions['docman_group'])) {
            return false;
        }

        // Make sure it is not empty
        $docman_group = (int)$actions['docman_group'];
        if(!$docman_group > 0) {
            return false;
        }

        // Get the DOCman group
        $query = "SELECT * FROM `#__docman_groups` WHERE `groups_id`=".(int)$docman_group;
        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        if (!empty($row)) {

            // Construct the new members-list
            if (empty($row->groups_members)) {
                $members = $user->id;
            } else {
                $user_ids = explode(',', $row->groups_members);
                $user_ids = array_diff($user_ids, array($user->id));
                $members = implode(',', $user_ids);
            }

            // Update the new members-list within the database
            $members = $this->db->Quote($members);
            $query = "UPDATE `#__docman_groups` SET `groups_members`=".$members." WHERE `groups_id`=".(int)$docman_group;
            $this->db->setQuery($query);
            $this->db->query();
        }

        return true;
    }
}

