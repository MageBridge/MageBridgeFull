<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (http://www.yireo.com/)
 * @copyright Copyright Yireo 2012
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// Check to ensure this file is included in Joomla!  
defined('_JEXEC') or die();

// Require the parent view
jimport( 'joomla.application.component.view');

/**
 * HTML View class 
 *
 * @static
 * @package MageBridge
 */
class MageBridgeView extends MagebridgeViewAbstract
{
    public function display($tpl = null)
    {
        //JToolBarHelper::help( 'screen.magebridge.usage' );
        $this->addMenuItems();
        JHTML::stylesheet('style.css', '/media/com_magebridge/css/');
        parent::display($tpl);
    }

    public function setTitle($title)
    {
        JToolBarHelper::title( JText::_('MageBridge') . ': ' . JText::_( $title ), 'yireo' );
    }

    public function addMenuItems()
    {
        $items = array(
            'Home' => 'home',
            'Settings' => 'config',
            'System Check' => 'check',
            'Logs' => 'logs',
            'Update' => 'update',
            'Magento Admin' => 'magento',
        );

        if(!in_array(JRequest::getCmd('view'), $items)) {
            JRequest::setVar('view', 'home');
        }
        foreach($items as $title => $view) {
            if(JRequest::getCmd('view') == $view) {
                $active = true;
            } else {
                $active = false;
            }

            $url = 'index.php?option=com_magebridge&view='.$view;
            JSubMenuHelper::addEntry( JText::_($title), $url, $active );
        }
    }
}
