<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo
 * @copyright Yireo Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla! 
defined('_JEXEC') or die();

// Require the parent view
jimport( 'joomla.application.component.view');

/**
 * HTML View class
 */
class MageBridgeViewUpdate extends MagebridgeViewAbstract
{
    /*
     * Prepare and display the template
     */
    public function display($tpl = null)
    {
        JSubMenuHelper::addEntry(JText::_('Update'), JRoute::_('index.php?option=com_magebridge'), true);

        require_once JPATH_SITE.'/components/com_magebridge/models/config.php';
        require_once JPATH_COMPONENT.'/models/check.php';

        // Add the configuration
        $config = MagebridgeModelConfig::load();
        $this->assignRef('config', $config);

        // Perform the system checks
        $model = new MagebridgeModelCheck();
        $checks = $model->getChecks();
        $endcheck = $model->getEndCheck();
        $this->assignRef('checks', $checks);
        $this->assignRef('endcheck', $endcheck);

        // Add extra things to the HTML-document
        JHTML::_('behavior.tooltip');
        JHTML::stylesheet('style.css', JURI::root().'/media/com_magebridge/css/');
        JToolBarHelper::title( JText::_('MageBridge') . ': ' . JText::_( 'Installer' ), 'yireo' );

        // Set the toolbar buttons
        JToolBarHelper::apply();
        JToolBarHelper::custom( 'update', 'download.png', 'download_f2.png', 'Install', false );

        if($endcheck == false) {
            JError::raiseWarning('ERROR', JText::_('Your system does not meet the system requirements of MageBridge'));
        }

        parent::display($tpl);
    }
}
