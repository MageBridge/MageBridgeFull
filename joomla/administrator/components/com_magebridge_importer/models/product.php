<?php
/*
 * Joomla! component MageBridge Importer
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge Importer
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Import Joomla! libraries
jimport('joomla.utilities.date');

/*
 * MageBridge Importer Product model
 */
class MagebridgeImporterModelProduct extends YireoModel
{
    /**
     * Indicator if this is a model for multiple or single entries
     */
    protected $_single = true;

    /*
     * Boolean to allow forms in the frontend
     *      
     * @protected int
     */
    protected $_frontend_form = true;

    /**
     * Constructor method
     *
     * @package MageBridge
     * @access public
     * @param null
     * @return null
     */
    public function __construct()
    {
        parent::__construct('product');
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
                    $value = $row->value;
                    if(!empty($value)) {
                        $tmpValue = @unserialize($value);
                        if(!empty($tmpValue)) $value = $tmpValue;
                    }

                    $data->$name = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Method to store the model
     *
     * @access public
     * @param mixed $data
     * @return bool
     */
    public function store($data)
    {
        if(!isset($data['item']['product_type'])) {
            $data['item']['product_type'] = 'simple';
        }

        if(!isset($data['item']['status'])) {
            $data['item']['status'] = 'pending';
        }

        if($this->_id > 0 == false) {
            $data['item']['created'] = date('Y-m-d H:i:s');
            $data['item']['created_by'] = JFactory::getUser()->id;
        }
        $data['item']['modified'] = date('Y-m-d H:i:s');
        $data['item']['modified_by'] = JFactory::getUser()->id;

        $rt = parent::store($data);

        if($this->_id > 0) {
            $this->_db->setQuery('DELETE FROM `#__magebridge_importer_product_values` WHERE `product_id`='.(int)$this->_id);
            $this->_db->query();
            foreach($data['item'] as $attributeName => $attributeValue) {

                if(!is_string($attributeValue)) {
                    $attributeValue = serialize($attributeValue);
                }

                $query = 'INSERT INTO `#__magebridge_importer_product_values` SET '
                    . '`product_id`='.(int)$this->_id.', '
                    . '`name`='.$this->_db->Quote($attributeName).', '
                    . '`value`='.$this->_db->Quote($attributeValue).', '
                    . '`timestamp`=NOW()'
                ;
                $this->_db->setQuery($query);
                $this->_db->query();
            }
        }

        return $rt;
    }
}
