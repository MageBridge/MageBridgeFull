<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * MageBridge Controller
 */
class MageBridgeImporterController extends YireoController
{
    /**
     * Constructor
     * @package MageBridge
     */
    public function __construct()
    {
        $this->_default_view = 'products';

        parent::__construct();
    }

    // Approve all pending entries
    public function approve()
    {
        $this->status('approved');
    }

    // Approve all pending entries
    public function status($status)
    {
        // Security check
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the ID-list
        $cid = $this->getIds();
        if (count( $cid ) < 1) {
            $this->doRedirect('products');
        }

        $db = JFactory::getDBO();
        foreach($cid as $id) {
            $query = 'UPDATE `#__magebridge_importer_products` SET `status`="'.$status.'" WHERE `id`='.(int)$id;
            $db->setQuery($query);
            $db->query();
        }

        // Redirect to this same page
        $this->doRedirect('products');
    }
}
