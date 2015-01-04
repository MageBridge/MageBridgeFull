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
 * MageBridge Product Plugin for OSE MSC v4
 *
 * @package MageBridge
 */
class plgMageBridgeProductOsemsc4 extends MageBridgePluginProduct
{
    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return $this->checkComponent('com_osemsc');
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

        // Check for the msc_id
        if(!isset($actions['msc_id'])) {
            return false;
        }

        // Make sure it is not empty
        $msc_id = (int)$actions['msc_id'];
        $msc_period = (int)$actions['msc_period'];
        $msc_periodtype = $actions['msc_periodtype'];
        if(!$msc_id > 0) {
            return false;
        }

        // Save the membership
        return $this->saveMembership($user->id, $msc_id, $msc_period, $msc_periodtype);
    }

    /**
     * Method to actually save the membership
     *
     * @param int $user_id
     * @param int $msc_id
     * @param int $msc_period
     * @return bool
     */
    private function saveMembership($user_id = 0, $msc_id = 0, $msc_period = 0, $msc_periodtype)
    {
        if (!$user_id > 0 || !$msc_id > 0 || !$msc_period > 0) {
            return false;
        }

        // Get system variables
        $db = JFactory::getDBO();

        // Import the JDate-library
        jimport('joomla.utilities.date');

        // Load an object of the MSC-group
        $query = 'SELECT * FROM `#__osemsc_acl` WHERE `id`="'.$msc_id.'" LIMIT 1';
        $db->setQuery($query);
        $group = $db->loadObject();

        // Load an object of the MSC-membership for this user
        $query = 'SELECT * FROM `#__osemsc_member` '
            . 'WHERE `member_id`="'.$user_id.'" AND `msc_id`="'.$msc_id.'" LIMIT 1';
        $db->setQuery($query);
        $member = $db->loadObject();

        // Initialize the query-segments for building the MySQL query
        $query_segments = array(
            '`msc_id`="'.$msc_id.'"',
            '`member_id`="'.$user_id.'"',
        );

        // New entry
        if (empty($member)) {
            $start_date = new JDate();
            $expired_date = new JDate($this->getTimestampAfterX(time(), $msc_period, $msc_periodtype));

            $query_segments[] = '`start_date`='.$db->Quote($start_date->toMySQL());
            $query_segments[] = '`expired_date`='.$db->Quote($expired_date->toMySQL());
            $query = 'INSERT INTO `#__osemsc_member` SET '.implode(', ', $query_segments);

            // Update the table
            $db->setQuery($query);
            $db->query();

            // Existing entry
        } else {

            $expired_date = new JDate($this->getTimestampAfterX(time(), $msc_period, $msc_periodtype));

            $query_segments[] = '`expired_date`='.$db->Quote($expired_date->toMySQL());
            $query = 'UPDATE `#__osemsc_member` SET '.implode(', ', $query_segments)
                . ' WHERE `member_id`="'.$user_id.'" AND `msc_id`="'.(int)$msc_id.'"';

            // Update the table
            $db->setQuery($query);
            $db->query();
        }
    }

    /*
     * Calculate the timestamp after X months from $timestamp
     *
     * @param int $date
     * @param int $months
     * @return int
     */
    private function getTimestampAfterX($timestamp = null, $number = 0, $type = 'month')
    {
        switch($type) {
            case 'day':
                $timestamp = $timestamp + (mktime(date('H'), date('i'), date('s'), date('m'), date('d') + $number, date('Y'))) - time();
                break;
            case 'week':
                $timestamp = $timestamp + (mktime(date('H'), date('i'), date('s'), date('m'), date('d') + ($number * 7), date('Y'))) - time();
                break;
            case 'month':
                $timestamp = $timestamp + (mktime(date('H'), date('i'), date('s'), date('m') + $number, date('d'), date('Y'))) - time();
                break;
            case 'year':
                $timestamp = $timestamp + (mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y') + $number)) - time();
                break;
        }
        return $timestamp;
    }
}

