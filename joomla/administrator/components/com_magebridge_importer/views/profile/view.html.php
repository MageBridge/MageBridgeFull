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

// Import the needed libraries
jimport('joomla.filter.output');

/**
 * HTML View class
 */
class MageBridgeImporterViewProfile extends MageBridgeImporterView
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

        // Fetch this item
        $this->fetchItem();

        // Load the form if it's there
        $form = $this->get('Form');

		parent::display($tpl);
	}
}
