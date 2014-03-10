<?php
/**
 * Joomla! MageBridge plugin for CB profiles
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the MageBridge autoloader
require_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Profile Plugin for CB Profiles
 */
class plgMageBridgeProfileCb extends MageBridgePluginProfile
{
    /*
     * Short name of this plugin
     */
    protected $pluginName = 'cb';

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

        // Convert the customer values
        $query_segments = array();
        foreach ($customer as $name => $value) {
            $newname = $this->convertField($name, self::CONVERT_TO_JOOMLA);
            $query_segments[] = "`$newname`=".$db->Quote($value);
        }

        // Determine whether the user already exists
        $db->setQuery('SELECT COUNT(*) AS count FROM #__comprofiler WHERE `user_id`='.(int)$user->id);
        $row = $db->loadObject();
        $exists = (boolean)$row->count;

        // Build the query
        $query = ($exists) ? 'UPDATE #__comprofiler ' : 'INSERT INTO #__comprofiler ' ;
        $query .= 'SET '.implode($query_segments);
        $query .= ($exists) ? ' WHERE `user_id`='.(int)$user->id : '' ;
        $db->setQuery($query);
        $db->query();

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
        // Get the custom CB fields
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__comprofiler WHERE `user_id`=".(int)$user_id;
        $db->setQuery($query);
        $cbuser = $db->loadObject();
        if (empty($cbuser)) return $user;

        // Parse the custom fields to add them to the Magento field-list
        foreach (get_object_vars($cbuser) as $name => $value) {
            $name = $this->convertField($name, self::CONVERT_TO_MAGENTO);
            if (!empty($name)) $user[$name] = $value;
        }

        return $user;
    }

    /*
     * Method to check whether this connector is enabled or not
     * 
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        jimport('joomla.application.component.helper');
        if (is_dir(JPATH_ADMINISTRATOR.'/components/com_comprofiler')
            && JComponentHelper::isEnabled('com_comprofiler') == true) {
            return true;
        } else {
            return false;
        }
    }
}
