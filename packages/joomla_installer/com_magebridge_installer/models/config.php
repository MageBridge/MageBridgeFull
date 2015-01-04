<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (https://www.yireo.com/)
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link https://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Require the parent view
jimport( 'joomla.application.component.model');

class MagebridgeModelConfig extends MagebridgeModelAbstract
{
    /**
     * Array of configured data
     *
     * @var array
     */
    private $_data = null;

    /**
     * Method to get data
     */
    public function getData()
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_data))
        {
            $query = 'SELECT * FROM #__magebridge_config AS c WHERE name="supportkey"';
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();

            if(empty($this->_data)) {
                $this->_data = (object)null;
                $this->_data->name = 'supportkey';
                $this->_data->value = null;
            }
        }

        return $this->_data;
    }

    /**
     * Static method to get data
     */
    static public function load($element = null)
    {
        static $config;

        if(empty($config)) {
            $model = new MagebridgeModelConfig();
            $config = $model->getData();
        }
        return $config;
    }

    public function store($data)
    {
        $db = JFactory::getDBO();
        $db->setQuery("SELECT * FROM #__magebridge_config AS c WHERE `name`='supportkey'");

        $result = $db->loadObjectList();
        if(empty($result)) {
            $query = "INSERT INTO #__magebridge_config SET name='supportkey', value=".$db->Quote($data['supportkey']);
        } else {
            $query = "UPDATE #__magebridge_config SET value=".$db->Quote($data['supportkey'])." WHERE name='supportkey'";
        }
        $db->setQuery($query);
        $db->query();

        return true;
    }
}
