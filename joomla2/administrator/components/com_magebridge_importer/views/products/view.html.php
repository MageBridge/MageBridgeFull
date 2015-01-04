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
class MageBridgeImporterViewProducts extends YireoViewList
{
    /*
     * Method to prepare the content for display
     */
	public function display($tpl = null)
	{
        // Load toolbar
        JToolBarHelper::custom('approve', 'copy.png', 'copy.png', 'LIB_YIREO_VIEW_TOOLBAR_APPROVE', false, true);

		parent::display($tpl);
	}
}
