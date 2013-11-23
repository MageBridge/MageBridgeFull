<?php
/**
 * Joomla! MageBridge plugin for Graphdat
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Import the parent class
jimport( 'joomla.plugin.plugin' );
        
/**
 * MageBridge Graphdat Plugin
 */
class plgMagebridgeMagebridge extends JPlugin
{
    /**
     * Wrapper function for graphdat_start
     * 
     * @access private
     * @param string $name
     * @return null
     */
    private function graphdatStart($timer = null)
    {
        if(function_exists('graphdat_start')) {
            graphdat_start($timer);
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
        $this->graphdatStart('build MageBridge');
    }

    /**
     * Event onAfterBuildMageBridge
     *
     * @access public
     * @param null
     * @return null
     */
    public function onAfterBuildMageBridge()
    {
        $this->graphdatEnd('build MageBridge');
    }
}
