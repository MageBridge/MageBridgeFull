<?php
/**
 * Joomla! 1.5 extension MageBridge - JCE plugin
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// no direct access
defined('_JCE_EXT') or die('Restricted access');

// Core function	
function magebridgelinks( &$advlink )
{
    jimport('joomla.application.component.helper');
    if(!is_dir(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_magebridge') || JComponentHelper::isEnabled('com_magebridge') == false) {
        return array();
    }

	$path = dirname( __FILE__ ) .DS. 'magebridgelinks';	
	$items = array(
        array(
            'name' => 'magebridge',
            'path' => $path,
            'file' => 'magebridge.php',
        ),
    );

	return $items;
}	
