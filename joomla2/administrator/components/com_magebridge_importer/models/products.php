<?php
/*
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!  
defined('_JEXEC') or die();

/*
 * MageBridge Importer Products model
 */
class MagebridgeImporterModelProducts extends YireoModel
{
    /**
     * Constructor method
     *
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        $this->_search = array('name');

        parent::__construct('product');

        if ($this->application->isSite()) {
            $user = JFactory::getUser();
            $this->addWhere('`created_by`='.(int)$user->id);
        }
    }

    /**
     * Method to load additional data into the model
     *
     * @access public
     * @return bool
     */
    public function onDataLoad($data)
    {
        if(!empty($data) && !empty($data->id)) {
            $query = 'SELECT `name`,`value` FROM `#__magebridge_importer_product_values` WHERE `product_id`='.(int)$data->id;
            $this->_db->setQuery($query);
            $rows = $this->_db->loadObjectList();
            if(!empty($rows)) {
                foreach($rows as $row) {
                    $name = $row->name;
                    if($name == 'status') continue;
                    $data->$name = $row->value;
                }
            }
        }
        return $data;
    }
}
