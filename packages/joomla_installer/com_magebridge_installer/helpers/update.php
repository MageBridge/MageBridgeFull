<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (https://www.yireo.com/)
 * @package MageBridge
 * @copyright Copyright 2012
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include Joomla! libraries
jimport('joomla.filesystem.file');

// Include MageBridge libraries
require_once JPATH_SITE.'/components/com_magebridge/models/proxy.php';

class MageBridgeUpdateHelper
{
    static public function getPackageList()
    {
        return array(
            array( 'type' => 'component', 'name' => 'com_magebridge', 'title' => 'Main component' ),
            array( 'type' => 'module', 'name' => 'mod_magebridge_block', 'title' => 'Block module' ),
            array( 'type' => 'module', 'name' => 'mod_magebridge_cart', 'title' => 'Cart module' ),
            array( 'type' => 'module', 'name' => 'mod_magebridge_menu', 'title' => 'Menu module' ),
            array( 'type' => 'module', 'name' => 'mod_magebridge_products', 'title' => 'Products module' ),
            array( 'type' => 'module', 'name' => 'mod_magebridge_login', 'title' => 'Login module' ),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_authentication', 'title' => 'Authentication plugin', 'group' => 'authentication', 'file' => 'magebridge' ),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_content', 'title' => 'Content plugin', 'group' => 'content', 'file' => 'magebridge'),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_magento', 'title' => 'Magento plugin', 'group' => 'magento', 'file' => 'magebridge' ),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_magebridge', 'title' => 'MageBridge plugin', 'group' => 'magebridge', 'file' => 'magebridge' ),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_search', 'title' => 'Search plugin', 'group' => 'search', 'file' => 'magebridge'),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_system', 'title' => 'System plugin', 'group' => 'system', 'file' => 'magebridge' ),
            array( 'type' => 'plugin', 'name' => 'plg_magebridge_user', 'title' => 'User plugin', 'group' => 'user', 'file' => 'magebridge' ),
        );
    }

    static public function downloadPackage($url, $target = false)
    {
        $app = JFactory::getApplication();

        // Open the remote server socket for reading
        $proxy = MageBridgeModelProxy::getInstance();
        $data = $proxy->getRemote( $url, null, 'get', false );
        if (empty($data)) {
            JError::raiseWarning(42, JText::_('REMOTE_DOWNLOAD_FAILED').', '.$error);
            return false;
        }

        // Set the target path if not given
        if (!$target) {
            $target = $app->getCfg('tmp_path').'/'.JInstallerHelper::getFilenameFromURL($url);
        } else {
            $target = $app->getCfg('tmp_path').'/'.basename($target);
        }

        // Write received data to file
        JFile::write($target, $data);

        // Return the name of the downloaded package
        return basename($target);
    }
}
