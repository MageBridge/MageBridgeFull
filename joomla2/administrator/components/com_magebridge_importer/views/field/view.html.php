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

// Import the needed libraries
jimport('joomla.filter.output');

/**
 * HTML View class
 */
class MageBridgeImporterViewField extends MageBridgeImporterView
{
    /*
     * Flag to determine whether to load the menu
     */
    protected $loadToolbar = false;
    
    /*
     * Display method
     *
     * @param string $tpl
     * @return null
     */
	public function display($tpl = null)
	{
        // Load toolbar
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();

        // Before loading anything, we build the bridge
        $this->preBuildBridge();

        // Fetch this item
        $this->fetchItem();

		parent::display($tpl);
	}
}
