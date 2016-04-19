<?php
/**
 * Joomla! module MageBridge: Slider block
 *
 * @author	Yireo (info@yireo.com)
 * @package   MageBridge
 * @copyright Copyright 2016
 * @license   GNU Public License
 * @link	  https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Read the parameters
$tool = $params->get('tool');
$layout = 'default';

// Call the helper
require_once(dirname(__FILE__) . '/helper.php');
$products = modMageBridgeSliderHelper::build($params);

// Add global CSS and JavaScript 
$templateHelper = new MageBridgeTemplateHelper();

if ($params->get('load_jquery', 1) == 1)
{
	$templateHelper->load('jquery');
}

if ($params->get('load_jquery_easing', 1) == 1)
{
	$templateHelper->load('jquery-easing');
}

if ($params->get('load_jquery_cycle', 1) == 1)
{
	$templateHelper->load('js', 'jquery/jquery.cycle.all.min.js');
}

// Add module-based CSS and JavaScript 
$helper = new ModMageBridgeSliderHelper();
switch ($tool)
{
	case 'bootstrap2':
		$layout = 'bootstrap2';
		break;

	case 'bootstrap3':
		$layout = 'bootstrap3';
		break;

	case 'awkward':
		$helper->addStylesheet('awkward/css/awkward.css');
		$helper->addScript('awkward/js/jquery.aw-showcase.min.js');
		$layout = 'awkward';
		break;

	case 'slidesjs':
		$helper->addStylesheet('slidesjs/css/slidesjs.css');
		$helper->addScript('slidesjs/js/slides.min.jquery.js');
		$layout = 'slidesjs';
		break;

	case 'jcoverflip':
		$helper->addStylesheet('jcoverflip/css/jcoverflip.css');
		$helper->addScript('jcoverflip/js/jquery-ui-1.7.2.custom.js');
		$helper->addScript('jcoverflip/js/jquery.jcoverflip.js');
		$layout = 'jcoverflip';
		break;

	case 'slidorion':
		$helper->addStylesheet('slidorion/css/slidorion.css');
		$helper->addStylesheet('slidorion/css/magebridge.css');
		$helper->addScript('slidorion/js/jquery.slidorion.min.js');
		$layout = 'slidorion';
		break;
}

// Include the layout-file
require(JModuleHelper::getLayoutPath('mod_magebridge_slider', $layout));
