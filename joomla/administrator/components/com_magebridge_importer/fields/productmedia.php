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
defined('JPATH_BASE') or die();

// Include the parent
if(YireoHelper::isJoomla25()) {
    require_once JPATH_LIBRARIES.'/joomla/form/fields/media.php';
} else {
    require_once JPATH_LIBRARIES.'/cms/form/field/media.php';
}

// Joomla! 2.5 and Joomla! 3 use different setup() arguments, so supress errors here
$display_errors = ini_get('display_errors');
ini_set('display_errors', '0');

/*
 * Form Field-class to extend behaviour of media-field
 */
class JFormFieldProductmedia extends JFormFieldMedia
{
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $rt = parent::setup($element, $value, $group);
        if ($rt == true) {
            $app = JFactory::getApplication();
            $user = JFactory::getUser();
            if($app->isSite()) {
                $directory = 'import/user'.(int)$user->id;
                @mkdir(JPATH_SITE.'/images/import');
                @mkdir(JPATH_SITE.'/images/'.$directory);
                $this->directory = $directory;
            }
        }
        return $rt;
    }
}

// Re-activate the old display_errors
ini_set('display_errors', $display_errors);

