<?php
/**
 * Joomla! MageBridge plugin for Joomla! profiles
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Profile Plugin for Joomla! Profiles
 */
class plgMageBridgeProfileJoomla extends MageBridgePluginProfile
{
    /*
     * Short name of this plugin
     */
    protected $pluginName = 'joomla';

    /**
     * Event "onMageBridgeProfileSave"
     * 
     * @access private
     * @param null
     * @return int
     */
    public function onMageBridgeProfileSave($user = null, $customer = null, $address = null)
    {
        // Preliminary checks
        if ($user == null || $customer == null) {
            return false;
        }

        // Get system variables
        $db = JFactory::getDBO();

        // Fetch the current profile-fields
        $db->setQuery('SELECT * FROM #__user_profiles WHERE `user_id`='.(int)$user->id);
        $rows = $db->loadObjectList();
        if (!empty($rows)) {
            $current_fields = array();
            foreach ($rows as $row) {
                $current_fields[$row->profile_key] = $row->profile_value;
            }
        }

        // Convert the customer values
        $query_segments = array();
        foreach ($customer as $name => $value) {
            $newname = $this->convertField($name, self::CONVERT_TO_JOOMLA);
            if (preg_match('/^profile\./', $newname)) {

                // Build the query
                if (isset($current_fields[$newname])) {
                    $query = 'UPDATE `#__user_profiles` SET `profile_value`='.$db->Quote($value)
                        . ' WHERE `user_id`='.(int)$user->id.' AND `profile_key`='.$db->Quote($newname);
                } else {
                    $query = 'INSERT INTO `#__user_profiles` SET `profile_value`='.$db->Quote($value)
                        . ', `user_id`='.(int)$user->id.', `profile_key`='.$db->Quote($newname);
                }

                // Update this profiler-field
                $db->setQuery($query);
                $db->query();
            }
        }

        return true;
    }

    /*
     * Method to modify the user array
     *
     * @param int $user_id
     * @param array $user
     * @return array
     */
    public function onMageBridgeProfileModifyFields($user_id = 0, $user = array())
    {
        // Get the custom Joomla! profile-fields
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__user_profiles WHERE `user_id`=".$user_id;
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        if (empty($fields)) return $user;

        // Parse the custom fields to add them to the Magento field-list
        foreach ($fields as $field) {
            $name = $this->convertField($field->profile_key, self::CONVERT_TO_MAGENTO);
            if (!empty($name)) $user[$name] = $field->profile_value;
        }

        // Remove the profile-data
        unset($user['profile']);

        return $user;
    }
}
