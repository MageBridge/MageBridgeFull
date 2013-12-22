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
 * MageBridge Product Plugin for jDownloads
 *
 * @package MageBridge
 */
class plgMageBridgeProductJdownloads extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'jdownloads_group';

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_jdownloads');
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
        if(!isset($actions['jdownloads_group'])) {
            return false;
        }

        // Make sure it is not empty
        $jdownloads_group = (int)$actions['jdownloads_group'];
        if(!$jdownloads_group > 0) {
            return false;
        }

        // Get the group
        $query = "SELECT * FROM `#__jdownloads_groups` WHERE `id`=".(int)$jdownloads_group;
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
            $query = "UPDATE `#__jdownloads_groups` SET `groups_members`=".$members." WHERE `id`=".(int)$jdownloads_group;
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
        if(!isset($actions['jdownloads_group'])) {
            return false;
        }

        // Make sure it is not empty
        $jdownloads_group = (int)$actions['jdownloads_group'];
        if(!$jdownloads_group > 0) {
            return false;
        }

        $query = "SELECT * FROM `#__jdownloads_groups` WHERE `id`=".(int)$jdownloads_group;
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
            $query = "UPDATE `#__jdownloads_groups` SET `groups_members`=".$members." WHERE `id`=".(int)$jdownloads_group;
            $this->db->setQuery($query);
            $this->db->query();
        }

        return true;
    }
}

