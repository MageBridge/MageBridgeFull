<?php
/**
 * Joomla! MageBridge - Lightbox System plugin
 *
 * @author Yireo (info@yireo.com)
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
 * MageBridge Lightbox System Plugin
 */
class plgSystemMageBridgeLightbox extends JPlugin
{
	/**
	 * Event onAfterRender
	 *
	 * @access public
	 * @param null
	 * @return null
	 */
	public function onAfterDispatch()
	{
		// Dot not load if this is not the right document-class
		$document = JFactory::getDocument();
		if(stristr(get_class($document), 'html') == false) {
			return false;
		}

		// Only do this on the frontend
		$application = JFactory::getApplication();
		if($application->isSite() == false) {
			return false;
		}

		if (JRequest::getCmd('option') == 'com_magebridge') {

			/**$body = JResponse::getBody();
			if (!empty($body)) {
				JResponse::setBody($body);
			}*/
			$library = $this->params->get('library');
			switch($library) {
				case 'lightbox2':
					$this->jquery();
					$this->addJs('lightbox2/js/lightbox.js');
					$this->addCss('lightbox2/css/lightbox.css');
					break;
				case 'lightview':
					$this->jquery();
					$this->addJs('lightview/js/spinners/spinners.min.js');
					$this->addJs('lightview/js/lightview/lightview.js');
					$this->addCss('lightview/css/lightview/lightview.css');
					break;
				case 'easybox':
					$this->jquery();
					$this->addJs('easybox/easybox.min.js');
					$this->addCss('easybox/easybox.min.css');
				case 'prettyphoto':
					$this->jquery();
					$this->addJs('prettyphoto/js/jquery.prettyPhoto.js');
					$this->addScriptDeclaration('jQuery(document).ready(function(){jQuery("a[rel^=\'lightbox\']").prettyPhoto();});');
					$this->addCss('prettyphoto/css/prettyPhoto.css');
				case 'pirobox':
					$this->jquery();
					$this->addJs('pirobox/js/jquery-ui-1.8.2.custom.min.js');
					$this->addJs('pirobox/js/pirobox_extended.js');
					$this->addScriptDeclaration('jQuery(document).ready(function(){jQuery().piroBox_ext({piro_speed : 900,bg_alpha : 0.1,piro_scroll:true});});');
					$this->addCss('pirobox/css_pirobox/style_2/style.css'); // @todo: Extra parameter for style_2
			}
		}
	}

	/**
	 * Helper method to load jQuery
	 */
	protected function jquery()
	{
		if($this->params->get('load_jquery', 1) == 0) return false;
		MageBridgeTemplateHelper::load('jquery');
	}

	/**
	 * Helper method to add a CSS-stylesheet
	 */
	protected function addCss($css)
	{
		$css = JURI::root().'media/plg_magebridgelightbox/'.$css;
		$document = JFactory::getDocument();
		$document->addStylesheet($css);
	}

	/**
	 * Helper method to add a JavaScript-file
	 */
	protected function addJs($js, $prototype = false)
	{
		$js = JURI::root().'media/plg_magebridgelightbox/'.$js;

		// Add the script to this document
		if($prototype == false) {
			$document = JFactory::getDocument();
			$document->addScript($js);
		} else {
			$html = '<script type="text/javascript" src="'.$js.'"></script>';
			$document = JFactory::getDocument();
			$document->addCustomTag($html);
		}

		// Add the script to the whitelist so MageBridge doesn't strip it afterwards
		$config = JFactory::getConfig();
		$whitelist = $config->get('magebridge.script.whitelist');
		if (empty($whitelist)) $whitelist = array();
		$whitelist[] = $js;
		$config->set('magebridge.script.whitelist', $whitelist);
	}

	/**
	 * Helper method to add a piece of JavaScript
	 */
	protected function addScriptDeclaration($js)
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
	}
}
