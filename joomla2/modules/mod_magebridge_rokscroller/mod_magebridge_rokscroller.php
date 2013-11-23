<?php
/**
* @package RokLatestNews
* @copyright Copyright (C) 2007 RocketWerx. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$products = modMagebridgeRokScrollerHelper::build($params);
$counter = 0;

require(JModuleHelper::getLayoutPath('mod_magebridge_rokscroller'));
