<?php
/*
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2014
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

jimport('cms.form.field.media');

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
