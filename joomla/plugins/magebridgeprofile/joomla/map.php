<?php
/**
 * Joomla! component MageBridge
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Joomla! fields conversion (Joomla! -> Magento)
$conversion = array(
	'profile.address1' => 'address_street',
	'profile.postal_code' => 'address_postcode',
	'profile.city' => 'address_city',
	'profile.region' => 'address_region',
	'profile.country' => 'address_country',
	'profile.phone' => 'address_telephone',
);

