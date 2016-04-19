<?php
/**
 * Joomla! MageBridge plugin for Graphdat
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2016
 * @license GNU Public License
 * @link https://www.yireo.com
 *
 * 1) Install Graphdat Agent (see graphdat.com)
 * 2) Install Graphdat PHP extension (pecl install graphdat)
 * 3) Enable this MageBridge Graphdat plugin
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );
		
/**
 * MageBridge Graphdat Plugin
 */
class plgMagebridgeGraphdat extends JPlugin
{
	/**
	 * Wrapper function for graphdat_begin
	 * 
	 * @access private
	 * @param string $name
	 * @return null
	 */
	private function graphdatBegin($timer = null)
	{
		if(function_exists('graphdat_begin')) {
			graphdat_begin($timer);
		}
	}

	/**
	 * Wrapper function for graphdat_end
	 * 
	 * @access private
	 * @param string $name
	 * @return null
	 */
	private function graphdatEnd($timer = null)
	{
		if(function_exists('graphdat_end')) {
			graphdat_end($timer);
		}
	}

	/**
	 * Event onBeforeBuildMageBridge
	 *
	 * @access public
	 * @param null
	 * @return null
	 */
	public function onBeforeBuildMageBridge()
	{
		$this->graphdatBegin('onBeforeBuildMageBridge');
	}
}
