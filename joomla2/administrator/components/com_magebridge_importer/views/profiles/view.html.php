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
 * HTML View class
 */
class MageBridgeImporterViewProfiles extends YireoViewList
{
    /*
     * Method to prepare the content for display
     */
	public function display($tpl = null)
	{
        // Fetch the items
        $this->fetchItems();

        // Prepare the items for display
        if (!empty($this->items)) {
            foreach ($this->items as $index => $item) {
                $item->custom_edit_link = 'index.php?option=com_magebridge_importer&view=profile&cid[]='.$item->id;
                $this->items[$index] = $item;
            }
        }

		parent::display($tpl);
	}
}
