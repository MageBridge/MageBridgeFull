<?php
/**
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
 * MageBridge View Helper
 */
class MageBridgeViewHelper
{
    /*
     * Helper-method to initialize YireoCommonView-based views
     *
     * @param string $name
     * @return mixed
     */
    static public function initialize($title)
    {
        // Load important variables
        $document = JFactory::getDocument();
        $view = JRequest::getCmd('view');

        // Add CSS-code
        $document->addStyleSheet(JURI::root().'media/com_magebridge/css/backend.css');
        $document->addStyleSheet(JURI::root().'media/com_magebridge/css/backend-view-'.$view.'.css');

        if (MageBridgeHelper::isJoomla25()) $document->addStyleSheet(JURI::root().'media/com_magebridge/css/backend-j25.css');
        if (MageBridgeHelper::isJoomla35()) $document->addStyleSheet(JURI::root().'media/com_magebridge/css/backend-j35.css');

        // Page title
        JToolBarHelper::title('MageBridge: '.$title, 'logo.png');

        // Add the menu
        self::addMenuItems(); // @todo: Integrate this with the abstract-helper
    }

    /*
     * Helper-method to add all the submenu-items for this component
     *
     * @param null
     * @return null
     */
    static protected function addMenuItems()
    {
        if (MageBridgeHelper::isJoomla15()) {
            if(in_array(JRequest::getCmd('view'), array('home','check','update'))) {
                return;
            }
        }

		$menu = JToolBar::getInstance('submenu');
        if(method_exists($menu, 'getItems')) {
            $currentItems = $menu->getItems();
        } else {
            $currentItems = array();
        }

        $items = array(
            'Home' => 'home',
            'Configuration' => 'config',
            'Store Relations' => 'stores',
            'Product Relations' => 'products',
            'Usergroup Relations' => 'usergroups',
            'Connectors' => 'connectors',
            'URL Replacements' => 'urls',
            'Users' => 'users',
            'System Check' => 'check',
            'Logs' => 'logs',
            'Update' => 'update',
        );
			
        foreach ($items as $title => $view) {

            // @todo: Integrate this with the abstract-helper

            // Skip this view, if it does not exist on the filesystem
            if (!is_dir(JPATH_COMPONENT.'/views/'.$view)) continue;

            // Skip this view, if ACLs prevent access to it
            if (MageBridgeAclHelper::isAuthorized($view, false) == false) continue;

            // Add the view
            $active = (JRequest::getCmd('view') == $view) ? true : false;
            $url = 'index.php?option=com_magebridge&view='.$view;
            $title = JText::_($title);

            $alreadySet = false;
            foreach($currentItems as $currentItem) {
                if($currentItem[1] == $url) {
                    $alreadySet = true;
                    break;
                }
            }

            if($alreadySet == false) {
        		$menu->appendButton($title, $url, $active);
            }
        }
        return;
    }
}
