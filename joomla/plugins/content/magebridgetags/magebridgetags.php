<?php
/**
 * Joomla! MageBridge Tags Content plugin
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );

// Import the MageBridge autoloader
include_once JPATH_SITE.'/components/com_magebridge/helpers/loader.php';

/**
 * MageBridge Tags Content Plugin
 */
class plgContentMageBridgeTags extends JPlugin
{
    /**
     * Handle the event that is generated when an user tries to login
     * 
     * @access public
     * @param string $context
     * @param object $row
     * @param string $params
     * @param mixed $page
     * @return null
     */
    public function onContentPrepare($context, $row, $params, $page)
    {
        // Do not continue if not enabled
        if ($this->isEnabled() == false) {
            return false;
        }

        // Get system variables
        $bridge = MageBridgeModelBridge::getInstance();

	    // Load plugin paramaters
	    $plugin = JPluginHelper::getPlugin('content', 'magebridgetags');
    	$pluginParams = YireoHelper::toRegistry($plugin->params);
        $max = $pluginParams->get('max_products', 10);

        // Find related data
        $tags = $this->getTags($row);

        // If there are no tags, don't do anything
        if (empty($tags)) {
            return false;
        }

        // Build the bridge
        $segment_id = $bridge->register('api', 'magebridge_tag.list', $tags);
        $bridge->build();
        $products = $bridge->getSegmentData($segment_id);

        // Do not continue if the result is empty
        if (empty($products)) {
            return false;
        }

        // Do not continue if the result is not an array
        if (!is_array($products)) {
            MageBridgeModelDebug::getInstance()->error( "Fetching tags resulted in non-array: ".var_export($products, true));
            return false;
        }

        // Only show the needed amount of tags
        $products = array_slice( $products, 0, $max );

        // Load the template script (and allow for overrides)
        jimport('joomla.filesystem.path');
        $template_dir = JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate();

        // Determine the right folder
        if (is_dir(dirname(__FILE__).'/tmpl')) {
            $tmplDir = 'tmpl';
        } else {
            $tmplDir = 'magebridgetags';
        }

        // Load the layout file
        $layout = $template_dir.'/html/plg_magebridgetags/default.php';
        if (!is_file($layout) || !is_readable($layout)) {
            $layout = dirname(__FILE__).'/'.$tmplDir.'/default.php';
        }

        // Prepare the variables
        $tagstring = implode(', ', $tags);

        // Read the template
        ob_start();
        include $layout;
        $output = ob_get_contents();
        ob_end_clean();

        // Append the result to the content
        $row->text .= MageBridgeModelBridgeBlock::filterHtml($output);
        return true;
    }

    /*
     * Method to get the tags from a specific resource
     *
     * @access private
     * @param object $content
     * @return array
     */
    private function getTags($content = null)
    {
        // Initialize the tags
        $tags = array();

        // Read the parameters
	    $plugin = JPluginHelper::getPlugin('content', 'magebridgetags');
    	$pluginParams = YireoHelper::toRegistry($plugin->params);
        $source = $pluginParams->get('tag_source');

        switch($source) {

            case 'joomlatags':
                if (empty($content->id)) break;
                $query = "SELECT t.name FROM `#__tag_term_content` AS c "
                    . " LEFT JOIN `#__tag_term` AS t ON t.id = c.tid "
                    . " WHERE cid = ".(int)$content->id;
                $db = JFactory::getDBO();
                $db->setQuery($query);
                $tags = (method_exists($db, 'loadColumn')) ? $db->loadColumn() : $db->loadResultArray(); 
                break;

            case 'core':
                if (empty($content->id)) break;
                $query = "SELECT t.title FROM `#__tags` AS t "
                    . " LEFT JOIN `#__contentitem_tag_map` AS m ON t.id = m.tag_id "
                    . " WHERE m.type_alias = 'com_content.article' AND m.content_item_id = ".(int)$content->id;
                $db = JFactory::getDBO();
                $db->setQuery($query);
                $tags = (method_exists($db, 'loadColumn')) ? $db->loadColumn() : $db->loadResultArray(); 
                break;

            case 'metakey':
            default:
                if (!empty($content->metakey)) {
                    $tags = explode(',', $content->metakey);
                    if (!empty($tags)) {
                        foreach ($tags as $index => $tag) {
                            $tags[$index] = trim($tag);
                        }
                    }
                }
                break;
        }

        return $tags;
    }

    /**
     * Joomla! 1.5 alias
     * 
     * @access public
     * @param object $article
     * @param JParameter $params
     * @param mixed $limitstart
     * @return null
     */
    public function onPrepareContent(&$article, &$params, $limitstart)
    {
        $this->onContentPrepare('content', $article, $params, $limitstart);
    }

    /**
     * Return whether MageBridge is available or not
     * 
     * @access private
     * @param null
     * @return mixed $value
     */
    private function isEnabled()
    {
        if (class_exists('MageBridgeModelBridge')) {
            if (MageBridgeModelBridge::getInstance()->isOffline() == false) {
                return true;
            }
        }
        return false;
    }
}
