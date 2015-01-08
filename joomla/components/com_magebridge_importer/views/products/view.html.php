<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class
 */
class MageBridgeImporterViewProducts extends YireoView
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
                $item->edit_link = 'index.php?option=com_magebridge_importer&view=product&id='.$item->id;
                $this->items[$index] = $item;
            }
        }

        // Load the component parameters and set the title
        $params = JFactory::getApplication()->getParams();
        $title = $params->get('page_heading');
        $this->assignRef('title', $title);

		parent::display($tpl);
	}
}
